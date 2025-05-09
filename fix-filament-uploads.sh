#!/bin/bash

# Script khusus untuk memperbaiki masalah upload file di Filament
# Script ini fokus pada perbaikan masalah upload file tanpa me-restart seluruh aplikasi

echo "====================================================="
echo "      SCRIPT PERBAIKAN UPLOAD FILE FILAMENT          "
echo "====================================================="
echo ""

# Deteksi apakah berjalan di Docker
IN_DOCKER=false
if [ -f /.dockerenv ] || grep -q docker /proc/1/cgroup 2>/dev/null; then
    IN_DOCKER=true
    echo "Terdeteksi berjalan di dalam container Docker."
else
    echo "Terdeteksi berjalan di lingkungan host biasa."
fi

# Tentukan path root aplikasi
if [ "$IN_DOCKER" = true ]; then
    APP_ROOT="/var/www/html"
else
    APP_ROOT="."
fi

echo "Menggunakan path root aplikasi: $APP_ROOT"
echo ""

# 1. Memperbaiki konfigurasi .env
echo ">> Memperbaiki konfigurasi .env..."
if [ -f "$APP_ROOT/.env" ]; then
    sed -i 's/SESSION_SECURE_COOKIE=false/SESSION_SECURE_COOKIE=true/g' "$APP_ROOT/.env"
    echo "Konfigurasi SESSION_SECURE_COOKIE diperbarui."
    
    # Pastikan FILESYSTEM_DISK diatur ke public
    if grep -q "FILESYSTEM_DISK=" "$APP_ROOT/.env"; then
        sed -i 's/FILESYSTEM_DISK=.*/FILESYSTEM_DISK=public/g' "$APP_ROOT/.env"
    else
        echo "FILESYSTEM_DISK=public" >> "$APP_ROOT/.env"
    fi
    echo "Konfigurasi FILESYSTEM_DISK diperbarui."
else
    echo "File .env tidak ditemukan!"
fi

# 2. Membuat direktori yang diperlukan
echo ""
echo ">> Membuat direktori yang diperlukan..."
mkdir -p "$APP_ROOT/storage/app/public/gallery"
mkdir -p "$APP_ROOT/storage/app/livewire-tmp"
echo "Direktori dibuat."

# 3. Mengatur izin file
echo ""
echo ">> Mengatur izin file..."
chmod -R 777 "$APP_ROOT/storage"
chmod -R 777 "$APP_ROOT/bootstrap/cache"
chmod -R 777 "$APP_ROOT/storage/app/public/gallery"
chmod -R 777 "$APP_ROOT/storage/app/livewire-tmp"
echo "Izin file diatur."

# 4. Membuat ulang symbolic link storage
echo ""
echo ">> Membuat ulang symbolic link storage..."
if [ -L "$APP_ROOT/public/storage" ]; then
    echo "Menghapus symbolic link storage yang ada..."
    rm "$APP_ROOT/public/storage"
fi
cd "$APP_ROOT" && php artisan storage:link
echo "Symbolic link storage dibuat ulang."

# 5. Membersihkan cache
echo ""
echo ">> Membersihkan cache..."
cd "$APP_ROOT" && php artisan config:clear
cd "$APP_ROOT" && php artisan cache:clear
cd "$APP_ROOT" && php artisan view:clear
echo "Cache dibersihkan."

# 6. Mempublikasikan ulang aset Livewire
echo ""
echo ">> Mempublikasikan ulang aset Livewire..."
cd "$APP_ROOT" && php artisan vendor:publish --force --tag=livewire:assets
echo "Aset Livewire dipublikasikan ulang."

# 7. Membuat file pengujian untuk upload
echo ""
echo ">> Membuat file pengujian untuk upload..."
TEST_IMAGE="$APP_ROOT/storage/app/public/test-upload.jpg"

# Buat file pengujian sederhana menggunakan PHP
php -r "
\$image = imagecreatetruecolor(1200, 675);
\$bgColor = imagecolorallocate(\$image, 255, 255, 255);
\$textColor = imagecolorallocate(\$image, 0, 0, 0);
imagefill(\$image, 0, 0, \$bgColor);
imagestring(\$image, 5, 500, 300, 'Test Image', \$textColor);
imagejpeg(\$image, '$TEST_IMAGE');
imagedestroy(\$image);
"

echo "File pengujian dibuat di: $TEST_IMAGE"

# 8. Uji upload ke direktori gallery
echo ""
echo ">> Menguji upload ke direktori gallery..."
GALLERY_TEST="$APP_ROOT/storage/app/public/gallery/test-upload.jpg"
cp "$TEST_IMAGE" "$GALLERY_TEST"
echo "File berhasil disalin ke: $GALLERY_TEST"

# 9. Verifikasi akses file
echo ""
echo ">> Verifikasi akses file..."
if [ -f "$GALLERY_TEST" ]; then
    echo "File ada di direktori gallery: YA"
    echo "Izin file: $(stat -c "%a" "$GALLERY_TEST")"
else
    echo "File tidak ada di direktori gallery: TIDAK"
fi

# 10. Membersihkan file sementara Livewire
echo ""
echo ">> Membersihkan file sementara Livewire..."
rm -rf "$APP_ROOT/storage/app/livewire-tmp"/*
echo "File sementara Livewire dibersihkan."

echo ""
echo "====================================================="
echo "      PERBAIKAN UPLOAD FILE SELESAI                  "
echo "====================================================="
echo ""
echo "Perbaikan untuk masalah upload file Filament telah selesai."
echo "Silakan buka browser dan coba upload file lagi."
echo ""
echo "Jika masih ada masalah, periksa log di storage/logs/laravel.log"
echo "====================================================="
