<?php

namespace App\Observers;

use App\Models\Service;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MechanicServiceObserver
{
    /**
     * Handle the pivot record "created" event.
     */
    public function created(Pivot $pivot): void
    {
        // Check if this is a mechanic_service pivot
        if ($pivot->getTable() !== 'mechanic_service') {
            return;
        }
        
        $serviceId = $pivot->service_id;
        $mechanicId = $pivot->mechanic_id;
        
        Log::info("MechanicServiceObserver: Mechanic #{$mechanicId} assigned to service #{$serviceId}");
        
        // Get the service
        $service = Service::find($serviceId);
        
        // If service is completed, sync mechanic reports
        if ($service && $service->status === 'completed') {
            Log::info("MechanicServiceObserver: Syncing mechanic reports after mechanic #{$mechanicId} assigned to completed service #{$serviceId}");
            
            // Run the command to sync mechanic reports for this service
            Artisan::call('mechanic:sync-reports', [
                '--service_id' => $serviceId,
            ]);
            
            Log::info("MechanicServiceObserver: Mechanic reports synced after mechanic #{$mechanicId} assigned to completed service #{$serviceId}");
        }
    }

    /**
     * Handle the pivot record "updated" event.
     */
    public function updated(Pivot $pivot): void
    {
        // Check if this is a mechanic_service pivot
        if ($pivot->getTable() !== 'mechanic_service') {
            return;
        }
        
        $serviceId = $pivot->service_id;
        $mechanicId = $pivot->mechanic_id;
        
        Log::info("MechanicServiceObserver: Mechanic #{$mechanicId} assignment to service #{$serviceId} updated");
        
        // Get the service
        $service = Service::find($serviceId);
        
        // If service is completed, sync mechanic reports
        if ($service && $service->status === 'completed') {
            Log::info("MechanicServiceObserver: Syncing mechanic reports after mechanic #{$mechanicId} assignment to completed service #{$serviceId} updated");
            
            // Run the command to sync mechanic reports for this service
            Artisan::call('mechanic:sync-reports', [
                '--service_id' => $serviceId,
            ]);
            
            Log::info("MechanicServiceObserver: Mechanic reports synced after mechanic #{$mechanicId} assignment to completed service #{$serviceId} updated");
        }
    }

    /**
     * Handle the pivot record "deleted" event.
     */
    public function deleted(Pivot $pivot): void
    {
        // Check if this is a mechanic_service pivot
        if ($pivot->getTable() !== 'mechanic_service') {
            return;
        }
        
        $serviceId = $pivot->service_id;
        $mechanicId = $pivot->mechanic_id;
        
        Log::info("MechanicServiceObserver: Mechanic #{$mechanicId} removed from service #{$serviceId}");
        
        // Get the service
        $service = Service::find($serviceId);
        
        // If service is completed, sync mechanic reports
        if ($service && $service->status === 'completed') {
            Log::info("MechanicServiceObserver: Syncing mechanic reports after mechanic #{$mechanicId} removed from completed service #{$serviceId}");
            
            // Run the command to sync mechanic reports for this mechanic
            Artisan::call('mechanic:sync-reports', [
                '--mechanic_id' => $mechanicId,
            ]);
            
            // Also sync the service in case other mechanics are still assigned
            Artisan::call('mechanic:sync-reports', [
                '--service_id' => $serviceId,
            ]);
            
            Log::info("MechanicServiceObserver: Mechanic reports synced after mechanic #{$mechanicId} removed from completed service #{$serviceId}");
        }
    }
}
