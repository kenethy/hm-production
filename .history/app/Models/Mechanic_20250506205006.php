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

        // Ambil semua servis untuk minggu ini
        $services = $this->services()
            ->wherePivot('week_start', $weekStart)
            ->wherePivot('week_end', $weekEnd)
            ->get();

        // Log jumlah servis yang ditemukan
        \Illuminate\Support\Facades\Log::info("Found " . $services->count() . " services");

        // Hitung total biaya jasa secara manual
        $totalLaborCost = 0;
        foreach ($services as $service) {
            $laborCost = (float) $service->pivot->labor_cost;
            \Illuminate\Support\Facades\Log::info("Service #{$service->id}: labor_cost = {$laborCost}");
            $totalLaborCost += $laborCost;
        }

        // Log total biaya jasa
        \Illuminate\Support\Facades\Log::info("Total labor cost: {$totalLaborCost}");

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
        }

        if (is_string($weekEnd)) {
            $weekEnd = Carbon::parse($weekEnd)->endOfDay();
        }

        \Illuminate\Support\Facades\Log::info("Week period: {$weekStart} to {$weekEnd}");

        // Check if report already exists
        $existingReport = $this->reports()
            ->where('week_start', $weekStart)
            ->where('week_end', $weekEnd)
            ->first();

        \Illuminate\Support\Facades\Log::info("Existing report: " . ($existingReport ? "Yes (ID: {$existingReport->id})" : "No"));

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
        foreach ($pivotData as $pivot) {
            \Illuminate\Support\Facades\Log::info("Pivot: mechanic_id={$pivot->mechanic_id}, service_id={$pivot->service_id}, labor_cost={$pivot->labor_cost}");
        }

        if ($existingReport) {
            // Update existing report
            $existingReport->update([
                'services_count' => $servicesCount,
                'total_labor_cost' => $totalLaborCost,
            ]);

            \Illuminate\Support\Facades\Log::info("Updated existing report ID: {$existingReport->id}");

            return $existingReport;
        } else {
            // Create new report only if there are services or labor costs
            if ($servicesCount > 0 || $totalLaborCost > 0) {
                $report = $this->reports()->create([
                    'week_start' => $weekStart,
                    'week_end' => $weekEnd,
                    'services_count' => $servicesCount,
                    'total_labor_cost' => $totalLaborCost,
                ]);

                \Illuminate\Support\Facades\Log::info("Created new report ID: {$report->id}");

                return $report;
            } else {
                // Jika tidak ada servis atau biaya jasa, tetap buat laporan dengan nilai 0
                // Ini untuk memastikan laporan tetap muncul di halaman Rekap Montirs
                $report = $this->reports()->create([
                    'week_start' => $weekStart,
                    'week_end' => $weekEnd,
                    'services_count' => 0,
                    'total_labor_cost' => 0,
                ]);

                \Illuminate\Support\Facades\Log::info("Created empty report ID: {$report->id}");

                return $report;
            }
        }
    }
}
