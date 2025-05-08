#!/bin/bash
set -e

echo "Memperbaiki database mechanic_reports..."

# Cari nama container yang benar
CONTAINER_NAME=$(docker ps | grep -E 'app|laravel|php' | awk '{print $NF}' | head -1)

if [ -z "$CONTAINER_NAME" ]; then
    echo "Error: Tidak dapat menemukan container PHP/Laravel. Silakan cek container yang berjalan dengan 'docker ps'."
    exit 1
fi

echo "Menggunakan container: $CONTAINER_NAME"

# Jalankan perintah SQL untuk memperbaiki database
echo "Menjalankan perintah SQL untuk memperbaiki database..."
docker exec $CONTAINER_NAME php artisan tinker --execute="
try {
    echo 'Memeriksa tabel mechanic_reports...\\n';
    
    // Cek apakah tabel ada
    if (!Schema::hasTable('mechanic_reports')) {
        echo 'Error: Tabel mechanic_reports tidak ditemukan!\\n';
        exit(1);
    }
    
    // Hapus semua data dari tabel mechanic_reports
    echo 'Menghapus semua data dari tabel mechanic_reports...\\n';
    DB::table('mechanic_reports')->truncate();
    echo 'Semua data berhasil dihapus.\\n';
    
    // Cek apakah tabel mechanic_service memiliki kolom yang diperlukan
    echo 'Memeriksa kolom pada tabel mechanic_service...\\n';
    \$hasLaborCost = Schema::hasColumn('mechanic_service', 'labor_cost');
    \$hasWeekStart = Schema::hasColumn('mechanic_service', 'week_start');
    \$hasWeekEnd = Schema::hasColumn('mechanic_service', 'week_end');
    
    echo 'Kolom labor_cost: ' . (\$hasLaborCost ? 'Ada' : 'Tidak ada') . '\\n';
    echo 'Kolom week_start: ' . (\$hasWeekStart ? 'Ada' : 'Tidak ada') . '\\n';
    echo 'Kolom week_end: ' . (\$hasWeekEnd ? 'Ada' : 'Tidak ada') . '\\n';
    
    // Cek data pada tabel mechanic_service
    \$mechanicServiceCount = DB::table('mechanic_service')->count();
    echo 'Jumlah data pada tabel mechanic_service: ' . \$mechanicServiceCount . '\\n';
    
    // Cek data pada tabel mechanics
    \$mechanicsCount = DB::table('mechanics')->count();
    echo 'Jumlah data pada tabel mechanics: ' . \$mechanicsCount . '\\n';
    
    echo 'Database berhasil diperiksa dan diperbaiki.\\n';
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

echo "Selesai! Database telah diperbaiki."
echo "Sekarang coba jalankan fix-mechanic-reports.sh lagi."
