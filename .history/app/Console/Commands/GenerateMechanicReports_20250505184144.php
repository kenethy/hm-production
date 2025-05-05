<?php

namespace App\Console\Commands;

use App\Models\Mechanic;
use App\Models\Service;
use Carbon\Carbon;
use Illuminate\Console\Command;

class GenerateMechanicReports extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:generate-mechanic-reports {--week= : The week to generate reports for (YYYY-MM-DD format)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate weekly reports for mechanics';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            // Get the week from the option or use the current week
            $weekDate = $this->option('week') ? Carbon::parse($this->option('week')) : Carbon::now();

            // Get the start and end of the week (Monday to Sunday)
            $weekStart = $weekDate->copy()->startOfWeek();
            $weekEnd = $weekDate->copy()->endOfWeek();

            $this->info("Generating mechanic reports for week: {$weekStart->format('Y-m-d')} to {$weekEnd->format('Y-m-d')}");

            // Get all active mechanics
            $mechanics = Mechanic::where('is_active', true)->get();
            $this->info("Found {$mechanics->count()} active mechanics");

            $bar = $this->output->createProgressBar($mechanics->count());
            $bar->start();

            $reportsGenerated = 0;
            $reportsUpdated = 0;
            $reportsSkipped = 0;

            foreach ($mechanics as $mechanic) {
                try {
                    // Check if mechanic has any services in this week
                    $servicesCount = $mechanic->countWeeklyServices($weekStart, $weekEnd);
                    $totalLaborCost = $mechanic->calculateWeeklyLaborCost($weekStart, $weekEnd);

                    // Check if report already exists
                    $existingReport = $mechanic->reports()
                        ->where('week_start', $weekStart)
                        ->where('week_end', $weekEnd)
                        ->first();

                    if ($existingReport) {
                        // Update existing report
                        $existingReport->update([
                            'services_count' => $servicesCount,
                            'total_labor_cost' => $totalLaborCost,
                        ]);
                        $reportsUpdated++;
                    } else if ($servicesCount > 0 || $totalLaborCost > 0) {
                        // Create new report only if there are services or labor costs
                        $mechanic->reports()->create([
                            'week_start' => $weekStart,
                            'week_end' => $weekEnd,
                            'services_count' => $servicesCount,
                            'total_labor_cost' => $totalLaborCost,
                        ]);
                        $reportsGenerated++;
                    } else {
                        // Skip creating report if no services or labor costs
                        $reportsSkipped++;
                    }
                } catch (\Exception $e) {
                    $this->error("Error processing mechanic {$mechanic->name}: {$e->getMessage()}");
                }

                $bar->advance();
            }

            $bar->finish();
            $this->newLine();

            $this->info("Generated {$reportsGenerated} new mechanic reports");
            $this->info("Updated {$reportsUpdated} existing mechanic reports");
            $this->info("Skipped {$reportsSkipped} mechanics with no services");

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error("An error occurred: {$e->getMessage()}");
            return Command::FAILURE;
        }
    }
}
