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

                // Use a database transaction with locking to prevent race conditions
                try {
                    $result = \Illuminate\Support\Facades\DB::transaction(function () use ($mechanic, $weekStart, $weekEnd, $servicesCount, $totalLaborCost) {
                        // Lock the mechanic_reports table for this mechanic and week to prevent concurrent modifications
                        $existingReport = MechanicReport::where('mechanic_id', $mechanic->id)
                            ->where('week_start', $weekStart)
                            ->where('week_end', $weekEnd)
                            ->lockForUpdate()
                            ->first();

                        if ($existingReport) {
                            // If report exists, update it
                            $this->info("Found existing report ID: {$existingReport->id}, updating...");

                            $existingReport->services_count = $servicesCount;
                            $existingReport->total_labor_cost = $totalLaborCost;
                            $existingReport->save();

                            return ['type' => 'updated', 'report' => $existingReport];
                        } else {
                            // Double-check that the report doesn't exist (extra safety)
                            $checkAgain = MechanicReport::where('mechanic_id', $mechanic->id)
                                ->where('week_start', $weekStart)
                                ->where('week_end', $weekEnd)
                                ->first();

                            if ($checkAgain) {
                                // If report exists (race condition), update it
                                $this->info("Report was created in the meantime, updating ID: {$checkAgain->id}");

                                $checkAgain->services_count = $servicesCount;
                                $checkAgain->total_labor_cost = $totalLaborCost;
                                $checkAgain->save();

                                return ['type' => 'updated', 'report' => $checkAgain];
                            }

                            // Create new report
                            $report = new MechanicReport();
                            $report->mechanic_id = $mechanic->id;
                            $report->week_start = $weekStart;
                            $report->week_end = $weekEnd;
                            $report->services_count = $servicesCount;
                            $report->total_labor_cost = $totalLaborCost;
                            $report->save();

                            return ['type' => 'created', 'report' => $report];
                        }
                    }, 5); // 5 retries for deadlock

                    if ($result['type'] === 'created') {
                        $reportsCreated++;
                        $this->info("Created new report ID: {$result['report']->id}");
                    } else {
                        $reportsUpdated++;
                        $this->info("Updated existing report ID: {$result['report']->id}");
                    }
                } catch (\Exception $e) {
                    $this->error("Error processing report for mechanic {$mechanic->name}: " . $e->getMessage());
                    Log::error("Error processing report for mechanic {$mechanic->name}: " . $e->getMessage(), [
                        'mechanic_id' => $mechanic->id,
                        'week_start' => $weekStart,
                        'week_end' => $weekEnd,
                        'exception' => $e
                    ]);
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
