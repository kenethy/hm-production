<?php

namespace App\Console\Commands;

use App\Models\Mechanic;
use App\Models\MechanicReport;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class RegenerateMechanicReports extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mechanic:regenerate-reports {--mechanic_id= : ID of specific mechanic to regenerate reports for}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Regenerate all mechanic reports to fix labor cost issues';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting regeneration of mechanic reports...');
        Log::info('Starting regeneration of mechanic reports...');

        $mechanicId = $this->option('mechanic_id');

        if ($mechanicId) {
            $mechanics = Mechanic::where('id', $mechanicId)->get();
            $this->info("Regenerating reports for mechanic #{$mechanicId} only");
        } else {
            $mechanics = Mechanic::all();
            $this->info("Regenerating reports for all mechanics");
        }

        $reportsUpdated = 0;
        $reportsCreated = 0;

        foreach ($mechanics as $mechanic) {
            $this->info("Processing mechanic: {$mechanic->name} (#{$mechanic->id})");

            // Get all services for this mechanic
            $services = $mechanic->services()
                ->whereNotNull('week_start')
                ->whereNotNull('week_end')
                ->get();

            if ($services->isEmpty()) {
                $this->info("No services found for mechanic {$mechanic->name}");
                continue;
            }

            // Group services by week
            $servicesByWeek = [];
            foreach ($services as $service) {
                $weekStart = $service->pivot->week_start;
                $weekEnd = $service->pivot->week_end;

                if (!$weekStart || !$weekEnd) {
                    continue;
                }

                $weekKey = "{$weekStart}_{$weekEnd}";

                if (!isset($servicesByWeek[$weekKey])) {
                    $servicesByWeek[$weekKey] = [
                        'week_start' => $weekStart,
                        'week_end' => $weekEnd,
                        'services' => []
                    ];
                }

                $servicesByWeek[$weekKey]['services'][] = $service;
            }

            // Process each week
            foreach ($servicesByWeek as $weekData) {
                $weekStart = $weekData['week_start'];
                $weekEnd = $weekData['week_end'];

                $this->info("Processing week: {$weekStart} to {$weekEnd}");

                // Calculate services count and total labor cost
                $servicesCount = count($weekData['services']);

                // Calculate total labor cost using the mechanic's method
                $totalLaborCost = $mechanic->calculateWeeklyLaborCost($weekStart, $weekEnd);

                $this->info("Services count: {$servicesCount}, Total labor cost: {$totalLaborCost}");

                // Try to find an existing report first
                $existingReport = MechanicReport::where('mechanic_id', $mechanic->id)
                    ->where('week_start', $weekStart)
                    ->where('week_end', $weekEnd)
                    ->first();

                if ($existingReport) {
                    // If report exists, update it
                    $existingReport->services_count = $servicesCount;
                    $existingReport->total_labor_cost = $totalLaborCost;
                    $existingReport->save();

                    $reportsUpdated++;
                    $this->info("Updated existing report ID: {$existingReport->id}");
                } else {
                    // If no report exists, try to create it with try-catch to handle race conditions
                    try {
                        $report = MechanicReport::create([
                            'mechanic_id' => $mechanic->id,
                            'week_start' => $weekStart,
                            'week_end' => $weekEnd,
                            'services_count' => $servicesCount,
                            'total_labor_cost' => $totalLaborCost,
                        ]);

                        $reportsCreated++;
                        $this->info("Created new report ID: {$report->id}");
                    } catch (\Illuminate\Database\QueryException $e) {
                        // Check if it's a duplicate entry error
                        if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
                            // If it's a duplicate entry error, find the report that was created in the meantime
                            $this->info("Duplicate entry detected, fetching existing report");

                            $report = MechanicReport::where('mechanic_id', $mechanic->id)
                                ->where('week_start', $weekStart)
                                ->where('week_end', $weekEnd)
                                ->first();

                            if ($report) {
                                // Update the existing report
                                $report->services_count = $servicesCount;
                                $report->total_labor_cost = $totalLaborCost;
                                $report->save();

                                $reportsUpdated++;
                                $this->info("Updated existing report after duplicate entry ID: {$report->id}");
                            } else {
                                // This should not happen, but just in case
                                $this->error("Could not find existing report after duplicate entry error");
                                Log::error("Could not find existing report after duplicate entry error");
                            }
                        } else {
                            // If it's not a duplicate entry error, rethrow it
                            $this->error("Error creating report: " . $e->getMessage());
                            Log::error("Error creating report: " . $e->getMessage());
                            throw $e;
                        }
                    }
                }
            }
        }

        $this->info("Regeneration complete!");
        $this->info("Reports created: {$reportsCreated}");
        $this->info("Reports updated: {$reportsUpdated}");

        Log::info("Mechanic reports regeneration complete. Created: {$reportsCreated}, Updated: {$reportsUpdated}");

        return Command::SUCCESS;
    }
}
