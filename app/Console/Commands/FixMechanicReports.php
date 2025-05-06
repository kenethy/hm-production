<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Mechanic;
use App\Models\MechanicReport;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class FixMechanicReports extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:fix-mechanic-reports';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix mechanic reports by recalculating labor costs';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting to fix mechanic reports...');
        Log::info('Starting to fix mechanic reports via command');

        // 1. Fix labor_cost in mechanic_service table
        $this->fixLaborCostInMechanicService();

        // 2. Regenerate all mechanic reports
        $this->regenerateAllMechanicReports();

        $this->info('Mechanic reports fixed successfully!');
        Log::info('Mechanic reports fixed successfully via command');

        return Command::SUCCESS;
    }

    /**
     * Fix labor_cost in mechanic_service table
     */
    private function fixLaborCostInMechanicService(): void
    {
        $this->info('Fixing labor_cost in mechanic_service table...');
        Log::info('Fixing labor_cost in mechanic_service table');

        // Ambil semua data dari tabel mechanic_service
        $records = DB::table('mechanic_service')->get();
        $this->info('Found ' . $records->count() . ' records in mechanic_service table');
        Log::info('Found ' . $records->count() . ' records in mechanic_service table');

        $updatedCount = 0;

        // Update setiap record
        foreach ($records as $record) {
            $oldValue = $record->labor_cost;
            
            // Pastikan labor_cost adalah angka yang valid
            $newValue = (float) $oldValue;
            
            // Update hanya jika nilai berubah
            if ($oldValue != $newValue) {
                $this->info("Updating mechanic_service record: mechanic_id={$record->mechanic_id}, service_id={$record->service_id}, labor_cost: {$oldValue} -> {$newValue}");
                Log::info("Updating mechanic_service record: mechanic_id={$record->mechanic_id}, service_id={$record->service_id}, labor_cost: {$oldValue} -> {$newValue}");
                
                DB::table('mechanic_service')
                    ->where('mechanic_id', $record->mechanic_id)
                    ->where('service_id', $record->service_id)
                    ->update(['labor_cost' => $newValue]);
                
                $updatedCount++;
            }
        }

        $this->info("Updated {$updatedCount} records in mechanic_service table");
        Log::info("Updated {$updatedCount} records in mechanic_service table");
    }

    /**
     * Regenerate all mechanic reports
     */
    private function regenerateAllMechanicReports(): void
    {
        $this->info('Regenerating all mechanic reports...');
        Log::info('Regenerating all mechanic reports');

        // Ambil semua montir
        $mechanics = Mechanic::all();
        $this->info('Found ' . $mechanics->count() . ' mechanics');
        Log::info('Found ' . $mechanics->count() . ' mechanics');

        // Untuk setiap montir, regenerate laporan mingguan
        foreach ($mechanics as $mechanic) {
            $this->info("Processing mechanic: {$mechanic->name} (ID: {$mechanic->id})");
            Log::info("Processing mechanic: {$mechanic->name} (ID: {$mechanic->id})");

            // Ambil semua laporan mingguan montir ini
            $reports = MechanicReport::where('mechanic_id', $mechanic->id)->get();
            $this->info("Found " . $reports->count() . " reports for mechanic {$mechanic->name}");
            Log::info("Found " . $reports->count() . " reports for mechanic {$mechanic->name}");

            // Untuk setiap laporan, regenerate
            foreach ($reports as $report) {
                $weekStart = $report->week_start;
                $weekEnd = $report->week_end;

                $oldLaborCost = $report->total_labor_cost;
                
                // Regenerate laporan
                $updatedReport = $mechanic->generateWeeklyReport($weekStart, $weekEnd);
                
                if ($updatedReport) {
                    $this->info("Updated report ID={$report->id}: total_labor_cost: {$oldLaborCost} -> {$updatedReport->total_labor_cost}");
                    Log::info("Updated report ID={$report->id}: total_labor_cost: {$oldLaborCost} -> {$updatedReport->total_labor_cost}");
                } else {
                    $this->warn("Failed to update report ID={$report->id}");
                    Log::warning("Failed to update report ID={$report->id}");
                }
            }
        }

        $this->info('Finished regenerating all mechanic reports');
        Log::info('Finished regenerating all mechanic reports');
    }
}
