<?php

namespace App\Models;

use App\Events\MechanicsAssigned;
use App\Events\ServiceStatusChanged;
use App\Helpers\DebugHelper;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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

            // Check if status has changed
            if ($service->isDirty('status') || $service->wasChanged('status')) {
                $previousStatus = $service->getOriginal('status');
                Log::info("Service #{$service->id} status changed from {$previousStatus} to {$service->status}");

                // Log detailed information for debugging
                Log::info("DEBUG_SERVICE_SAVE: Service #{$service->id} status changed", [
                    'previous_status' => $previousStatus,
                    'new_status' => $service->status,
                    'dirty_attributes' => $service->getDirty(),
                    'changed_attributes' => $service->getChanges(),
                    'original_attributes' => $service->getOriginal(),
                    'has_mechanics' => $service->mechanics()->exists(),
                    'mechanics_count' => $service->mechanics()->count(),
                    'mechanics_ids' => $service->mechanics()->pluck('mechanics.id')->toArray(),
                    'backtrace' => debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 5),
                ]);

                try {
                    // Log database queries
                    DB::enableQueryLog();

                    // Dispatch ServiceStatusChanged event
                    $event = new ServiceStatusChanged($service, $previousStatus);
                    DebugHelper::logEventDetails($event);

                    // Dispatch event through event system
                    event($event);

                    // Also run the listener directly to ensure it's processed immediately
                    $listener = new \App\Listeners\UpdateMechanicReports();
                    $listener->handle($event);

                    // Log queries executed during event dispatch
                    $queries = DB::getQueryLog();
                    Log::info("DEBUG_SERVICE_SAVE: Queries executed during event dispatch", [
                        'queries_count' => count($queries),
                        'queries' => $queries,
                    ]);

                    // Log service details after event dispatch
                    DebugHelper::logServiceDetails($service->id);
                } catch (\Exception $e) {
                    Log::error("DEBUG_SERVICE_SAVE: Error dispatching ServiceStatusChanged event", [
                        'service_id' => $service->id,
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString(),
                    ]);
                } finally {
                    DB::disableQueryLog();
                }
            }
        });

        // Register event handlers for relationship syncing
        static::registerModelEvent('syncing', function ($service, $relation) {
            // Store original mechanics IDs for use in synced event
            if ($relation === 'mechanics') {
                $originalMechanics = $service->mechanics()->get();
                $service->originalMechanicIds = $originalMechanics->pluck('id')->toArray();

                Log::info("Service #{$service->id} syncing mechanics, storing original IDs", [
                    'original_mechanic_ids' => $service->originalMechanicIds
                ]);
            }
        });

        // Register event handlers for relationship synced
        static::registerModelEvent('synced', function ($service, $relation) {
            // Dispatch MechanicsAssigned event after relationship is synced
            if ($relation === 'mechanics') {
                $currentMechanicIds = $service->mechanics()->pluck('mechanics.id')->toArray();

                Log::info("Service #{$service->id} mechanics synced", [
                    'original_mechanic_ids' => $service->originalMechanicIds ?? [],
                    'current_mechanic_ids' => $currentMechanicIds
                ]);

                // Log detailed information for debugging
                Log::info("DEBUG_SERVICE_SYNC: Service #{$service->id} mechanics synced", [
                    'original_mechanic_ids' => $service->originalMechanicIds ?? [],
                    'current_mechanic_ids' => $currentMechanicIds,
                    'service_status' => $service->status,
                    'backtrace' => debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 5),
                ]);

                try {
                    // Log database queries
                    DB::enableQueryLog();

                    // Dispatch MechanicsAssigned event
                    $event = new MechanicsAssigned($service, $service->originalMechanicIds ?? []);
                    DebugHelper::logEventDetails($event);

                    // Dispatch event through event system
                    event($event);

                    // Also run the listener directly to ensure it's processed immediately
                    $listener = new \App\Listeners\UpdateMechanicReports();
                    $listener->handle($event);

                    // Log queries executed during event dispatch
                    $queries = DB::getQueryLog();
                    Log::info("DEBUG_SERVICE_SYNC: Queries executed during event dispatch", [
                        'queries_count' => count($queries),
                        'queries' => $queries,
                    ]);

                    // Log service details after event dispatch
                    DebugHelper::logServiceDetails($service->id);
                } catch (\Exception $e) {
                    Log::error("DEBUG_SERVICE_SYNC: Error dispatching MechanicsAssigned event", [
                        'service_id' => $service->id,
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString(),
                    ]);
                } finally {
                    DB::disableQueryLog();
                }
            }
        });
    }

    /**
     * Store original mechanic IDs before sync
     */
    public $originalMechanicIds = null;
}
