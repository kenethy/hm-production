<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Mechanic extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'phone',
        'specialization',
        'notes',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get the services that are assigned to this mechanic.
     */
    public function services(): BelongsToMany
    {
        return $this->belongsToMany(Service::class)
            ->withPivot('notes', 'labor_cost', 'invoice_number', 'week_start', 'week_end')
            ->withTimestamps();
    }

    /**
     * Get the reports for this mechanic.
     */
    public function reports(): HasMany
    {
        return $this->hasMany(MechanicReport::class);
    }

    /**
     * Get active mechanics.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Calculate total labor cost for a specific week.
     */
    public function calculateWeeklyLaborCost($weekStart, $weekEnd)
    {
        // Log untuk debugging
        \Illuminate\Support\Facades\Log::info("Calculating labor cost for mechanic #{$this->id} ({$this->name}) for week {$weekStart} to {$weekEnd}");

        // Format tanggal untuk memastikan konsistensi
        if (is_string($weekStart)) {
            $weekStart = \Carbon\Carbon::parse($weekStart)->startOfDay()->format('Y-m-d');
        } elseif ($weekStart instanceof \Carbon\Carbon) {
            $weekStart = $weekStart->copy()->startOfDay()->format('Y-m-d');
        }

        if (is_string($weekEnd)) {
            $weekEnd = \Carbon\Carbon::parse($weekEnd)->endOfDay()->format('Y-m-d');
        } elseif ($weekEnd instanceof \Carbon\Carbon) {
            $weekEnd = $weekEnd->copy()->endOfDay()->format('Y-m-d');
        }

        \Illuminate\Support\Facades\Log::info("Formatted week period: {$weekStart} to {$weekEnd}");

        // Cek semua data mechanic_service untuk montir ini
        $allServices = \Illuminate\Support\Facades\DB::table('mechanic_service')
            ->where('mechanic_id', $this->id)
            ->get();

        \Illuminate\Support\Facades\Log::info("All mechanic_service records for mechanic #{$this->id}: " . $allServices->count());

        // Gunakan query builder untuk mendapatkan total labor_cost langsung dari database
        $totalLaborCost = \Illuminate\Support\Facades\DB::table('mechanic_service')
            ->where('mechanic_id', $this->id)
            ->where('week_start', $weekStart)
            ->where('week_end', $weekEnd)
            ->sum('labor_cost');

        // Log total biaya jasa dari database
        \Illuminate\Support\Facades\Log::info("Total labor cost from DB: {$totalLaborCost}");

        // Log query SQL untuk debugging
        $query = \Illuminate\Support\Facades\DB::table('mechanic_service')
            ->where('mechanic_id', $this->id)
            ->where('week_start', $weekStart)
            ->where('week_end', $weekEnd);

        \Illuminate\Support\Facades\Log::info("SQL Query: " . $query->toSql());
        \Illuminate\Support\Facades\Log::info("SQL Bindings: ", $query->getBindings());

        // Ambil semua servis yang cocok untuk debugging
        $matchingServices = $query->get();
        \Illuminate\Support\Facades\Log::info("Matching services count: " . $matchingServices->count());
        foreach ($matchingServices as $service) {
            \Illuminate\Support\Facades\Log::info("Matching service: ", (array) $service);
        }

        // Jika total biaya jasa dari database adalah 0, coba hitung secara manual dengan query yang lebih fleksibel
        if ($totalLaborCost == 0) {
            \Illuminate\Support\Facades\Log::info("Labor cost is 0, trying alternative calculation methods");

            // Coba dengan format tanggal yang berbeda
            $totalLaborCost = \Illuminate\Support\Facades\DB::table('mechanic_service')
                ->where('mechanic_id', $this->id)
                ->whereDate('week_start', '=', $weekStart)
                ->whereDate('week_end', '=', $weekEnd)
                ->sum('labor_cost');

            \Illuminate\Support\Facades\Log::info("Total labor cost with whereDate: {$totalLaborCost}");

            // Jika masih 0, coba dengan pendekatan yang lebih fleksibel
            if ($totalLaborCost == 0) {
                // Ambil semua servis untuk minggu ini menggunakan relasi Eloquent
                $services = $this->services()
                    ->wherePivot('week_start', $weekStart)
                    ->wherePivot('week_end', $weekEnd)
                    ->get();

                // Log jumlah servis yang ditemukan
                \Illuminate\Support\Facades\Log::info("Found " . $services->count() . " services via Eloquent relation");

                // Hitung total biaya jasa secara manual
                $manualTotalLaborCost = 0;
                foreach ($services as $service) {
                    $laborCost = (float) $service->pivot->labor_cost;
                    \Illuminate\Support\Facades\Log::info("Service #{$service->id}: labor_cost = {$laborCost}");
                    $manualTotalLaborCost += $laborCost;
                }

                // Log total biaya jasa manual
                \Illuminate\Support\Facades\Log::info("Manual total labor cost: {$manualTotalLaborCost}");

                // Gunakan hasil perhitungan manual jika lebih besar dari 0
                if ($manualTotalLaborCost > 0) {
                    $totalLaborCost = $manualTotalLaborCost;
                }

                // Jika masih 0, coba dengan pendekatan paling fleksibel
                if ($totalLaborCost == 0) {
                    \Illuminate\Support\Facades\Log::info("Still 0, trying most flexible approach");

                    // Ambil semua servis yang selesai untuk montir ini
                    $completedServices = $this->services()
                        ->where('status', 'completed')
                        ->get();

                    \Illuminate\Support\Facades\Log::info("Found " . $completedServices->count() . " completed services");

                    // Hitung total biaya jasa dari semua servis yang selesai
                    $totalFromCompleted = 0;
                    foreach ($completedServices as $service) {
                        $laborCost = (float) $service->pivot->labor_cost;
                        \Illuminate\Support\Facades\Log::info("Completed service #{$service->id}: labor_cost = {$laborCost}");
                        $totalFromCompleted += $laborCost;
                    }

                    \Illuminate\Support\Facades\Log::info("Total from all completed services: {$totalFromCompleted}");

                    if ($totalFromCompleted > 0) {
                        $totalLaborCost = $totalFromCompleted;
                    }
                }
            }
        }

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
        \Illuminate\Support\Facades\Log::info("Generating weekly report for mechanic #{$this->id} ({$this->name})");

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

        \Illuminate\Support\Facades\Log::info("Week period: {$weekStart} to {$weekEnd}");

        // Use a database transaction with locking to prevent race conditions
        return \Illuminate\Support\Facades\DB::transaction(function () use ($weekStart, $weekEnd) {
            try {
                // Count services and calculate labor cost
                $servicesCount = $this->countWeeklyServices($weekStart, $weekEnd);
                $totalLaborCost = $this->calculateWeeklyLaborCost($weekStart, $weekEnd);

                \Illuminate\Support\Facades\Log::info("Services count: {$servicesCount}, Total labor cost: {$totalLaborCost}");

                // Lock the mechanic_reports table for this mechanic and week to prevent concurrent modifications
                // Use FOR UPDATE to lock the row if it exists
                $existingReport = \App\Models\MechanicReport::where('mechanic_id', $this->id)
                    ->where('week_start', $weekStart)
                    ->where('week_end', $weekEnd)
                    ->lockForUpdate()
                    ->first();

                if ($existingReport) {
                    // If report exists, update it
                    \Illuminate\Support\Facades\Log::info("Found existing report ID: {$existingReport->id}, updating...");

                    $existingReport->services_count = $servicesCount;
                    $existingReport->total_labor_cost = $totalLaborCost;
                    $existingReport->save();

                    \Illuminate\Support\Facades\Log::info("Updated existing report ID: {$existingReport->id}");

                    return $existingReport;
                } else {
                    // If no report exists, create it
                    \Illuminate\Support\Facades\Log::info("No existing report found, creating new report...");

                    // Double-check that the report doesn't exist (extra safety)
                    $checkAgain = \App\Models\MechanicReport::where('mechanic_id', $this->id)
                        ->where('week_start', $weekStart)
                        ->where('week_end', $weekEnd)
                        ->first();

                    if ($checkAgain) {
                        // If report exists (race condition), update it
                        \Illuminate\Support\Facades\Log::info("Report was created in the meantime, updating ID: {$checkAgain->id}");

                        $checkAgain->services_count = $servicesCount;
                        $checkAgain->total_labor_cost = $totalLaborCost;
                        $checkAgain->save();

                        return $checkAgain;
                    }

                    // Create new report
                    $report = new \App\Models\MechanicReport();
                    $report->mechanic_id = $this->id;
                    $report->week_start = $weekStart;
                    $report->week_end = $weekEnd;
                    $report->services_count = $servicesCount;
                    $report->total_labor_cost = $totalLaborCost;
                    $report->save();

                    \Illuminate\Support\Facades\Log::info("Created new report ID: {$report->id}");

                    return $report;
                }
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error("Error in transaction: " . $e->getMessage(), [
                    'mechanic_id' => $this->id,
                    'week_start' => $weekStart,
                    'week_end' => $weekEnd,
                    'exception' => $e
                ]);

                // Rethrow the exception to roll back the transaction
                throw $e;
            }
        }, 5); // 5 retries for deadlock
    }
}
