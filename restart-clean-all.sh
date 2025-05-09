#!/bin/bash

# Script untuk membersihkan dan me-restart seluruh aplikasi
# Gunakan dengan hati-hati karena akan membersihkan semua cache dan me-restart layanan

echo "====================================================="
echo "      SCRIPT PEMBERSIHAN DAN RESTART APLIKASI        "
echo "====================================================="
echo ""
echo "Script ini akan:"
echo "1. Membersihkan semua cache aplikasi"
echo "2. Mengatur ulang izin file"
echo "3. Membuat ulang symbolic link storage"
echo "4. Membersihkan file sementara"
echo "5. Me-restart semua layanan (PHP-FPM, Nginx, dll)"
echo ""
echo "PERINGATAN: Pastikan tidak ada proses penting yang sedang berjalan!"
echo ""
read -p "Lanjutkan? (y/n): " confirm
if [[ $confirm != "y" && $confirm != "Y" ]]; then
    echo "Operasi dibatalkan."
    exit 1
fi

echo ""
echo "====================================================="
echo "Memulai proses pembersihan dan restart..."
echo "====================================================="

# 1. Membersihkan semua cache aplikasi
echo ""
echo ">> Membersihkan cache aplikasi..."
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan optimize:clear
php artisan event:clear
php artisan queue:restart

# 2. Membersihkan file sesi
echo ""
echo ">> Membersihkan file sesi..."
rm -rf storage/framework/sessions/*
echo "File sesi dibersihkan."

# 3. Membersihkan file log (opsional)
echo ""
echo ">> Membersihkan file log..."
echo "" > storage/logs/laravel.log
echo "File log dibersihkan."

# 4. Membersihkan file sementara Livewire
echo ""
echo ">> Membersihkan file sementara Livewire..."
rm -rf storage/app/livewire-tmp/*
mkdir -p storage/app/livewire-tmp
echo "File sementara Livewire dibersihkan."

# 5. Mengatur ulang izin file
echo ""
echo ">> Mengatur ulang izin file..."
chmod -R 755 public
chmod -R 777 storage
chmod -R 777 bootstrap/cache
chown -R www-data:www-data storage
chown -R www-data:www-data bootstrap/cache
chown -R www-data:www-data public

# Membuat direktori yang diperlukan
echo ""
echo ">> Membuat direktori yang diperlukan..."
mkdir -p storage/app/public/gallery
chmod -R 777 storage/app/public/gallery
chown -R www-data:www-data storage/app/public/gallery

# 6. Membuat ulang symbolic link storage
echo ""
echo ">> Membuat ulang symbolic link storage..."
if [ -L public/storage ]; then
    echo "Menghapus symbolic link storage yang ada..."
    rm public/storage
fi
php artisan storage:link
echo "Symbolic link storage dibuat ulang."

# 7. Memperbaiki konfigurasi .env
echo ""
echo ">> Memperbaiki konfigurasi .env..."
sed -i 's/SESSION_SECURE_COOKIE=false/SESSION_SECURE_COOKIE=true/g' .env
echo "Konfigurasi .env diperbaiki."

# 8. Mempublikasikan ulang aset Livewire
echo ""
echo ">> Mempublikasikan ulang aset Livewire..."
php artisan vendor:publish --force --tag=livewire:assets
echo "Aset Livewire dipublikasikan ulang."

# 9. Me-restart layanan
echo ""
echo ">> Me-restart layanan..."

# Restart PHP-FPM
if command -v service >/dev/null 2>&1; then
    echo "Me-restart PHP-FPM..."
    service php8.2-fpm restart || service php8.1-fpm restart || service php8.0-fpm restart || service php7.4-fpm restart || echo "Tidak dapat me-restart PHP-FPM"
fi

# Restart Nginx
if command -v service >/dev/null 2>&1; then
    echo "Me-restart Nginx..."
    service nginx restart || echo "Tidak dapat me-restart Nginx"
fi

# Restart Apache (jika digunakan)
if command -v service >/dev/null 2>&1; then
    if service --status-all | grep -Fq 'apache2'; then
        echo "Me-restart Apache..."
        service apache2 restart || echo "Tidak dapat me-restart Apache"
    fi
fi

# 10. Membersihkan cache Opcache (jika ada)
echo ""
echo ">> Membersihkan cache Opcache..."
if php -r 'echo (int)function_exists("opcache_reset");' == 1; then
    php -r 'opcache_reset();'
    echo "Cache Opcache dibersihkan."
else
    echo "Opcache tidak tersedia atau tidak diaktifkan."
fi

# 11. Verifikasi status aplikasi
echo ""
echo ">> Verifikasi status aplikasi..."
php artisan about

echo ""
echo "====================================================="
echo "      PEMBERSIHAN DAN RESTART SELESAI                "
echo "====================================================="
echo ""
echo "Aplikasi telah dibersihkan dan di-restart."
echo "Silakan buka browser dan periksa apakah masalah telah teratasi."
echo ""
echo "Jika masih ada masalah, periksa log di storage/logs/laravel.log"
echo "====================================================="
