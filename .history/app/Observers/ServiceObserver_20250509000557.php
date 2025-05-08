<?php

namespace App\Observers;

use App\Models\Service;
use App\Services\MechanicReportService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

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
