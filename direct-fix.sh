#!/bin/bash
set -e

echo "=== PERBAIKAN LANGSUNG UNTUK MASALAH BIAYA JASA MONTIR ==="
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

# Langkah 2: Upload dan jalankan script PHP untuk memperbaiki metode calculateWeeklyLaborCost
echo ""
echo "=== LANGKAH 2: MEMPERBAIKI METODE CALCULATEWEEKLYLABORCOST ==="
echo "Membuat backup file Mechanic.php..."
docker exec $CONTAINER_NAME cp app/Models/Mechanic.php app/Models/Mechanic.php.bak.direct.$(date +%Y%m%d%H%M%S)

echo "Mengupload script perbaikan..."
docker cp fix_labor_cost.php $CONTAINER_NAME:/tmp/fix_labor_cost.php

echo "Menjalankan script perbaikan..."
docker exec $CONTAINER_NAME php /tmp/fix_labor_cost.php

# Langkah 3: Upload dan jalankan script PHP untuk regenerasi laporan montir
echo ""
echo "=== LANGKAH 3: REGENERASI LAPORAN MONTIR ==="
echo "Mengupload script regenerasi..."
docker cp regenerate_reports.php $CONTAINER_NAME:/tmp/regenerate_reports.php

echo "Menjalankan script regenerasi..."
docker exec $CONTAINER_NAME php -f /tmp/regenerate_reports.php

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
