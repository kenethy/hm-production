#!/bin/bash
set -e

echo "Memperbaiki model Mechanic.php di server..."

# Cari nama container yang benar
CONTAINER_NAME=$(docker ps | grep -E 'app|laravel|php' | awk '{print $NF}' | head -1)

if [ -z "$CONTAINER_NAME" ]; then
    echo "Error: Tidak dapat menemukan container PHP/Laravel. Silakan cek container yang berjalan dengan 'docker ps'."
    exit 1
fi

echo "Menggunakan container: $CONTAINER_NAME"

# Buat backup file asli
echo "Membuat backup file Mechanic.php..."
docker exec $CONTAINER_NAME cp app/Models/Mechanic.php app/Models/Mechanic.php.bak.$(date +%Y%m%d%H%M%S)

# Buat file sementara dengan metode yang benar
echo "Menyiapkan metode generateWeeklyReport yang benar..."
docker exec $CONTAINER_NAME bash -c "cat > /tmp/correct_method.php << 'EOL'
    /**
     * Generate or update weekly report.
     */
    public function generateWeeklyReport(\$weekStart, \$weekEnd)
    {
        // Log untuk debugging
        \Illuminate\Support\Facades\Log::info(\"Generating weekly report for mechanic #{\$this->id} ({\$this->name})\");

        try {
            // Format dates to ensure consistency
            if (is_string(\$weekStart)) {
                \$weekStart = Carbon::parse(\$weekStart)->startOfDay();
            } elseif (\$weekStart instanceof Carbon) {
                \$weekStart = \$weekStart->copy()->startOfDay();
            }

            if (is_string(\$weekEnd)) {
                \$weekEnd = Carbon::parse(\$weekEnd)->endOfDay();
            } elseif (\$weekEnd instanceof Carbon) {
                \$weekEnd = \$weekEnd->copy()->endOfDay();
            }

            \Illuminate\Support\Facades\Log::info(\"Week period: {\$weekStart} to {\$weekEnd}\");

            // Count services and calculate labor cost
            \$servicesCount = \$this->countWeeklyServices(\$weekStart, \$weekEnd);
            \$totalLaborCost = \$this->calculateWeeklyLaborCost(\$weekStart, \$weekEnd);

            \Illuminate\Support\Facades\Log::info(\"Services count: {\$servicesCount}, Total labor cost: {\$totalLaborCost}\");

            // Use updateOrCreate to prevent duplicate entries
            \$report = \$this->reports()->updateOrCreate(
                [
                    'week_start' => \$weekStart,
                    'week_end' => \$weekEnd,
                ],
                [
                    'services_count' => \$servicesCount,
                    'total_labor_cost' => \$totalLaborCost,
                ]
            );

            \Illuminate\Support\Facades\Log::info(\"Report ID: {\$report->id}, Action: \" . (\$report->wasRecentlyCreated ? 'Created' : 'Updated'));
            
            return \$report;
        } catch (\Exception \$e) {
            \Illuminate\Support\Facades\Log::error(\"Error generating weekly report: \" . \$e->getMessage(), [
                'mechanic_id' => \$this->id,
                'week_start' => \$weekStart,
                'week_end' => \$weekEnd,
                'exception' => \$e
            ]);
            
            throw \$e;
        }
    }
EOL"

# Perbarui metode di file model
echo "Memperbarui metode generateWeeklyReport di file Mechanic.php..."
docker exec $CONTAINER_NAME bash -c "
# Cek apakah metode generateWeeklyReport ada
if grep -q 'function generateWeeklyReport' app/Models/Mechanic.php; then
    # Hapus metode yang ada dan ganti dengan yang baru
    sed -i '/public function generateWeeklyReport/,/^    }/c\\
$(cat /tmp/correct_method.php)' app/Models/Mechanic.php
    echo 'Metode generateWeeklyReport berhasil diperbarui.'
else
    # Tambahkan metode baru sebelum kurung kurawal penutup terakhir
    sed -i 's/}$/\\n$(cat /tmp/correct_method.php)\\n}/' app/Models/Mechanic.php
    echo 'Metode generateWeeklyReport berhasil ditambahkan.'
fi
"

# Bersihkan cache Laravel
echo "Membersihkan cache Laravel..."
docker exec $CONTAINER_NAME php artisan cache:clear
docker exec $CONTAINER_NAME php artisan config:clear
docker exec $CONTAINER_NAME php artisan route:clear
docker exec $CONTAINER_NAME php artisan view:clear
docker exec $CONTAINER_NAME php artisan optimize

echo "Selesai! Model Mechanic.php telah diperbarui."
