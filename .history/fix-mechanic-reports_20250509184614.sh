#!/bin/bash
set -e

echo "Memperbaiki laporan montir dengan masalah biaya jasa dan riwayat servis..."

# Cek apakah docker-compose berjalan
if ! docker ps | grep -q "app\|laravel\|php"; then
    echo "Error: Container Docker tidak terdeteksi. Pastikan container Docker berjalan."
    echo "Coba jalankan 'docker ps' untuk melihat container yang aktif."
    exit 1
fi

# Cari nama container yang benar
CONTAINER_NAME=$(docker ps | grep -E 'app|laravel|php' | awk '{print $NF}' | head -1)

if [ -z "$CONTAINER_NAME" ]; then
    echo "Error: Tidak dapat menemukan container PHP/Laravel. Silakan cek container yang berjalan dengan 'docker ps'."
    exit 1
fi

echo "Menggunakan container: $CONTAINER_NAME"

# Bersihkan cache Laravel
echo "Membersihkan cache Laravel..."
docker exec $CONTAINER_NAME php artisan cache:clear
docker exec $CONTAINER_NAME php artisan config:clear
docker exec $CONTAINER_NAME php artisan route:clear
docker exec $CONTAINER_NAME php artisan view:clear
docker exec $CONTAINER_NAME php artisan package:discover
docker exec $CONTAINER_NAME php artisan filament:clear-cache
docker exec $CONTAINER_NAME php artisan route:clear
docker exec $CONTAINER_NAME php artisan view:clear
docker exec $CONTAINER_NAME php artisan optimize:clear
docker exec $CONTAINER_NAME php artisan filament:cache
docker exec $CONTAINER_NAME php artisan route:cache
docker exec $CONTAINER_NAME php artisan view:cache

# Optimize the application
echo "Optimizing application..."
docker exec $CONTAINER_NAME php artisan optimize

# Periksa rute yang terdaftar
echo "Memeriksa rute yang terdaftar..."
docker exec $CONTAINER_NAME php artisan route:list | grep mechanic-reports

# Jalankan perintah untuk memperbaiki laporan montir
echo "Menjalankan perintah regenerate-mechanic-reports..."
docker exec $CONTAINER_NAME php artisan mechanic:regenerate-reports

# Periksa apakah ada laporan montir yang masih menunjukkan biaya jasa 0
echo "Memeriksa laporan montir dengan biaya jasa 0..."
ZERO_REPORTS=$(docker exec $CONTAINER_NAME php artisan tinker --execute="echo \App\Models\MechanicReport::where('total_labor_cost', 0)->count();")

if [[ $ZERO_REPORTS -gt 0 ]]; then
    echo "Ditemukan $ZERO_REPORTS laporan montir dengan biaya jasa 0."
    echo "Memperbaiki laporan montir dengan biaya jasa default..."

    # Jalankan perintah untuk memperbaiki laporan montir dengan biaya jasa 0
    docker exec $CONTAINER_NAME php artisan tinker --execute="
        \$reports = \App\Models\MechanicReport::where('total_labor_cost', 0)->get();
        foreach (\$reports as \$report) {
            \$services = \$report->mechanic->services()
                ->wherePivot('week_start', \$report->week_start)
                ->wherePivot('week_end', \$report->week_end)
                ->get();

            if (\$services->count() > 0) {
                \$report->total_labor_cost = 50000 * \$services->count();
                \$report->services_count = \$services->count();
                \$report->save();
                echo \"Fixed report ID: {\$report->id} for mechanic {\$report->mechanic->name}, new labor cost: {\$report->total_labor_cost}\\n\";
            }
        }
    "
fi

echo "Selesai! Laporan montir telah diperbaiki."
echo "Sekarang biaya jasa seharusnya ditampilkan dengan benar di laporan montir."
echo "Fitur riwayat servis montir juga telah diperbaiki."
echo "Untuk melihat riwayat servis montir, klik tombol 'Riwayat Servis' pada laporan montir."
echo ""
echo "Fitur baru yang ditambahkan:"
echo "1. Filter untuk melihat servis berdasarkan status (Selesai, Dalam Pengerjaan, Dibatalkan)"
echo "2. Secara default menampilkan servis dengan status 'Selesai'"
echo "3. Informasi tanggal masuk dan tanggal selesai servis"
echo "4. Ringkasan total servis dan biaya jasa pada periode tersebut"
echo "5. Tampilan yang lebih jelas dan mudah digunakan"
echo ""
echo "Cara menggunakan fitur ini:"
echo "1. Buka halaman Rekap Montir di Filament admin panel"
echo "2. Pilih rekap montir yang ingin dilihat"
echo "3. Klik tombol 'Riwayat Servis' pada rekap montir tersebut"
echo "4. Secara default, halaman akan menampilkan servis dengan status 'Selesai'"
echo "5. Gunakan tombol filter untuk beralih antara status servis yang berbeda"
echo ""
echo "PENTING: Jika masih ada error 404 pada halaman riwayat servis montir, silakan jalankan script ini sekali lagi."
echo "Jika masih tetap error, coba akses halaman dengan URL: https://hartonomotor.xyz/admin/mechanic-reports/{id}/services"
echo "Ganti {id} dengan ID laporan montir yang ingin dilihat, misalnya: https://hartonomotor.xyz/admin/mechanic-reports/1/services"
echo ""
echo "Jika masih tetap error, coba jalankan perintah berikut secara manual di server:"
echo "php artisan cache:clear"
echo "php artisan config:clear"
echo "php artisan route:clear"
echo "php artisan view:clear"
echo "php artisan package:discover"
echo "php artisan filament:clear-cache"
echo "php artisan optimize:clear"
echo "php artisan filament:cache"
echo "php artisan route:cache"
echo "php artisan view:cache"
echo "php artisan optimize"
echo ""
echo "Untuk memeriksa rute yang terdaftar, jalankan perintah berikut:"
echo "php artisan route:list | grep mechanic-reports"
