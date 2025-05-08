#!/bin/bash
set -e

echo "=== MEMERIKSA HASIL PERBAIKAN ==="
echo "Script ini akan memeriksa apakah perbaikan telah berhasil diterapkan."

# Cari nama container yang benar
CONTAINER_NAME=$(docker ps | grep -E 'app|laravel|php' | awk '{print $NF}' | head -1)

if [ -z "$CONTAINER_NAME" ]; then
    echo "Error: Tidak dapat menemukan container PHP/Laravel. Silakan cek container yang berjalan dengan 'docker ps'."
    exit 1
fi

echo "Menggunakan container: $CONTAINER_NAME"

# Langkah 1: Periksa file Mechanic.php
echo ""
echo "=== LANGKAH 1: MEMERIKSA FILE MECHANIC.PHP ==="
echo "Memeriksa apakah file Mechanic.php berisi metode generateWeeklyReport yang benar..."
docker exec $CONTAINER_NAME grep -A 5 "public function generateWeeklyReport" app/Models/Mechanic.php

# Langkah 2: Periksa database
echo ""
echo "=== LANGKAH 2: MEMERIKSA DATABASE ==="
echo "Memeriksa tabel mechanic_reports..."
docker exec $CONTAINER_NAME php artisan tinker --execute="
try {
    echo 'Memeriksa tabel mechanic_reports...\\n';
    
    // Cek apakah tabel ada
    if (!Schema::hasTable('mechanic_reports')) {
        echo 'Error: Tabel mechanic_reports tidak ditemukan!\\n';
        exit(1);
    }
    
    // Ambil semua data dari tabel mechanic_reports
    \$reports = DB::table('mechanic_reports')->get();
    echo 'Total laporan: ' . \$reports->count() . '\\n';
    
    // Cek apakah ada duplikat
    \$grouped = \$reports->groupBy(function(\$item) {
        return \$item->mechanic_id . '-' . \$item->week_start . '-' . \$item->week_end;
    });
    
    \$duplicates = \$grouped->filter(function(\$group) {
        return \$group->count() > 1;
    });
    
    if (\$duplicates->count() > 0) {
        echo 'Error: Ditemukan ' . \$duplicates->count() . ' grup duplikat!\\n';
        foreach (\$duplicates as \$key => \$group) {
            echo 'Grup: ' . \$key . ', Jumlah: ' . \$group->count() . '\\n';
        }
    } else {
        echo 'Tidak ditemukan duplikat. Bagus!\\n';
    }
    
    // Tampilkan beberapa laporan
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

# Langkah 3: Periksa ServiceResource.php
echo ""
echo "=== LANGKAH 3: MEMERIKSA SERVICERESOUCE.PHP ==="
echo "Memeriksa apakah ServiceResource.php berisi try-catch untuk generateWeeklyReport..."
docker exec $CONTAINER_NAME grep -A 10 "try {" app/Filament/Resources/ServiceResource.php | grep -A 5 "generateWeeklyReport"

echo ""
echo "=== PEMERIKSAAN SELESAI ==="
echo "Silakan periksa hasil di atas untuk memastikan perbaikan telah berhasil diterapkan."
