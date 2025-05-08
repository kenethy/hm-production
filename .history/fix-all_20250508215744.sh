#!/bin/bash
set -e

echo "Memperbaiki masalah laporan montir secara menyeluruh..."

# Cari nama container yang benar
CONTAINER_NAME=$(docker ps | grep -E 'app|laravel|php' | awk '{print $NF}' | head -1)

if [ -z "$CONTAINER_NAME" ]; then
    echo "Error: Tidak dapat menemukan container PHP/Laravel. Silakan cek container yang berjalan dengan 'docker ps'."
    exit 1
fi

echo "Menggunakan container: $CONTAINER_NAME"

# 1. Perbaiki model Mechanic.php
echo "Langkah 1: Memperbaiki model Mechanic.php..."
bash fix-mechanic-model-simple.sh

# 2. Perbaiki ServiceResource.php
echo "Langkah 2: Memperbaiki ServiceResource.php..."
bash fix-service-resource-simple.sh

# 3. Perbaiki database
echo "Langkah 3: Memperbaiki database..."
docker exec $CONTAINER_NAME php artisan tinker --execute="
try {
    echo 'Memperbaiki tabel mechanic_reports...\\n';

    // Cek apakah tabel ada
    if (!Schema::hasTable('mechanic_reports')) {
        echo 'Error: Tabel mechanic_reports tidak ditemukan!\\n';
        exit(1);
    }

    // Ambil semua data dari tabel mechanic_reports
    \$reports = DB::table('mechanic_reports')->get();
    echo 'Total laporan: ' . \$reports->count() . '\\n';

    // Kelompokkan berdasarkan mechanic_id, week_start, week_end
    \$grouped = \$reports->groupBy(function(\$item) {
        return \$item->mechanic_id . '-' . \$item->week_start . '-' . \$item->week_end;
    });

    // Cari grup yang memiliki lebih dari 1 item (duplikat)
    \$duplicates = \$grouped->filter(function(\$group) {
        return \$group->count() > 1;
    });

    echo 'Grup duplikat ditemukan: ' . \$duplicates->count() . '\\n';

    // Hapus duplikat, simpan hanya yang terbaru
    foreach (\$duplicates as \$key => \$group) {
        echo 'Memproses grup: ' . \$key . '\\n';

        // Urutkan berdasarkan ID (tertinggi = terbaru)
        \$sorted = \$group->sortByDesc('id');

        // Simpan ID tertinggi
        \$keepId = \$sorted->first()->id;
        echo 'Mempertahankan ID: ' . \$keepId . '\\n';

        // Hapus yang lain
        foreach (\$sorted->slice(1) as \$duplicate) {
            echo 'Menghapus ID: ' . \$duplicate->id . '\\n';
            DB::table('mechanic_reports')->where('id', \$duplicate->id)->delete();
        }
    }

    echo 'Selesai membersihkan laporan duplikat.\\n';
} catch (Exception \$e) {
    echo 'Error: ' . \$e->getMessage() . '\\n';
}
"

# 4. Bersihkan cache Laravel
echo "Langkah 4: Membersihkan cache Laravel..."
docker exec $CONTAINER_NAME php artisan cache:clear
docker exec $CONTAINER_NAME php artisan config:clear
docker exec $CONTAINER_NAME php artisan route:clear
docker exec $CONTAINER_NAME php artisan view:clear
docker exec $CONTAINER_NAME php artisan optimize

echo "Selesai! Masalah laporan montir telah diperbaiki secara menyeluruh."
echo "Silakan coba gunakan aplikasi seperti biasa dan laporkan jika masih ada masalah."
