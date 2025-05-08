#!/bin/bash
set -e

echo "Memperbarui model Mechanic.php di server..."

# Cari nama container yang benar
CONTAINER_NAME=$(docker ps | grep -E 'app|laravel|php' | awk '{print $NF}' | head -1)

if [ -z "$CONTAINER_NAME" ]; then
    echo "Error: Tidak dapat menemukan container PHP/Laravel. Silakan cek container yang berjalan dengan 'docker ps'."
    exit 1
fi

echo "Menggunakan container: $CONTAINER_NAME"

# Buat backup file asli
echo "Membuat backup file Mechanic.php..."
docker exec $CONTAINER_NAME cp app/Models/Mechanic.php app/Models/Mechanic.php.bak

# Perbarui metode generateWeeklyReport
echo "Memperbarui metode generateWeeklyReport..."
docker exec $CONTAINER_NAME bash -c "cat > /tmp/mechanic_update.php << 'EOL'
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

# Ganti metode lama dengan yang baru menggunakan sed
echo "Menerapkan perubahan ke file Mechanic.php..."
docker exec $CONTAINER_NAME bash -c "sed -i '/public function generateWeeklyReport/,/^    }$/c\\
$(cat /tmp/mechanic_update.php)
' app/Models/Mechanic.php"

# Bersihkan cache Laravel
echo "Membersihkan cache Laravel..."
docker exec $CONTAINER_NAME php artisan cache:clear
docker exec $CONTAINER_NAME php artisan config:clear
docker exec $CONTAINER_NAME php artisan route:clear
docker exec $CONTAINER_NAME php artisan view:clear
docker exec $CONTAINER_NAME php artisan optimize

echo "Selesai! Model Mechanic.php telah diperbarui."
echo "Sekarang coba jalankan fix-duplicate-reports.sh terlebih dahulu, kemudian fix-mechanic-reports.sh."
