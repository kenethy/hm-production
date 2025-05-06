<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Vehicle extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'customer_id',
        'model',
        'license_plate',
        'year',
        'color',
        'vin',
        'engine_number',
        'transmission',
        'fuel_type',
        'notes',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get the customer that owns the vehicle.
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Get the services for this vehicle.
     */
    public function services(): HasMany
    {
        return $this->hasMany(Service::class);
    }

    /**
     * Scope a query to only include active vehicles.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Get the vehicle's full details.
     */
    public function getFullDetailsAttribute(): string
    {
        $details = $this->model;

        if ($this->license_plate) {
            $details .= ' - ' . $this->license_plate;
        }

        if ($this->color) {
            $details .= ' - ' . $this->color;
        }

        if ($this->year) {
            $details .= ' (' . $this->year . ')';
        }

        return $details;
    }

    /**
     * Find or create a vehicle based on customer phone and license plate.
     *
     * @param string $phone Customer phone number
     * @param string $licensePlate License plate number
     * @param array $attributes Additional attributes for the vehicle
     * @return Vehicle
     */
    public static function findOrCreateByPhoneAndPlate(string $phone, string $licensePlate, array $attributes = []): Vehicle
    {
        // Normalize license plate (remove spaces, uppercase)
        $normalizedPlate = strtoupper(str_replace(' ', '', $licensePlate));

        // Find customer by phone
        $customer = Customer::where('phone', $phone)->first();

        if (!$customer) {
            // Create customer if not exists
            $customer = Customer::create([
                'name' => $attributes['customer_name'] ?? 'Unknown',
                'phone' => $phone,
                'is_active' => true,
            ]);
        }

        // Find vehicle by customer and normalized license plate
        $vehicle = self::where('customer_id', $customer->id)
            ->whereRaw('UPPER(REPLACE(license_plate, " ", "")) = ?', [$normalizedPlate])
            ->first();

        if (!$vehicle) {
            // Create vehicle if not exists
            $vehicle = self::create([
                'customer_id' => $customer->id,
                'model' => $attributes['car_model'] ?? 'Unknown',
                'license_plate' => $licensePlate, // Store original format
                'is_active' => true,
            ]);
        }

        return $vehicle;
    }
}
