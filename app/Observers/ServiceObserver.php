<?php

namespace App\Observers;

use App\Models\Service;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

class ServiceObserver
{
    /**
     * Handle the Service "created" event.
     */
    public function created(Service $service): void
    {
        Log::info("ServiceObserver: Service #{$service->id} created");
    }

    /**
     * Handle the Service "updated" event.
     */
    public function updated(Service $service): void
    {
        Log::info("ServiceObserver: Service #{$service->id} updated");
        
        // Check if status has changed
        if ($service->isDirty('status') || $service->wasChanged('status')) {
            $previousStatus = $service->getOriginal('status');
            $currentStatus = $service->status;
            
            Log::info("ServiceObserver: Service #{$service->id} status changed from {$previousStatus} to {$currentStatus}");
            
            // If status changed to or from 'completed', sync mechanic reports
            if ($previousStatus === 'completed' || $currentStatus === 'completed') {
                Log::info("ServiceObserver: Syncing mechanic reports for service #{$service->id}");
                
                // Run the command to sync mechanic reports for this service
                Artisan::call('mechanic:sync-reports', [
                    '--service_id' => $service->id,
                ]);
                
                Log::info("ServiceObserver: Mechanic reports synced for service #{$service->id}");
            }
        }
    }

    /**
     * Handle the Service "deleted" event.
     */
    public function deleted(Service $service): void
    {
        Log::info("ServiceObserver: Service #{$service->id} deleted");
        
        // If service was completed, sync mechanic reports
        if ($service->status === 'completed') {
            Log::info("ServiceObserver: Syncing mechanic reports after service #{$service->id} deletion");
            
            // Get mechanics associated with this service
            $mechanics = $service->mechanics;
            
            // Run the command to sync mechanic reports for each mechanic
            foreach ($mechanics as $mechanic) {
                Artisan::call('mechanic:sync-reports', [
                    '--mechanic_id' => $mechanic->id,
                ]);
            }
            
            Log::info("ServiceObserver: Mechanic reports synced after service #{$service->id} deletion");
        }
    }

    /**
     * Handle the Service "restored" event.
     */
    public function restored(Service $service): void
    {
        Log::info("ServiceObserver: Service #{$service->id} restored");
        
        // If service is completed, sync mechanic reports
        if ($service->status === 'completed') {
            Log::info("ServiceObserver: Syncing mechanic reports after service #{$service->id} restoration");
            
            // Run the command to sync mechanic reports for this service
            Artisan::call('mechanic:sync-reports', [
                '--service_id' => $service->id,
            ]);
            
            Log::info("ServiceObserver: Mechanic reports synced after service #{$service->id} restoration");
        }
    }

    /**
     * Handle the Service "force deleted" event.
     */
    public function forceDeleted(Service $service): void
    {
        Log::info("ServiceObserver: Service #{$service->id} force deleted");
        
        // If service was completed, sync mechanic reports
        if ($service->status === 'completed') {
            Log::info("ServiceObserver: Syncing mechanic reports after service #{$service->id} force deletion");
            
            // Get mechanics associated with this service
            $mechanics = $service->mechanics;
            
            // Run the command to sync mechanic reports for each mechanic
            foreach ($mechanics as $mechanic) {
                Artisan::call('mechanic:sync-reports', [
                    '--mechanic_id' => $mechanic->id,
                ]);
            }
            
            Log::info("ServiceObserver: Mechanic reports synced after service #{$service->id} force deletion");
        }
    }
}
