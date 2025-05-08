#!/bin/bash
set -e

echo "=== PERBAIKAN MENYELURUH UNTUK MASALAH LAPORAN MONTIR ==="
echo "Script ini akan memperbaiki masalah laporan montir secara menyeluruh."
echo "Pastikan Anda telah membuat backup database sebelum melanjutkan."
echo ""
read -p "Apakah Anda ingin melanjutkan? (y/n): " confirm
if [[ $confirm != "y" && $confirm != "Y" ]]; then
    echo "Operasi dibatalkan."
    exit 1
fi

# Cari nama container yang benar
CONTAINER_NAME=$(docker ps | grep -E 'app|laravel|php' | awk '{print $NF}' | head -1)

if [ -z "$CONTAINER_NAME" ]; then
    echo "Error: Tidak dapat menemukan container PHP/Laravel. Silakan cek container yang berjalan dengan 'docker ps'."
    exit 1
fi

echo "Menggunakan container: $CONTAINER_NAME"

# Langkah 1: Backup file-file penting
echo ""
echo "=== LANGKAH 1: BACKUP FILE-FILE PENTING ==="
echo "Membuat backup file Mechanic.php..."
docker exec $CONTAINER_NAME cp app/Models/Mechanic.php app/Models/Mechanic.php.bak.$(date +%Y%m%d%H%M%S)

# Langkah 2: Ganti file Mechanic.php dengan versi baru
echo ""
echo "=== LANGKAH 2: MENGGANTI FILE MECHANIC.PHP ==="
echo "Mengganti file Mechanic.php dengan versi baru..."
docker cp new-mechanic.php $CONTAINER_NAME:/var/www/html/app/Models/Mechanic.php
echo "File Mechanic.php berhasil diganti."

# Langkah 3: Perbaiki database
echo ""
echo "=== LANGKAH 3: MEMPERBAIKI DATABASE ==="
echo "Memperbaiki tabel mechanic_reports..."
docker exec $CONTAINER_NAME php artisan tinker --execute="
try {
    echo 'Memperbaiki tabel mechanic_reports...\\n';
    
    // Cek apakah tabel ada
    if (!Schema::hasTable('mechanic_reports')) {
        echo 'Error: Tabel mechanic_reports tidak ditemukan!\\n';
        exit(1);
    }
    
    // Hapus semua data dari tabel mechanic_reports
    echo 'Menghapus semua data dari tabel mechanic_reports...\\n';
    DB::table('mechanic_reports')->truncate();
    echo 'Semua data berhasil dihapus.\\n';
    
    // Regenerasi laporan untuk semua montir
    echo 'Regenerasi laporan untuk semua montir...\\n';
    \$mechanics = App\\Models\\Mechanic::all();
    echo 'Total montir: ' . \$mechanics->count() . '\\n';
    
    foreach (\$mechanics as \$mechanic) {
        echo 'Memproses montir: ' . \$mechanic->name . ' (ID: ' . \$mechanic->id . ')\\n';
        
        // Ambil semua servis untuk montir ini
        \$services = DB::table('mechanic_service')
            ->where('mechanic_id', \$mechanic->id)
            ->whereNotNull('week_start')
            ->whereNotNull('week_end')
            ->get();
        
        echo 'Total servis: ' . \$services->count() . '\\n';
        
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
            \$totalLaborCost = \$weekServices->sum('labor_cost');
            
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
    
    echo 'Selesai memperbaiki database.\\n';
} catch (Exception \$e) {
    echo 'Error: ' . \$e->getMessage() . '\\n';
}
"

# Langkah 4: Perbaiki ServiceResource.php
echo ""
echo "=== LANGKAH 4: MEMPERBAIKI SERVICERESOUCE.PHP ==="
echo "Menambahkan try-catch pada panggilan generateWeeklyReport..."
docker exec $CONTAINER_NAME php -r "
\$file = file_get_contents('app/Filament/Resources/ServiceResource.php');
\$pattern = '/(\\\$mechanic->generateWeeklyReport\(\\\$weekStart, \\\$weekEnd\);)/';
\$replacement = 'try {
                                        \$mechanic->generateWeeklyReport(\$weekStart, \$weekEnd);
                                    } catch (\Exception \$e) {
                                        \Illuminate\Support\Facades\Log::error(\"Error generating weekly report: \" . \$e->getMessage(), [
                                            \"mechanic_id\" => \$mechanic->id,
                                            \"week_start\" => \$weekStart,
                                            \"week_end\" => \$weekEnd
                                        ]);
                                    }';
\$newFile = preg_replace(\$pattern, \$replacement, \$file);
file_put_contents('app/Filament/Resources/ServiceResource.php', \$newFile);
echo 'ServiceResource.php berhasil diperbarui.';
"

# Langkah 5: Bersihkan cache Laravel
echo ""
echo "=== LANGKAH 5: MEMBERSIHKAN CACHE LARAVEL ==="
echo "Membersihkan cache Laravel..."
docker exec $CONTAINER_NAME php artisan cache:clear
docker exec $CONTAINER_NAME php artisan config:clear
docker exec $CONTAINER_NAME php artisan route:clear
docker exec $CONTAINER_NAME php artisan view:clear
docker exec $CONTAINER_NAME php artisan optimize

echo ""
echo "=== PERBAIKAN SELESAI ==="
echo "Masalah laporan montir telah diperbaiki secara menyeluruh."
echo "Silakan coba gunakan aplikasi seperti biasa dan laporkan jika masih ada masalah."
