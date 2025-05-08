<?php

namespace App\Observers;

use App\Models\Service;
use App\Services\MechanicReportService;
use Illuminate\Support\Facades\Log;

class ServiceObserver
{
    /**
     * @var MechanicReportService
     */
    protected $mechanicReportService;

    /**
     * Constructor
     */
    public function __construct(MechanicReportService $mechanicReportService)
    {
        $this->mechanicReportService = $mechanicReportService;
    }

    /**
     * Handle the Service "created" event.
     */
    public function created(Service $service): void
    {
        // No action needed for created event
    }

    /**
     * Handle the Service "updated" event.
     */
    public function updated(Service $service): void
    {
        // Check if status was changed
        $statusChanged = $service->isDirty('status');
        $oldStatus = $service->getOriginal('status');
        $newStatus = $service->status;

        Log::info("Service #{$service->id} status changed: " . ($statusChanged ? "Yes (from {$oldStatus} to {$newStatus})" : "No"));

        // Case 1: Service was completed
        if ($newStatus === 'completed') {
            $wasAlreadyCompleted = !$statusChanged && $newStatus === 'completed';

            // Process if status was changed to completed OR if we're editing an already completed service
            if ($statusChanged || $wasAlreadyCompleted) {
                Log::info("Service #{$service->id} is completed, updating mechanic reports");
                $this->mechanicReportService->handleServiceCompleted($service);
            }
        }
        // Case 2: Service was cancelled or changed back to in_progress
        else if (($newStatus === 'cancelled' || $newStatus === 'in_progress') && $statusChanged && $oldStatus === 'completed') {
            Log::info("Service #{$service->id} was changed from completed to {$newStatus}, removing from mechanic reports");
            $this->mechanicReportService->handleServiceCancelled($service);
        }
    }

    /**
     * Handle the Service "deleted" event.
     */
    public function deleted(Service $service): void
    {
        //
    }

    /**
     * Handle the Service "restored" event.
     */
    public function restored(Service $service): void
    {
        //
    }

    /**
     * Handle the Service "force deleted" event.
     */
    public function forceDeleted(Service $service): void
    {
        //
    }

    /**
     * Handle the Service "saved" event.
     * This is called after both created and updated events.
     */
    public function saved(Service $service): void
    {
        Log::info("Service #{$service->id} saved with status: {$service->status}");

        // Case 1: Service is completed
        if ($service->status === 'completed') {
            Log::info("Service #{$service->id} is saved and completed, updating mechanic reports");
            $this->mechanicReportService->handleServiceCompleted($service);
        }
        // Case 2: Service is cancelled
        else if ($service->status === 'cancelled') {
            Log::info("Service #{$service->id} is saved and cancelled, checking if we need to update mechanic reports");
            $this->mechanicReportService->handleServiceCancelled($service);
        }
    }

    /**
     * Handle the Service "syncing" event.
     * This is called when a relationship is being synced.
     */
    public function syncing($service, $relation, $_properties): void
    {
        // Check if the mechanics relationship is being synced
        if ($relation === 'mechanics') {
            Log::info("Mechanics relationship is being synced for service #{$service->id} with status {$service->status}");

            // Get the original mechanics before sync
            $originalMechanics = $service->mechanics()->get();

            // Log the original mechanics
            Log::info("Original mechanics: " . $originalMechanics->pluck('id')->implode(', '));

            // Store the original mechanics IDs for use in synced method
            $service->originalMechanicIds = $originalMechanics->pluck('id')->toArray();
        }
    }

    /**
     * Handle the Service "synced" event.
     * This is called after a relationship has been synced.
     */
    public function synced($service, $relation, $_properties): void
    {
        // Check if the mechanics relationship was synced
        if ($relation === 'mechanics') {
            Log::info("Mechanics relationship has been synced for service #{$service->id} with status {$service->status}");

            // Get the new mechanics after sync
            $newMechanicIds = $service->mechanics()->pluck('mechanics.id')->toArray();

            // Log the new mechanics
            Log::info("New mechanics: " . implode(', ', $newMechanicIds));

            // Check if we have the original mechanics stored
            if (isset($service->originalMechanicIds)) {
                $originalMechanicIds = $service->originalMechanicIds;

                // Handle mechanics change
                $this->mechanicReportService->handleMechanicsChanged($service, $originalMechanicIds, $newMechanicIds);
            }
        }
    }
}
