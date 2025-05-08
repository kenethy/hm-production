<?php
// Script untuk memperbaiki metode calculateWeeklyLaborCost di Mechanic.php

// Baca file Mechanic.php
$file = file_get_contents("/var/www/html/app/Models/Mechanic.php");

// Cari posisi metode calculateWeeklyLaborCost
$startPos = strpos($file, "public function calculateWeeklyLaborCost");
if ($startPos === false) {
    echo "Error: Metode calculateWeeklyLaborCost tidak ditemukan!";
    exit(1);
}

// Cari akhir metode
$braceCount = 0;
$endPos = $startPos;
$inMethod = false;
for ($i = $startPos; $i < strlen($file); $i++) {
    if ($file[$i] == "{") {
        $braceCount++;
        $inMethod = true;
    } elseif ($file[$i] == "}") {
        $braceCount--;
        if ($inMethod && $braceCount == 0) {
            $endPos = $i + 1;
            break;
        }
    }
}

// Metode baru
$newMethod = <<<'EOT'
public function calculateWeeklyLaborCost($weekStart, $weekEnd)
    {
        // Log untuk debugging
        \Illuminate\Support\Facades\Log::info("Calculating labor cost for mechanic #{$this->id} ({$this->name}) for week {$weekStart} to {$weekEnd}");

        // Gunakan query builder untuk mendapatkan total labor_cost langsung dari database
        $totalLaborCost = \Illuminate\Support\Facades\DB::table("mechanic_service")
            ->where("mechanic_id", $this->id)
            ->where("week_start", $weekStart)
            ->where("week_end", $weekEnd)
            ->sum("labor_cost");

        // Log total biaya jasa
        \Illuminate\Support\Facades\Log::info("Total labor cost (from DB): {$totalLaborCost}");

        // Jika totalLaborCost masih 0, coba cara lain
        if ($totalLaborCost == 0) {
            // Ambil semua servis untuk minggu ini
            $services = $this->services()
                ->wherePivot("week_start", $weekStart)
                ->wherePivot("week_end", $weekEnd)
                ->get();

            // Log jumlah servis yang ditemukan
            \Illuminate\Support\Facades\Log::info("Found " . $services->count() . " services");

            // Hitung total biaya jasa secara manual
            $manualTotal = 0;
            foreach ($services as $service) {
                $laborCost = (float) $service->pivot->labor_cost;
                \Illuminate\Support\Facades\Log::info("Service #{$service->id}: labor_cost = {$laborCost}");
                $manualTotal += $laborCost;
            }

            \Illuminate\Support\Facades\Log::info("Total labor cost (manual calculation): {$manualTotal}");
            
            // Gunakan hasil perhitungan manual jika lebih besar dari 0
            if ($manualTotal > 0) {
                $totalLaborCost = $manualTotal;
            }
        }

        return $totalLaborCost;
    }
EOT;

// Ganti metode lama dengan yang baru
$newFile = substr($file, 0, $startPos) . $newMethod . substr($file, $endPos);

// Simpan file yang sudah dimodifikasi
file_put_contents("/var/www/html/app/Models/Mechanic.php", $newFile);

echo "Metode calculateWeeklyLaborCost berhasil diperbarui.";
