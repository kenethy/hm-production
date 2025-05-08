#!/bin/bash
set -e

echo "Memperbaiki ServiceResource.php di server..."

# Cari nama container yang benar
CONTAINER_NAME=$(docker ps | grep -E 'app|laravel|php' | awk '{print $NF}' | head -1)

if [ -z "$CONTAINER_NAME" ]; then
    echo "Error: Tidak dapat menemukan container PHP/Laravel. Silakan cek container yang berjalan dengan 'docker ps'."
    exit 1
fi

echo "Menggunakan container: $CONTAINER_NAME"

# Buat backup file asli
echo "Membuat backup file ServiceResource.php..."
docker exec $CONTAINER_NAME cp app/Filament/Resources/ServiceResource.php app/Filament/Resources/ServiceResource.php.bak.$(date +%Y%m%d%H%M%S)

# Buat file sementara dengan perbaikan untuk panggilan generateWeeklyReport
echo "Menyiapkan perbaikan untuk panggilan generateWeeklyReport..."
docker exec $CONTAINER_NAME bash -c "cat > /tmp/fixed_generate_call.php << 'EOL'
                                    // Generate atau update laporan mingguan montir
                                    try {
                                        $mechanic = Mechanic::find($mechanicId);
                                        if ($mechanic) {
                                            $mechanic->generateWeeklyReport($weekStart, $weekEnd);
                                        }
                                    } catch (\Exception $e) {
                                        \Illuminate\Support\Facades\Log::error('Error generating weekly report: ' . $e->getMessage(), [
                                            'mechanic_id' => $mechanicId,
                                            'week_start' => $weekStart,
                                            'week_end' => $weekEnd
                                        ]);
                                    }
EOL"

# Perbarui semua panggilan ke generateWeeklyReport di ServiceResource.php
echo "Memperbarui panggilan ke generateWeeklyReport di ServiceResource.php..."
docker exec $CONTAINER_NAME bash -c "
# Cari semua baris yang memanggil generateWeeklyReport
LINES=\$(grep -n \"generateWeeklyReport\" app/Filament/Resources/ServiceResource.php | cut -d: -f1)

# Untuk setiap baris, ganti dengan versi yang diperbaiki
for LINE in \$LINES; do
    # Ambil baris asli
    ORIGINAL_LINE=\$(sed -n \"\${LINE}p\" app/Filament/Resources/ServiceResource.php)
    
    # Cek apakah baris ini adalah panggilan langsung ke generateWeeklyReport
    if [[ \$ORIGINAL_LINE == *\"generateWeeklyReport\"* && \$ORIGINAL_LINE == *\"mechanic->\"* ]]; then
        # Ambil indentasi dari baris asli
        INDENT=\$(echo \"\$ORIGINAL_LINE\" | sed -E 's/^([[:space:]]*).*/\\1/')
        
        # Ambil beberapa baris sebelumnya untuk konteks
        PREV_LINES=\$(sed -n \"\$((LINE-5)),\${LINE}p\" app/Filament/Resources/ServiceResource.php)
        
        # Cek apakah ini adalah bagian yang kita ingin perbaiki
        if [[ \$PREV_LINES == *\"mechanics()->attach\"* || \$PREV_LINES == *\"mechanics()->updateExistingPivot\"* ]]; then
            # Hapus baris asli dan ganti dengan versi yang diperbaiki
            sed -i \"\${LINE}s|.*|\${INDENT}$(cat /tmp/fixed_generate_call.php | sed 's/^/                                    /')|\\" app/Filament/Resources/ServiceResource.php
            echo \"Memperbaiki panggilan generateWeeklyReport pada baris \$LINE\"
        fi
    fi
done
"

# Bersihkan cache Laravel
echo "Membersihkan cache Laravel..."
docker exec $CONTAINER_NAME php artisan cache:clear
docker exec $CONTAINER_NAME php artisan config:clear
docker exec $CONTAINER_NAME php artisan route:clear
docker exec $CONTAINER_NAME php artisan view:clear
docker exec $CONTAINER_NAME php artisan optimize

echo "Selesai! ServiceResource.php telah diperbarui."
