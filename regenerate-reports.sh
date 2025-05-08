#!/bin/bash
set -e

echo "=== REGENERASI LAPORAN MONTIR ==="
echo "Script ini akan menghapus dan membuat ulang semua laporan montir."

# Cari nama container yang benar
CONTAINER_NAME=$(docker ps | grep -E 'app|laravel|php' | awk '{print $NF}' | head -1)

if [ -z "$CONTAINER_NAME" ]; then
    echo "Error: Tidak dapat menemukan container PHP/Laravel. Silakan cek container yang berjalan dengan 'docker ps'."
    exit 1
fi

echo "Menggunakan container: $CONTAINER_NAME"

# Jalankan regenerasi laporan montir menggunakan Artisan Tinker
echo "Menjalankan regenerasi laporan montir..."
docker exec $CONTAINER_NAME php artisan tinker --execute="
try {
    echo 'Menghapus semua data dari tabel mechanic_reports...\\n';
    DB::table('mechanic_reports')->truncate();
    
    echo 'Regenerasi laporan untuk semua montir...\\n';
    \$mechanics = App\\Models\\Mechanic::all();
    echo 'Total montir: ' . \$mechanics->count() . '\\n';
    
    foreach (\$mechanics as \$mechanic) {
        echo '\\nMemproses montir: ' . \$mechanic->name . ' (ID: ' . \$mechanic->id . ')\\n';
        
        // Ambil semua servis untuk montir ini
        \$services = DB::table('mechanic_service')
            ->where('mechanic_id', \$mechanic->id)
            ->whereNotNull('week_start')
            ->whereNotNull('week_end')
            ->get();
        
        echo 'Total servis: ' . \$services->count() . '\\n';
        
        if (\$services->count() == 0) {
            echo 'Tidak ada servis untuk montir ini. Lewati.\\n';
            continue;
        }
        
        // Kelompokkan servis berdasarkan minggu
        \$groupedServices = \$services->groupBy(function(\$service) {
            return \$service->week_start . '-' . \$service->week_end;
        });
        
        echo 'Total minggu: ' . \$groupedServices->count() . '\\n';
        
        // Buat laporan untuk setiap minggu
        foreach (\$groupedServices as \$weekKey => \$weekServices) {
            list(\$weekStart, \$weekEnd) = explode('-', \$weekKey);
            
            echo 'Membuat laporan untuk minggu: ' . \$weekStart . ' sampai ' . \$weekEnd . '\\n';
            
            // Hitung total biaya jasa
            \$totalLaborCost = 0;
            foreach (\$weekServices as \$service) {
                \$totalLaborCost += (float) \$service->labor_cost;
                echo 'Service ID: ' . \$service->service_id . ', Labor Cost: ' . \$service->labor_cost . '\\n';
            }
            
            echo 'Total Labor Cost: ' . \$totalLaborCost . '\\n';
            
            // Buat laporan baru
            DB::table('mechanic_reports')->insert([
                'mechanic_id' => \$mechanic->id,
                'week_start' => \$weekStart,
                'week_end' => \$weekEnd,
                'services_count' => \$weekServices->count(),
                'total_labor_cost' => \$totalLaborCost,
                'created_at' => now(),
                'updated_at' => now()
            ]);
            
            echo 'Laporan berhasil dibuat.\\n';
        }
    }
    
    echo '\\nSelesai regenerasi laporan montir.\\n';
    
    // Tampilkan beberapa laporan terbaru
    echo '\\nBeberapa laporan terbaru:\\n';
    \$latestReports = DB::table('mechanic_reports')
        ->orderBy('id', 'desc')
        ->limit(5)
        ->get();
    
    foreach (\$latestReports as \$report) {
        echo 'ID: ' . \$report->id . 
             ', Mechanic ID: ' . \$report->mechanic_id . 
             ', Week: ' . \$report->week_start . ' to ' . \$report->week_end . 
             ', Services: ' . \$report->services_count . 
             ', Labor Cost: ' . \$report->total_labor_cost . '\\n';
    }
} catch (Exception \$e) {
    echo 'Error: ' . \$e->getMessage() . '\\n';
}
"

# Bersihkan cache Laravel
echo "Membersihkan cache Laravel..."
docker exec $CONTAINER_NAME php artisan cache:clear
docker exec $CONTAINER_NAME php artisan config:clear
docker exec $CONTAINER_NAME php artisan route:clear
docker exec $CONTAINER_NAME php artisan view:clear
docker exec $CONTAINER_NAME php artisan optimize

echo ""
echo "=== REGENERASI SELESAI ==="
echo "Laporan montir telah dibuat ulang."
echo "Silakan cek rekap montir untuk memastikan biaya jasa sudah muncul dengan benar."
