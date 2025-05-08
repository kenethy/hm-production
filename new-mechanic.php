<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

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
        Log::info("Calculating labor cost for mechanic #{$this->id} ({$this->name}) for week {$weekStart} to {$weekEnd}");

        // Ambil semua servis untuk minggu ini
        $services = $this->services()
            ->wherePivot('week_start', $weekStart)
            ->wherePivot('week_end', $weekEnd)
            ->get();

        // Log jumlah servis yang ditemukan
        Log::info("Found " . $services->count() . " services");

        // Hitung total biaya jasa secara manual
        $totalLaborCost = 0;
        foreach ($services as $service) {
            $laborCost = (float) $service->pivot->labor_cost;
            Log::info("Service #{$service->id}: labor_cost = {$laborCost}");
            $totalLaborCost += $laborCost;
        }

        // Log total biaya jasa
        Log::info("Total labor cost: {$totalLaborCost}");

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
        try {
            // Log untuk debugging
            Log::info("Generating weekly report for mechanic #{$this->id} ({$this->name})");

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

            Log::info("Week period: {$weekStart} to {$weekEnd}");

            // Count services and calculate labor cost
            $servicesCount = $this->countWeeklyServices($weekStart, $weekEnd);
            $totalLaborCost = $this->calculateWeeklyLaborCost($weekStart, $weekEnd);

            Log::info("Services count: {$servicesCount}, Total labor cost: {$totalLaborCost}");

            // Cek apakah laporan sudah ada
            $existingReport = DB::table('mechanic_reports')
                ->where('mechanic_id', $this->id)
                ->where('week_start', $weekStart)
                ->where('week_end', $weekEnd)
                ->first();

            if ($existingReport) {
                // Update laporan yang sudah ada
                Log::info("Updating existing report ID: {$existingReport->id}");
                
                DB::table('mechanic_reports')
                    ->where('id', $existingReport->id)
                    ->update([
                        'services_count' => $servicesCount,
                        'total_labor_cost' => $totalLaborCost,
                        'updated_at' => now()
                    ]);
                
                // Ambil laporan yang sudah diupdate
                $report = MechanicReport::find($existingReport->id);
                Log::info("Updated report ID: {$report->id}");
                
                return $report;
            } else {
                // Buat laporan baru
                Log::info("Creating new report");
                
                $report = new MechanicReport();
                $report->mechanic_id = $this->id;
                $report->week_start = $weekStart;
                $report->week_end = $weekEnd;
                $report->services_count = $servicesCount;
                $report->total_labor_cost = $totalLaborCost;
                $report->save();
                
                Log::info("Created new report ID: {$report->id}");
                
                return $report;
            }
        } catch (\Exception $e) {
            Log::error("Error generating weekly report: " . $e->getMessage(), [
                'mechanic_id' => $this->id,
                'week_start' => $weekStart,
                'week_end' => $weekEnd,
                'exception' => $e
            ]);
            
            throw $e;
        }
    }
}
