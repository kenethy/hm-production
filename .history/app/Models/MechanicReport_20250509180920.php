<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MechanicReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'mechanic_id',
        'week_start',
        'week_end',
        'services_count',
        'total_labor_cost',
        'notes',
        'is_paid',
        'paid_at',
    ];

    protected $casts = [
        'week_start' => 'date',
        'week_end' => 'date',
        'services_count' => 'integer',
        'total_labor_cost' => 'decimal:2',
        'is_paid' => 'boolean',
        'paid_at' => 'datetime',
    ];

    /**
     * Get the mechanic that owns the report.
     */
    public function mechanic(): BelongsTo
    {
        return $this->belongsTo(Mechanic::class);
    }

    /**
     * Scope a query to only include reports for a specific week.
     */
    public function scopeForWeek($query, $weekStart, $weekEnd)
    {
        return $query->where('week_start', $weekStart)
            ->where('week_end', $weekEnd);
    }

    /**
     * Scope a query to only include unpaid reports.
     */
    public function scopeUnpaid($query)
    {
        return $query->where('is_paid', false);
    }

    /**
     * Scope a query to only include paid reports.
     */
    public function scopePaid($query)
    {
        return $query->where('is_paid', true);
    }

    /**
     * Mark the report as paid.
     */
    public function markAsPaid()
    {
        $this->is_paid = true;
        $this->paid_at = now();
        $this->save();

        return $this;
    }

    /**
     * Get the services for this mechanic report.
     * This is a helper method, not a relationship.
     */
    public function services()
    {
        if (!$this->mechanic) {
            return collect();
        }

        return $this->mechanic->services()
            ->wherePivot('week_start', $this->week_start)
            ->wherePivot('week_end', $this->week_end);
    }
}
