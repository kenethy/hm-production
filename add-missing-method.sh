#!/bin/bash
set -e

echo "Menambahkan metode generateWeeklyReport yang hilang ke model Mechanic.php..."

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

# Buat metode yang akan ditambahkan
echo "Menyiapkan metode generateWeeklyReport..."
docker exec $CONTAINER_NAME bash -c "cat > /tmp/missing_method.php << 'EOL'
    /**
     * Calculate total labor cost for a specific week.
     */
    public function calculateWeeklyLaborCost($weekStart, $weekEnd)
    {
        // Log untuk debugging
        \Illuminate\Support\Facades\Log::info(\"Calculating labor cost for mechanic #{$this->id} ({$this->name}) for week {$weekStart} to {$weekEnd}\");

        // Ambil semua servis untuk minggu ini
        $services = $this->services()
            ->wherePivot('week_start', $weekStart)
            ->wherePivot('week_end', $weekEnd)
            ->get();

        // Log jumlah servis yang ditemukan
        \Illuminate\Support\Facades\Log::info(\"Found \" . $services->count() . \" services\");

        // Hitung total biaya jasa secara manual
        $totalLaborCost = 0;
        foreach ($services as $service) {
            $laborCost = (float) $service->pivot->labor_cost;
            \Illuminate\Support\Facades\Log::info(\"Service #{$service->id}: labor_cost = {$laborCost}\");
            $totalLaborCost += $laborCost;
        }

        // Log total biaya jasa
        \Illuminate\Support\Facades\Log::info(\"Total labor cost: {$totalLaborCost}\");

        return $totalLaborCost;
    }

    /**
     * Count services for a specific week.
     */
    public function countWeeklyServices($weekStart, $weekEnd)
    {
        return $this->services()
            ->wherePivot('week_start', $weekStart)
            ->wherePivot('week_end', $weekEnd)
            ->count();
    }

    /**
     * Generate or update weekly report.
     */
    public function generateWeeklyReport($weekStart, $weekEnd)
    {
        // Log untuk debugging
        \Illuminate\Support\Facades\Log::info(\"Generating weekly report for mechanic #{$this->id} ({$this->name})\");

        try {
            // Format dates to ensure consistency
            if (is_string($weekStart)) {
                $weekStart = Carbon::parse($weekStart)->startOfDay();
            } elseif ($weekStart instanceof Carbon) {
                $weekStart = $weekStart->copy()->startOfDay();
            }

            if (is_string($weekEnd)) {
                $weekEnd = Carbon::parse($weekEnd)->endOfDay();
            } elseif ($weekEnd instanceof Carbon) {
                $weekEnd = $weekEnd->copy()->endOfDay();
            }

            \Illuminate\Support\Facades\Log::info(\"Week period: {$weekStart} to {$weekEnd}\");

            // Count services and calculate labor cost
            $servicesCount = $this->countWeeklyServices($weekStart, $weekEnd);
            $totalLaborCost = $this->calculateWeeklyLaborCost($weekStart, $weekEnd);

            \Illuminate\Support\Facades\Log::info(\"Services count: {$servicesCount}, Total labor cost: {$totalLaborCost}\");

            // Use updateOrCreate to prevent duplicate entries
            $report = $this->reports()->updateOrCreate(
                [
                    'week_start' => $weekStart,
                    'week_end' => $weekEnd,
                ],
                [
                    'services_count' => $servicesCount,
                    'total_labor_cost' => $totalLaborCost,
                ]
            );

            \Illuminate\Support\Facades\Log::info(\"Report ID: {$report->id}, Action: \" . ($report->wasRecentlyCreated ? 'Created' : 'Updated'));
            
            return $report;
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error(\"Error generating weekly report: \" . $e->getMessage(), [
                'mechanic_id' => $this->id,
                'week_start' => $weekStart,
                'week_end' => $weekEnd,
                'exception' => $e
            ]);
            
            throw $e;
        }
    }
EOL"

# Tambahkan metode ke file model
echo "Menambahkan metode ke file Mechanic.php..."
docker exec $CONTAINER_NAME bash -c "
# Periksa apakah file sudah memiliki metode calculateWeeklyLaborCost
if ! grep -q 'function calculateWeeklyLaborCost' app/Models/Mechanic.php; then
    # Tambahkan metode sebelum kurung kurawal penutup terakhir
    sed -i 's/}$/\n\n$(cat /tmp/missing_method.php)\n}/' app/Models/Mechanic.php
else
    echo 'Metode calculateWeeklyLaborCost sudah ada, hanya menambahkan generateWeeklyReport'
    # Hapus metode generateWeeklyReport yang ada (jika ada)
    sed -i '/public function generateWeeklyReport/,/^    }/d' app/Models/Mechanic.php
    # Tambahkan metode generateWeeklyReport baru sebelum kurung kurawal penutup terakhir
    sed -i 's/}$/\n\n    \/\*\*\n     \* Generate or update weekly report.\n     \*\/\n    public function generateWeeklyReport($weekStart, $weekEnd)\n    {\n        \/\/ Log untuk debugging\n        \\Illuminate\\Support\\Facades\\Log::info(\"Generating weekly report for mechanic #{$this->id} ({$this->name})\");\n\n        try {\n            \/\/ Format dates to ensure consistency\n            if (is_string($weekStart)) {\n                $weekStart = Carbon::parse($weekStart)->startOfDay();\n            } elseif ($weekStart instanceof Carbon) {\n                $weekStart = $weekStart->copy()->startOfDay();\n            }\n\n            if (is_string($weekEnd)) {\n                $weekEnd = Carbon::parse($weekEnd)->endOfDay();\n            } elseif ($weekEnd instanceof Carbon) {\n                $weekEnd = $weekEnd->copy()->endOfDay();\n            }\n\n            \\Illuminate\\Support\\Facades\\Log::info(\"Week period: {$weekStart} to {$weekEnd}\");\n\n            \/\/ Count services and calculate labor cost\n            $servicesCount = $this->countWeeklyServices($weekStart, $weekEnd);\n            $totalLaborCost = $this->calculateWeeklyLaborCost($weekStart, $weekEnd);\n\n            \\Illuminate\\Support\\Facades\\Log::info(\"Services count: {$servicesCount}, Total labor cost: {$totalLaborCost}\");\n\n            \/\/ Use updateOrCreate to prevent duplicate entries\n            $report = $this->reports()->updateOrCreate(\n                [\n                    \'week_start\' => $weekStart,\n                    \'week_end\' => $weekEnd,\n                ],\n                [\n                    \'services_count\' => $servicesCount,\n                    \'total_labor_cost\' => $totalLaborCost,\n                ]\n            );\n\n            \\Illuminate\\Support\\Facades\\Log::info(\"Report ID: {$report->id}, Action: \" . ($report->wasRecentlyCreated ? \'Created\' : \'Updated\'));\n            \n            return $report;\n        } catch (\\Exception $e) {\n            \\Illuminate\\Support\\Facades\\Log::error(\"Error generating weekly report: \" . $e->getMessage(), [\n                \'mechanic_id\' => $this->id,\n                \'week_start\' => $weekStart,\n                \'week_end\' => $weekEnd,\n                \'exception\' => $e\n            ]);\n            \n            throw $e;\n        }\n    }\n}/' app/Models/Mechanic.php
fi
"

# Bersihkan cache Laravel
echo "Membersihkan cache Laravel..."
docker exec $CONTAINER_NAME php artisan cache:clear
docker exec $CONTAINER_NAME php artisan config:clear
docker exec $CONTAINER_NAME php artisan route:clear
docker exec $CONTAINER_NAME php artisan view:clear
docker exec $CONTAINER_NAME php artisan optimize

echo "Selesai! Metode generateWeeklyReport telah ditambahkan ke model Mechanic.php."
echo "Sekarang coba jalankan fix-mechanic-reports.sh lagi."
