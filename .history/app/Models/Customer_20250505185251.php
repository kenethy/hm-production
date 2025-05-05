<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'phone',
        'email',
        'address',
        'city',
        'birth_date',
        'gender',
        'notes',
        'source',
        'is_active',
        'last_service_date',
        'service_count',
        'total_spent',
    ];

    protected $casts = [
        'birth_date' => 'date',
        'last_service_date' => 'date',
        'is_active' => 'boolean',
        'service_count' => 'integer',
        'total_spent' => 'decimal:2',
    ];

    /**
     * Get all services for this customer.
     */
    public function services()
    {
        return $this->hasMany(Service::class);
    }

    /**
     * Get all bookings for this customer.
     */
    public function bookings()
    {
        return $this->hasMany(Booking::class, 'phone', 'phone');
    }

    /**
     * Update customer statistics based on services.
     */
    public function updateStatistics()
    {
        $services = $this->services;

        $this->service_count = $services->count();
        $this->total_spent = $services->sum('total_cost');

        if ($services->count() > 0) {
            $this->last_service_date = $services->sortByDesc('created_at')->first()->created_at;
        }

        $this->save();
    }
}
