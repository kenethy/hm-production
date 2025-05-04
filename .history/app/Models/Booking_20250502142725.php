<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    protected $fillable = [
        'name',
        'phone',
        'car_model',
        'service_type',
        'date',
        'time',
        'message',
        'status',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    /**
     * Get the service associated with the booking.
     */
    public function service()
    {
        return $this->hasOne(Service::class);
    }
}
