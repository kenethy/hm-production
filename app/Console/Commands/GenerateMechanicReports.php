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

        foreach ($mechanics as $mechanic) {
            // Generate weekly report for the mechanic
            $report = $mechanic->generateWeeklyReport($weekStart, $weekEnd);

            if ($report) {
                $reportsGenerated++;
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();

        $this->info("Generated {$reportsGenerated} mechanic reports");

        return Command::SUCCESS;
    }
}
