<?php

namespace App\Models;

use App\Events\ServiceUpdated;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Service extends Model
{
    use HasFactory;
    protected $fillable = [
        'booking_id',
        'customer_id',
        'vehicle_id',
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
        'invoice_number',
        'notes',
        'completed_at',
        'entry_time',
        'exit_time',
    ];

    protected $casts = [
        'labor_cost' => 'decimal:2',
        'parts_cost' => 'decimal:2',
        'total_cost' => 'decimal:2',
        'completed_at' => 'datetime',
        'entry_time' => 'datetime',
        'exit_time' => 'datetime',
    ];

    /**
     * Get the booking associated with the service.
     */
    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    /**
     * Get the vehicle associated with the service.
     */
    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
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
    public function updates(): HasMany
    {
        return $this->hasMany(ServiceUpdate::class);
    }

    /**
     * Get the customer associated with the service.
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Get the mechanics assigned to this service.
     */
    public function mechanics(): BelongsToMany
    {
        return $this->belongsToMany(Mechanic::class)
            ->withPivot('notes', 'labor_cost', 'invoice_number', 'week_start', 'week_end')
            ->withTimestamps();
    }

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        // After a service is created or updated, update the customer statistics
        static::saved(function ($service) {
            if ($service->customer_id) {
                $service->customer->updateStatistics();
            }

            // Dispatch ServiceUpdated event
            event(new ServiceUpdated($service));
        });

        // Register event handlers for relationship syncing
        static::registerModelEvent('syncing', function ($service, $relation) {
            // Store original mechanics IDs for use in synced event
            if ($relation === 'mechanics') {
                $originalMechanics = $service->mechanics()->get();
                $service->originalMechanicIds = $originalMechanics->pluck('id')->toArray();
            }
        });

        // Register event handlers for relationship synced
        static::registerModelEvent('synced', function ($service, $relation) {
            // Dispatch ServiceUpdated event after relationship is synced
            if ($relation === 'mechanics') {
                event(new ServiceUpdated($service));
            }
        });
    }

    /**
     * Store original mechanic IDs before sync
     */
    public $originalMechanicIds = null;
}
