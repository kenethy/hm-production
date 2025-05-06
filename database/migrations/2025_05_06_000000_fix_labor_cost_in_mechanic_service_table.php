<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Log untuk debugging
        Log::info('Starting migration to fix labor_cost in mechanic_service table');

        // Ambil semua data dari tabel mechanic_service
        $records = DB::table('mechanic_service')->get();
        Log::info('Found ' . $records->count() . ' records in mechanic_service table');

        // Update setiap record
        foreach ($records as $record) {
            $oldValue = $record->labor_cost;
            
            // Pastikan labor_cost adalah angka yang valid
            $newValue = (float) $oldValue;
            
            // Update hanya jika nilai berubah
            if ($oldValue != $newValue) {
                Log::info("Updating mechanic_service record: mechanic_id={$record->mechanic_id}, service_id={$record->service_id}, labor_cost: {$oldValue} -> {$newValue}");
                
                DB::table('mechanic_service')
                    ->where('mechanic_id', $record->mechanic_id)
                    ->where('service_id', $record->service_id)
                    ->update(['labor_cost' => $newValue]);
            }
        }

        // Regenerate semua laporan mingguan montir
        $this->regenerateAllMechanicReports();

        Log::info('Finished migration to fix labor_cost in mechanic_service table');
    }

    /**
     * Regenerate semua laporan mingguan montir
     */
    private function regenerateAllMechanicReports(): void
    {
        Log::info('Regenerating all mechanic reports');

        // Ambil semua data dari tabel mechanic_report
        $reports = DB::table('mechanic_reports')->get();
        Log::info('Found ' . $reports->count() . ' reports in mechanic_reports table');

        // Untuk setiap laporan, hitung ulang total biaya jasa
        foreach ($reports as $report) {
            $totalLaborCost = DB::table('mechanic_service')
                ->where('mechanic_id', $report->mechanic_id)
                ->where('week_start', $report->week_start)
                ->where('week_end', $report->week_end)
                ->sum('labor_cost');

            Log::info("Recalculating report ID={$report->id}: mechanic_id={$report->mechanic_id}, total_labor_cost: {$report->total_labor_cost} -> {$totalLaborCost}");

            // Update laporan
            DB::table('mechanic_reports')
                ->where('id', $report->id)
                ->update(['total_labor_cost' => $totalLaborCost]);
        }

        Log::info('Finished regenerating all mechanic reports');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Tidak perlu melakukan apa-apa di down migration
    }
};
