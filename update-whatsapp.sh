#!/bin/bash
set -e

echo "=== MENGGANTI NOMOR WHATSAPP ==="
echo "Script ini akan mengganti nomor WhatsApp di file cta.blade.php"

# Cari nama container yang benar
CONTAINER_NAME=$(docker ps | grep -E 'app|laravel|php' | awk '{print $NF}' | head -1)

if [ -z "$CONTAINER_NAME" ]; then
    echo "Error: Tidak dapat menemukan container PHP/Laravel. Silakan cek container yang berjalan dengan 'docker ps'."
    exit 1
fi

echo "Menggunakan container: $CONTAINER_NAME"

# Buat backup file asli
echo "Membuat backup file cta.blade.php..."
docker exec $CONTAINER_NAME cp resources/views/components/promos/cta.blade.php resources/views/components/promos/cta.blade.php.bak.$(date +%Y%m%d%H%M%S)

# Ganti nomor WhatsApp
echo "Mengganti nomor WhatsApp..."
docker exec $CONTAINER_NAME sed -i 's/https:\/\/wa.me\/6281234567890/https:\/\/wa.me\/6282135202581/g' resources/views/components/promos/cta.blade.php

# Perbaiki permission jika diperlukan
echo "Memperbaiki permission file..."
docker exec $CONTAINER_NAME chmod 644 resources/views/components/promos/cta.blade.php

# Bersihkan cache view
echo "Membersihkan cache view..."
docker exec $CONTAINER_NAME php artisan view:clear

echo "Selesai! Nomor WhatsApp telah diperbarui."
echo "Nomor lama: 6281234567890"
echo "Nomor baru: 6282135202581"
