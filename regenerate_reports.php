<?php
// Script untuk regenerasi laporan montir

use App\Models\Mechanic;
use Illuminate\Support\Facades\DB;

// Hapus semua data dari tabel mechanic_reports
echo "Menghapus semua data dari tabel mechanic_reports...\n";
DB::table('mechanic_reports')->truncate();

// Regenerasi laporan untuk semua montir
echo "Regenerasi laporan untuk semua montir...\n";
$mechanics = Mechanic::all();
echo "Total montir: " . $mechanics->count() . "\n";

foreach ($mechanics as $mechanic) {
    echo "\nMemproses montir: " . $mechanic->name . " (ID: " . $mechanic->id . ")\n";
    
    // Ambil semua servis untuk montir ini
    $services = DB::table('mechanic_service')
        ->where('mechanic_id', $mechanic->id)
        ->whereNotNull('week_start')
        ->whereNotNull('week_end')
        ->get();
    
    echo "Total servis: " . $services->count() . "\n";
    
    if ($services->count() == 0) {
        echo "Tidak ada servis untuk montir ini. Lewati.\n";
        continue;
    }
    
    // Kelompokkan servis berdasarkan minggu
    $groupedServices = $services->groupBy(function($service) {
        return $service->week_start . '-' . $service->week_end;
    });
    
    echo "Total minggu: " . $groupedServices->count() . "\n";
    
    // Buat laporan untuk setiap minggu
    foreach ($groupedServices as $weekKey => $weekServices) {
        list($weekStart, $weekEnd) = explode('-', $weekKey);
        
        echo "Membuat laporan untuk minggu: " . $weekStart . " sampai " . $weekEnd . "\n";
        
        // Hitung total biaya jasa
        $totalLaborCost = 0;
        foreach ($weekServices as $service) {
            $totalLaborCost += (float) $service->labor_cost;
            echo "Service ID: " . $service->service_id . ", Labor Cost: " . $service->labor_cost . "\n";
        }
        
        echo "Total Labor Cost: " . $totalLaborCost . "\n";
        
        // Buat laporan baru
        DB::table('mechanic_reports')->insert([
            'mechanic_id' => $mechanic->id,
            'week_start' => $weekStart,
            'week_end' => $weekEnd,
            'services_count' => $weekServices->count(),
            'total_labor_cost' => $totalLaborCost,
            'created_at' => now(),
            'updated_at' => now()
        ]);
        
        echo "Laporan berhasil dibuat.\n";
    }
}

echo "\nSelesai regenerasi laporan montir.\n";

// Tampilkan beberapa laporan terbaru
echo "\nBeberapa laporan terbaru:\n";
$latestReports = DB::table('mechanic_reports')
    ->orderBy('id', 'desc')
    ->limit(5)
    ->get();

foreach ($latestReports as $report) {
    echo "ID: " . $report->id . 
         ", Mechanic ID: " . $report->mechanic_id . 
         ", Week: " . $report->week_start . " to " . $report->week_end . 
         ", Services: " . $report->services_count . 
         ", Labor Cost: " . $report->total_labor_cost . "\n";
}
