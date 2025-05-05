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
            ->withPivot('notes', 'labor_cost', 'week_start', 'week_end')
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
        return $this->services()
            ->wherePivot('week_start', $weekStart)
            ->wherePivot('week_end', $weekEnd)
            ->sum('mechanic_service.labor_cost');
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
        // Format dates to ensure consistency
        if (is_string($weekStart)) {
            $weekStart = Carbon\Carbon::parse($weekStart)->startOfDay();
        }

        if (is_string($weekEnd)) {
            $weekEnd = Carbon\Carbon::parse($weekEnd)->endOfDay();
        }

        // Check if report already exists
        $existingReport = $this->reports()
            ->where('week_start', $weekStart)
            ->where('week_end', $weekEnd)
            ->first();

        // Count services and calculate labor cost
        $servicesCount = $this->countWeeklyServices($weekStart, $weekEnd);
        $totalLaborCost = $this->calculateWeeklyLaborCost($weekStart, $weekEnd);

        if ($existingReport) {
            // Update existing report
            $existingReport->update([
                'services_count' => $servicesCount,
                'total_labor_cost' => $totalLaborCost,
            ]);

            return $existingReport;
        } else {
            // Create new report only if there are services or labor costs
            if ($servicesCount > 0 || $totalLaborCost > 0) {
                return $this->reports()->create([
                    'week_start' => $weekStart,
                    'week_end' => $weekEnd,
                    'services_count' => $servicesCount,
                    'total_labor_cost' => $totalLaborCost,
                ]);
            }

            // Return null if no report was created
            return null;
        }
    }
}
