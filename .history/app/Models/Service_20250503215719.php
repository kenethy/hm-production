<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Service extends Model
{
    protected $fillable = [
        'booking_id',
        'customer_name',
        'phone',
        'car_model',
        'license_plate',
        'service_type',
        'description',
        'parts_used',
        'labor_cost',
        'parts_cost',
        'total_cost',
        'status',
        'notes',
        'completed_at',
    ];

    protected $casts = [
        'labor_cost' => 'decimal:2',
        'parts_cost' => 'decimal:2',
        'total_cost' => 'decimal:2',
        'completed_at' => 'datetime',
    ];

    /**
     * Get the booking associated with the service.
     */
    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    /**
     * Calculate the total cost of the service.
     */
    public function calculateTotalCost()
    {
        $this->total_cost = $this->labor_cost + $this->parts_cost;
        return $this->total_cost;
    }

    /**
     * Get the updates for the service.
     */
    public function updates()
    {
        return $this->hasMany(ServiceUpdate::class);
    }
}
