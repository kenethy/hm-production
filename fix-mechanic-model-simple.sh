#!/bin/bash
set -e

echo "Memperbaiki model Mechanic.php di server dengan pendekatan sederhana..."

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

# Buat file PHP sementara di server untuk memperbaiki model
echo "Membuat file PHP sementara untuk perbaikan..."
docker exec $CONTAINER_NAME bash -c 'cat > /tmp/fix_mechanic_model.php << "EOF"
<?php

// Baca file Mechanic.php
$file = file_get_contents("app/Models/Mechanic.php");

// Cek apakah metode generateWeeklyReport ada
if (strpos($file, "function generateWeeklyReport") !== false) {
    // Hapus metode yang ada
    $pattern = "/public function generateWeeklyReport.*?\\n    \\}/s";
    $file = preg_replace($pattern, "", $file);
}

// Tambahkan metode baru sebelum kurung kurawal penutup terakhir
$newMethod = <<<EOT
    /**
     * Generate or update weekly report.
     */
    public function generateWeeklyReport(\$weekStart, \$weekEnd)
    {
        // Log untuk debugging
        \\Illuminate\\Support\\Facades\\Log::info("Generating weekly report for mechanic #{\$this->id} ({\$this->name})");

        try {
            // Format dates to ensure consistency
            if (is_string(\$weekStart)) {
                \$weekStart = \\Carbon\\Carbon::parse(\$weekStart)->startOfDay();
            } elseif (\$weekStart instanceof \\Carbon\\Carbon) {
                \$weekStart = \$weekStart->copy()->startOfDay();
            }

            if (is_string(\$weekEnd)) {
                \$weekEnd = \\Carbon\\Carbon::parse(\$weekEnd)->endOfDay();
            } elseif (\$weekEnd instanceof \\Carbon\\Carbon) {
                \$weekEnd = \$weekEnd->copy()->endOfDay();
            }

            \\Illuminate\\Support\\Facades\\Log::info("Week period: {\$weekStart} to {\$weekEnd}");

            // Count services and calculate labor cost
            \$servicesCount = \$this->countWeeklyServices(\$weekStart, \$weekEnd);
            \$totalLaborCost = \$this->calculateWeeklyLaborCost(\$weekStart, \$weekEnd);

            \\Illuminate\\Support\\Facades\\Log::info("Services count: {\$servicesCount}, Total labor cost: {\$totalLaborCost}");

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

            \\Illuminate\\Support\\Facades\\Log::info("Report ID: {\$report->id}, Action: " . (\$report->wasRecentlyCreated ? 'Created' : 'Updated'));
            
            return \$report;
        } catch (\\Exception \$e) {
            \\Illuminate\\Support\\Facades\\Log::error("Error generating weekly report: " . \$e->getMessage(), [
                'mechanic_id' => \$this->id,
                'week_start' => \$weekStart,
                'week_end' => \$weekEnd,
                'exception' => \$e
            ]);
            
            throw \$e;
        }
    }
EOT;

// Tambahkan metode baru sebelum kurung kurawal penutup terakhir
$file = preg_replace("/}\\s*$/", "$newMethod\n}\n", $file);

// Simpan file yang sudah dimodifikasi
file_put_contents("app/Models/Mechanic.php", $file);

echo "File Mechanic.php berhasil diperbarui.";
EOF'

# Jalankan script PHP
echo "Menjalankan script perbaikan..."
docker exec $CONTAINER_NAME php /tmp/fix_mechanic_model.php

# Bersihkan cache Laravel
echo "Membersihkan cache Laravel..."
docker exec $CONTAINER_NAME php artisan cache:clear
docker exec $CONTAINER_NAME php artisan config:clear
docker exec $CONTAINER_NAME php artisan route:clear
docker exec $CONTAINER_NAME php artisan view:clear
docker exec $CONTAINER_NAME php artisan optimize

echo "Selesai! Model Mechanic.php telah diperbarui."
