<?php

namespace App\Observers;

use App\Models\Service;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class ServiceObserver
{
    /**
     * Handle the Service "created" event.
     */
    public function created(Service $service): void
    {
        //
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

        // Check if mechanics were changed
        $mechanicsChanged = $service->isDirty('mechanics');

        Log::info("Service #{$service->id} status changed: " . ($statusChanged ? "Yes (from {$oldStatus} to {$newStatus})" : "No") .
            ", mechanics changed: " . ($mechanicsChanged ? 'Yes' : 'No'));

        // Case 1: Service was completed
        if ($newStatus === 'completed') {
            $wasAlreadyCompleted = !$statusChanged && $newStatus === 'completed';

            // Process if status was changed to completed OR if we're editing an already completed service
            if ($statusChanged || $wasAlreadyCompleted || $mechanicsChanged) {
                Log::info("Service #{$service->id} is completed, updating mechanic reports");

                // Get all mechanics for this service
                $mechanics = $service->mechanics;

                if ($mechanics->count() > 0) {
                    Log::info("Service has {$mechanics->count()} mechanics, generating reports");

                    // Get the week start and end dates
                    $weekStart = now()->startOfWeek();
                    $weekEnd = now()->endOfWeek();

                    // Update mechanic_service pivot with week dates if not already set
                    foreach ($mechanics as $mechanic) {
                        if (empty($mechanic->pivot->week_start) || empty($mechanic->pivot->week_end)) {
                            Log::info("Updating week dates for mechanic #{$mechanic->id}");

                            $service->mechanics()->updateExistingPivot($mechanic->id, [
                                'week_start' => $weekStart,
                                'week_end' => $weekEnd,
                            ]);
                        }
                    }

                    // Generate reports for all mechanics
                    $this->generateMechanicReports($service);
                }
            }
        }
        // Case 2: Service was cancelled
        else if ($newStatus === 'cancelled' && $statusChanged) {
            Log::info("Service #{$service->id} was cancelled, removing from mechanic reports");

            // If service was previously completed, we need to remove it from mechanic reports
            if ($oldStatus === 'completed') {
                // Get all mechanics for this service
                $mechanics = $service->mechanics;

                if ($mechanics->count() > 0) {
                    Log::info("Service has {$mechanics->count()} mechanics, updating reports to remove service");

                    // Process in a transaction to ensure consistency
                    DB::transaction(function () use ($service, $mechanics) {
                        // For each mechanic, update their report to remove this service
                        foreach ($mechanics as $mechanic) {
                            // Get the week start and end from the pivot
                            $weekStart = $mechanic->pivot->week_start;
                            $weekEnd = $mechanic->pivot->week_end;

                            if ($weekStart && $weekEnd) {
                                Log::info("Removing service #{$service->id} from mechanic #{$mechanic->id} report for week {$weekStart} to {$weekEnd}");

                                // Set labor_cost to 0 for this service-mechanic relationship
                                $service->mechanics()->updateExistingPivot($mechanic->id, [
                                    'labor_cost' => 0,
                                ]);

                                // Generate or update weekly report for this mechanic
                                $mechanic->generateWeeklyReport($weekStart, $weekEnd);
                            }
                        }
                    });
                }
            }
        }
        // Case 3: Service was reactivated (changed from cancelled to in_progress)
        else if ($newStatus === 'in_progress' && $statusChanged && $oldStatus === 'cancelled') {
            Log::info("Service #{$service->id} was reactivated from cancelled, no action needed yet");
            // No action needed here, as the service is not completed yet
            // When it's marked as completed later, it will be added to mechanic reports
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

            // Get all mechanics for this service
            $mechanics = $service->mechanics;

            if ($mechanics->count() > 0) {
                Log::info("Service has {$mechanics->count()} mechanics, generating reports");

                // Process in a transaction to ensure consistency
                DB::transaction(function () use ($service, $mechanics) {
                    // Get the week start and end dates
                    $weekStart = now()->startOfWeek();
                    $weekEnd = now()->endOfWeek();

                    // Update mechanic_service pivot with week dates if not already set
                    foreach ($mechanics as $mechanic) {
                        if (empty($mechanic->pivot->week_start) || empty($mechanic->pivot->week_end)) {
                            Log::info("Updating week dates for mechanic #{$mechanic->id}");

                            $service->mechanics()->updateExistingPivot($mechanic->id, [
                                'week_start' => $weekStart,
                                'week_end' => $weekEnd,
                            ]);
                        }

                        // Check if labor_cost is set
                        $laborCost = $mechanic->pivot->labor_cost;

                        // If labor_cost is not set or is 0, set a default value
                        if (empty($laborCost) || $laborCost == 0) {
                            $defaultLaborCost = 50000; // Default labor cost
                            Log::info("Setting default labor cost for mechanic #{$mechanic->id}: {$defaultLaborCost}");

                            $service->mechanics()->updateExistingPivot($mechanic->id, [
                                'labor_cost' => $defaultLaborCost,
                            ]);
                        }
                    }

                    // Generate reports for all mechanics
                    $this->generateMechanicReports($service);
                });
            }
        }
        // Case 2: Service is cancelled
        else if ($service->status === 'cancelled') {
            Log::info("Service #{$service->id} is saved and cancelled, checking if we need to update mechanic reports");

            // Get all mechanics for this service
            $mechanics = $service->mechanics;

            if ($mechanics->count() > 0) {
                Log::info("Service has {$mechanics->count()} mechanics, checking if we need to update reports");

                // Process in a transaction to ensure consistency
                DB::transaction(function () use ($service, $mechanics) {
                    // For each mechanic, check if we need to update their report
                    foreach ($mechanics as $mechanic) {
                        // Get the week start and end from the pivot
                        $weekStart = $mechanic->pivot->week_start;
                        $weekEnd = $mechanic->pivot->week_end;

                        // Check if labor_cost is set
                        $laborCost = $mechanic->pivot->labor_cost;

                        // If we have week dates and labor_cost, we need to update the report
                        if ($weekStart && $weekEnd && $laborCost > 0) {
                            Log::info("Removing service #{$service->id} from mechanic #{$mechanic->id} report for week {$weekStart} to {$weekEnd}");

                            // Set labor_cost to 0 for this service-mechanic relationship
                            $service->mechanics()->updateExistingPivot($mechanic->id, [
                                'labor_cost' => 0,
                            ]);

                            // Generate or update weekly report for this mechanic
                            $mechanic->generateWeeklyReport($weekStart, $weekEnd);
                        }
                    }
                });
            }
        }
    }

    /**
     * Handle the Service "syncing" event.
     * This is called when a relationship is being synced.
     */
    public function syncing($service, $relation, $properties): void
    {
        // Check if the mechanics relationship is being synced
        if ($relation === 'mechanics') {
            Log::info("Mechanics relationship is being synced for service #{$service->id} with status {$service->status}");

            // Get the original mechanics before sync
            $originalMechanics = $service->mechanics()->get();

            // Log the original mechanics
            Log::info("Original mechanics: " . $originalMechanics->pluck('id')->implode(', '));

            // Store the original mechanics in a property for use in synced method
            $service->originalMechanics = $originalMechanics;
        }
    }

    /**
     * Handle the Service "synced" event.
     * This is called after a relationship has been synced.
     */
    public function synced($service, $relation, $properties): void
    {
        // Check if the mechanics relationship was synced
        if ($relation === 'mechanics') {
            Log::info("Mechanics relationship has been synced for service #{$service->id} with status {$service->status}");

            // Get the new mechanics after sync
            $newMechanics = $service->mechanics()->get();

            // Log the new mechanics
            Log::info("New mechanics: " . $newMechanics->pluck('id')->implode(', '));

            // Check if we have the original mechanics stored
            if (isset($service->originalMechanics)) {
                $originalMechanics = $service->originalMechanics;

                // Find mechanics that were removed
                $removedMechanics = $originalMechanics->filter(function ($mechanic) use ($newMechanics) {
                    return !$newMechanics->contains('id', $mechanic->id);
                });

                // Find mechanics that were added
                $addedMechanics = $newMechanics->filter(function ($mechanic) use ($originalMechanics) {
                    return !$originalMechanics->contains('id', $mechanic->id);
                });

                Log::info("Removed mechanics: " . $removedMechanics->pluck('id')->implode(', '));
                Log::info("Added mechanics: " . $addedMechanics->pluck('id')->implode(', '));

                // Process in a transaction to ensure consistency
                DB::transaction(function () use ($service, $removedMechanics, $addedMechanics) {
                    // For each removed mechanic, update their report to remove this service
                    foreach ($removedMechanics as $mechanic) {
                        Log::info("Updating report for removed mechanic #{$mechanic->id}");

                        // Get the week start and end from the pivot
                        $weekStart = $mechanic->pivot->week_start;
                        $weekEnd = $mechanic->pivot->week_end;

                        if ($weekStart && $weekEnd) {
                            // Generate or update weekly report for this mechanic
                            $mechanic->generateWeeklyReport($weekStart, $weekEnd);
                        }
                    }

                    // For each added mechanic, update their report to add this service
                    foreach ($addedMechanics as $mechanic) {
                        Log::info("Updating report for added mechanic #{$mechanic->id}");

                        // Only update reports if service is completed
                        if ($service->status === 'completed') {
                            // Get the week start and end dates
                            $weekStart = now()->startOfWeek();
                            $weekEnd = now()->endOfWeek();

                            // Update mechanic_service pivot with week dates
                            $service->mechanics()->updateExistingPivot($mechanic->id, [
                                'week_start' => $weekStart,
                                'week_end' => $weekEnd,
                            ]);

                            // Check if labor_cost is set
                            $laborCost = $mechanic->pivot->labor_cost;

                            // If labor_cost is not set or is 0, set a default value
                            if (empty($laborCost) || $laborCost == 0) {
                                $defaultLaborCost = 50000; // Default labor cost
                                Log::info("Setting default labor cost for added mechanic #{$mechanic->id}: {$defaultLaborCost}");

                                $service->mechanics()->updateExistingPivot($mechanic->id, [
                                    'labor_cost' => $defaultLaborCost,
                                ]);
                            }

                            // Generate or update weekly report for this mechanic
                            $mechanic->generateWeeklyReport($weekStart, $weekEnd);
                        }
                        // If service is cancelled, set labor_cost to 0
                        else if ($service->status === 'cancelled') {
                            Log::info("Service is cancelled, setting labor_cost to 0 for added mechanic #{$mechanic->id}");

                            // Set labor_cost to 0 for this service-mechanic relationship
                            $service->mechanics()->updateExistingPivot($mechanic->id, [
                                'labor_cost' => 0,
                            ]);
                        }
                    }
                });
            }
        }
    }

    /**
     * Generate mechanic reports for all mechanics in a service.
     */
    protected function generateMechanicReports($service): void
    {
        // Refresh the service to get the latest data
        $service->refresh();

        // Get all mechanics for this service
        $mechanics = $service->mechanics;

        // Log for debugging
        Log::info("Generating mechanic reports for service #{$service->id} with " . $mechanics->count() . " mechanics");

        // Process each mechanic
        foreach ($mechanics as $mechanic) {
            try {
                // Get week start and end from pivot
                $weekStart = $mechanic->pivot->week_start;
                $weekEnd = $mechanic->pivot->week_end;

                // If week dates are not set, use current week
                if (empty($weekStart) || empty($weekEnd)) {
                    $weekStart = now()->startOfWeek();
                    $weekEnd = now()->endOfWeek();

                    // Update pivot with week dates
                    $service->mechanics()->updateExistingPivot($mechanic->id, [
                        'week_start' => $weekStart,
                        'week_end' => $weekEnd,
                    ]);
                }

                Log::info("Mechanic #{$mechanic->id} week period: {$weekStart} to {$weekEnd}");

                // Check if labor_cost is set
                $laborCost = $mechanic->pivot->labor_cost;
                Log::info("Mechanic #{$mechanic->id} labor cost: {$laborCost}");

                // If labor_cost is not set or is 0, set a default value
                if (empty($laborCost) || $laborCost == 0) {
                    $defaultLaborCost = 50000; // Default labor cost
                    Log::info("Setting default labor cost for mechanic #{$mechanic->id}: {$defaultLaborCost}");

                    $service->mechanics()->updateExistingPivot($mechanic->id, [
                        'labor_cost' => $defaultLaborCost,
                    ]);

                    // Update the local variable for use in generateWeeklyReport
                    $laborCost = $defaultLaborCost;
                }

                // Force refresh mechanic from database
                $freshMechanic = \App\Models\Mechanic::find($mechanic->id);

                // Generate or update weekly report for this mechanic
                $report = $freshMechanic->generateWeeklyReport($weekStart, $weekEnd);

                // If the report still has 0 labor cost, update it directly
                if ($report->total_labor_cost == 0 && $laborCost > 0) {
                    Log::info("Report #{$report->id} has 0 labor cost, updating directly with {$laborCost}");

                    // Get all services for this mechanic in this week
                    $servicesCount = $freshMechanic->services()
                        ->wherePivot('week_start', $weekStart)
                        ->wherePivot('week_end', $weekEnd)
                        ->count();

                    // Update the report
                    $report->total_labor_cost = $laborCost;
                    $report->services_count = max(1, $servicesCount); // At least 1 service
                    $report->save();

                    Log::info("Updated report #{$report->id} directly: services_count={$report->services_count}, total_labor_cost={$report->total_labor_cost}");
                }

                Log::info("Successfully generated report for mechanic #{$mechanic->id} ({$mechanic->name})", [
                    'report_id' => $report->id,
                    'services_count' => $report->services_count,
                    'total_labor_cost' => $report->total_labor_cost,
                ]);
            } catch (\Exception $e) {
                // Log error but continue with other mechanics
                Log::error("Error generating report for mechanic #{$mechanic->id}: " . $e->getMessage(), [
                    'service_id' => $service->id,
                    'mechanic_id' => $mechanic->id,
                    'exception' => $e
                ]);
            }
        }
    }
}
