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

        // Gunakan query builder untuk mendapatkan total labor_cost langsung dari database
        $totalLaborCost = \Illuminate\Support\Facades\DB::table('mechanic_service')
            ->where('mechanic_id', $this->id)
            ->where('week_start', $weekStart)
            ->where('week_end', $weekEnd)
            ->sum('labor_cost');

        // Log total biaya jasa dari database
        \Illuminate\Support\Facades\Log::info("Total labor cost from DB: {$totalLaborCost}");

        // Jika total biaya jasa dari database adalah 0, coba hitung secara manual
        if ($totalLaborCost == 0) {
            // Ambil semua servis untuk minggu ini
            $services = $this->services()
                ->wherePivot('week_start', $weekStart)
                ->wherePivot('week_end', $weekEnd)
                ->get();

            // Log jumlah servis yang ditemukan
            \Illuminate\Support\Facades\Log::info("Found " . $services->count() . " services");

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

            \Illuminate\Support\Facades\Log::info("Week period: {$weekStart} to {$weekEnd}");

            // Count services and calculate labor cost
            $servicesCount = $this->countWeeklyServices($weekStart, $weekEnd);
            $totalLaborCost = $this->calculateWeeklyLaborCost($weekStart, $weekEnd);

            \Illuminate\Support\Facades\Log::info("Services count: {$servicesCount}, Total labor cost: {$totalLaborCost}");

            // Periksa apakah ada servis di pivot table
            $pivotData = \Illuminate\Support\Facades\DB::table('mechanic_service')
                ->where('mechanic_id', $this->id)
                ->where('week_start', $weekStart)
                ->where('week_end', $weekEnd)
                ->get();

            \Illuminate\Support\Facades\Log::info("Pivot data count: " . $pivotData->count());

            // Try to find an existing report first
            $existingReport = $this->reports()
                ->where('week_start', $weekStart)
                ->where('week_end', $weekEnd)
                ->first();

            if ($existingReport) {
                // If report exists, update it
                $existingReport->services_count = $servicesCount;
                $existingReport->total_labor_cost = $totalLaborCost;
                $existingReport->save();

                $report = $existingReport;
                \Illuminate\Support\Facades\Log::info("Updated existing report ID: {$report->id}");
            } else {
                // If no report exists, try to create it with try-catch to handle race conditions
                try {
                    $report = $this->reports()->create([
                        'week_start' => $weekStart,
                        'week_end' => $weekEnd,
                        'services_count' => $servicesCount,
                        'total_labor_cost' => $totalLaborCost,
                    ]);

                    \Illuminate\Support\Facades\Log::info("Created new report ID: {$report->id}");
                } catch (\Illuminate\Database\QueryException $e) {
                    // Check if it's a duplicate entry error
                    if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
                        // If it's a duplicate entry error, find the report that was created in the meantime
                        \Illuminate\Support\Facades\Log::info("Duplicate entry detected, fetching existing report");

                        $report = $this->reports()
                            ->where('week_start', $weekStart)
                            ->where('week_end', $weekEnd)
                            ->first();

                        if ($report) {
                            // Update the existing report
                            $report->services_count = $servicesCount;
                            $report->total_labor_cost = $totalLaborCost;
                            $report->save();

                            \Illuminate\Support\Facades\Log::info("Updated existing report after duplicate entry ID: {$report->id}");
                        } else {
                            // This should not happen, but just in case
                            \Illuminate\Support\Facades\Log::error("Could not find existing report after duplicate entry error");
                            throw $e;
                        }
                    } else {
                        // If it's not a duplicate entry error, rethrow it
                        \Illuminate\Support\Facades\Log::error("Error creating report: " . $e->getMessage());
                        throw $e;
                    }
                }
            }

            return $report;
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Error generating weekly report: " . $e->getMessage(), [
                'mechanic_id' => $this->id,
                'week_start' => $weekStart,
                'week_end' => $weekEnd,
                'exception' => $e
            ]);

            throw $e;
        }
    }
}
