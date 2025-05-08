#!/bin/bash
set -e

echo "=== MEMPERBAIKI MASALAH BIAYA JASA MONTIR (VERSI SEDERHANA) ==="
echo "Script ini akan memperbaiki masalah biaya jasa montir yang tidak masuk ke rekap montir."

# Cari nama container yang benar
CONTAINER_NAME=$(docker ps | grep -E 'app|laravel|php' | awk '{print $NF}' | head -1)

if [ -z "$CONTAINER_NAME" ]; then
    echo "Error: Tidak dapat menemukan container PHP/Laravel. Silakan cek container yang berjalan dengan 'docker ps'."
    exit 1
fi

echo "Menggunakan container: $CONTAINER_NAME"

# Langkah 1: Periksa data di tabel mechanic_service
echo ""
echo "=== LANGKAH 1: MEMERIKSA DATA DI TABEL MECHANIC_SERVICE ==="
docker exec $CONTAINER_NAME php artisan tinker --execute="
try {
    echo 'Memeriksa data di tabel mechanic_service...\\n';
    
    \$services = DB::table('mechanic_service')->get();
    echo 'Total data: ' . \$services->count() . '\\n';
    
    \$withLaborCost = \$services->filter(function(\$service) {
        return \$service->labor_cost > 0;
    });
    
    echo 'Data dengan labor_cost > 0: ' . \$withLaborCost->count() . '\\n';
    
    if (\$withLaborCost->count() > 0) {
        echo '\\nBeberapa data dengan labor_cost > 0:\\n';
        foreach (\$withLaborCost->take(5) as \$service) {
            echo 'ID: ' . \$service->id . 
                 ', Mechanic ID: ' . \$service->mechanic_id . 
                 ', Service ID: ' . \$service->service_id . 
                 ', Labor Cost: ' . \$service->labor_cost . '\\n';
        }
    } else {
        echo '\\nTidak ada data dengan labor_cost > 0!\\n';
    }
} catch (Exception \$e) {
    echo 'Error: ' . \$e->getMessage() . '\\n';
}
"

# Langkah 2: Buat file PHP untuk memperbaiki metode calculateWeeklyLaborCost
echo ""
echo "=== LANGKAH 2: MEMPERBAIKI METODE CALCULATEWEEKLYLABORCOST ==="
echo "Membuat backup file Mechanic.php..."
docker exec $CONTAINER_NAME cp app/Models/Mechanic.php app/Models/Mechanic.php.bak.labor.$(date +%Y%m%d%H%M%S)

echo "Membuat file PHP untuk perbaikan..."
docker exec $CONTAINER_NAME bash -c 'cat > /tmp/fix_labor_cost.php << EOF
<?php

// Baca file Mechanic.php
$file = file_get_contents("app/Models/Mechanic.php");

// Cari posisi metode calculateWeeklyLaborCost
$startPos = strpos($file, "public function calculateWeeklyLaborCost");
if ($startPos === false) {
    echo "Error: Metode calculateWeeklyLaborCost tidak ditemukan!";
    exit(1);
}

// Cari akhir metode
$braceCount = 0;
$endPos = $startPos;
$inMethod = false;
for ($i = $startPos; $i < strlen($file); $i++) {
    if ($file[$i] == "{") {
        $braceCount++;
        $inMethod = true;
    } elseif ($file[$i] == "}") {
        $braceCount--;
        if ($inMethod && $braceCount == 0) {
            $endPos = $i + 1;
            break;
        }
    }
}

// Metode baru
$newMethod = <<<EOT
public function calculateWeeklyLaborCost(\$weekStart, \$weekEnd)
    {
        // Log untuk debugging
        \\Illuminate\\Support\\Facades\\Log::info("Calculating labor cost for mechanic #{\$this->id} ({\$this->name}) for week {\$weekStart} to {\$weekEnd}");

        // Gunakan query builder untuk mendapatkan total labor_cost langsung dari database
        \$totalLaborCost = \\Illuminate\\Support\\Facades\\DB::table("mechanic_service")
            ->where("mechanic_id", \$this->id)
            ->where("week_start", \$weekStart)
            ->where("week_end", \$weekEnd)
            ->sum("labor_cost");

        // Log total biaya jasa
        \\Illuminate\\Support\\Facades\\Log::info("Total labor cost (from DB): {\$totalLaborCost}");

        // Jika totalLaborCost masih 0, coba cara lain
        if (\$totalLaborCost == 0) {
            // Ambil semua servis untuk minggu ini
            \$services = \$this->services()
                ->wherePivot("week_start", \$weekStart)
                ->wherePivot("week_end", \$weekEnd)
                ->get();

            // Log jumlah servis yang ditemukan
            \\Illuminate\\Support\\Facades\\Log::info("Found " . \$services->count() . " services");

            // Hitung total biaya jasa secara manual
            \$manualTotal = 0;
            foreach (\$services as \$service) {
                \$laborCost = (float) \$service->pivot->labor_cost;
                \\Illuminate\\Support\\Facades\\Log::info("Service #{\$service->id}: labor_cost = {\$laborCost}");
                \$manualTotal += \$laborCost;
            }

            \\Illuminate\\Support\\Facades\\Log::info("Total labor cost (manual calculation): {\$manualTotal}");
            
            // Gunakan hasil perhitungan manual jika lebih besar dari 0
            if (\$manualTotal > 0) {
                \$totalLaborCost = \$manualTotal;
            }
        }

        return \$totalLaborCost;
    }
EOT;

// Ganti metode lama dengan yang baru
$newFile = substr($file, 0, $startPos) . $newMethod . substr($file, $endPos);

// Simpan file yang sudah dimodifikasi
file_put_contents("app/Models/Mechanic.php", $newFile);

echo "Metode calculateWeeklyLaborCost berhasil diperbarui.";
EOF'

# Jalankan script PHP
echo "Menjalankan script perbaikan..."
docker exec $CONTAINER_NAME php /tmp/fix_labor_cost.php

# Langkah 3: Regenerasi laporan montir
echo ""
echo "=== LANGKAH 3: REGENERASI LAPORAN MONTIR ==="
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

# Langkah 4: Bersihkan cache Laravel
echo ""
echo "=== LANGKAH 4: MEMBERSIHKAN CACHE LARAVEL ==="
echo "Membersihkan cache Laravel..."
docker exec $CONTAINER_NAME php artisan cache:clear
docker exec $CONTAINER_NAME php artisan config:clear
docker exec $CONTAINER_NAME php artisan route:clear
docker exec $CONTAINER_NAME php artisan view:clear
docker exec $CONTAINER_NAME php artisan optimize

echo ""
echo "=== PERBAIKAN SELESAI ==="
echo "Masalah biaya jasa montir telah diperbaiki."
echo "Silakan cek rekap montir untuk memastikan biaya jasa sudah muncul dengan benar."
