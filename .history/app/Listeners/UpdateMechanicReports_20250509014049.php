<?php

namespace App\Listeners;

use App\Events\MechanicsAssigned;
use App\Events\ServiceStatusChanged;
use App\Models\Mechanic;
use App\Models\Service;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UpdateMechanicReports
{

    /**
     * Handle the ServiceStatusChanged event.
     */
    public function handle($event): void
    {
        try {
            // Validate event
            if (!($event instanceof ServiceStatusChanged) && !($event instanceof MechanicsAssigned)) {
                Log::error("UpdateMechanicReports: Invalid event type", [
                    'event_class' => get_class($event),
                ]);
                return;
            }

            $service = $event->service;

            // Validate service
            if (!$service) {
                Log::error("UpdateMechanicReports: Service is null");
                return;
            }

            Log::info("UpdateMechanicReports: Handle event started", [
                'event_class' => get_class($event),
                'service_id' => $service->id,
                'service_status' => $service->status,
            ]);

            if ($event instanceof ServiceStatusChanged) {
                Log::info("UpdateMechanicReports: Service #{$service->id} status changed from {$event->previousStatus} to {$service->status}");
                $this->handleServiceStatusChanged($service, $event->previousStatus);
            } elseif ($event instanceof MechanicsAssigned) {
                Log::info("UpdateMechanicReports: Mechanics assigned to service #{$service->id}");
                $this->handleMechanicsAssigned($service, $event->previousMechanicIds);
            }

            Log::info("UpdateMechanicReports: Handle event completed successfully", [
                'service_id' => $service->id,
            ]);
        } catch (\Exception $e) {
            Log::error("UpdateMechanicReports: Error handling event", [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            // Don't rethrow the exception to prevent the job from failing
        }
    }

    /**
     * Handle service status change.
     */
    private function handleServiceStatusChanged(Service $service, ?string $previousStatus): void
    {
        Log::info("UpdateMechanicReports: handleServiceStatusChanged started", [
            'service_id' => $service->id,
            'current_status' => $service->status,
            'previous_status' => $previousStatus,
        ]);

        // If service is completed, update mechanic reports
        if ($service->status === 'completed') {
            Log::info("UpdateMechanicReports: Service is completed, updating mechanic reports");
            $this->updateMechanicReportsForCompletedService($service);
        }
        // If service was completed but now is not, remove labor costs
        elseif ($previousStatus === 'completed') {
            Log::info("UpdateMechanicReports: Service was completed but now is not, removing labor costs");
            $this->removeLaborCostsFromMechanics($service);
        } else {
            Log::info("UpdateMechanicReports: No action needed for this status change");
        }

        Log::info("UpdateMechanicReports: handleServiceStatusChanged completed");
    }

    /**
     * Handle mechanics assignment.
     */
    private function handleMechanicsAssigned(Service $service, array $previousMechanicIds): void
    {
        // Only process if service is completed
        if ($service->status === 'completed') {
            // Get current mechanic IDs
            $currentMechanicIds = $service->mechanics()->pluck('mechanics.id')->toArray();

            // Find mechanics that were removed
            $removedMechanicIds = array_diff($previousMechanicIds, $currentMechanicIds);

            // Remove labor costs for removed mechanics
            if (!empty($removedMechanicIds)) {
                $this->removeLaborCostsForMechanics($service, $removedMechanicIds);
            }

            // Update reports for current mechanics
            $this->updateMechanicReportsForCompletedService($service);
        }
    }

    /**
     * Update mechanic reports for a completed service.
     */
    private function updateMechanicReportsForCompletedService(Service $service): void
    {
        Log::info("UpdateMechanicReports: updateMechanicReportsForCompletedService started", [
            'service_id' => $service->id,
            'service_status' => $service->status,
        ]);

        // Get all mechanics for this service
        $mechanics = $service->mechanics;

        Log::info("UpdateMechanicReports: Mechanics query executed", [
            'mechanics_count' => $mechanics->count(),
            'mechanics_ids' => $mechanics->pluck('id')->toArray(),
        ]);

        if ($mechanics->count() === 0) {
            Log::info("UpdateMechanicReports: No mechanics found for service #{$service->id}");
            return;
        }

        Log::info("UpdateMechanicReports: Service #{$service->id} has {$mechanics->count()} mechanics");

        // Process in a transaction to ensure consistency
        DB::transaction(function () use ($service, $mechanics) {
            // Get the week start and end dates
            $weekStart = Carbon::now()->startOfWeek()->format('Y-m-d');
            $weekEnd = Carbon::now()->endOfWeek()->format('Y-m-d');

            // Process each mechanic
            foreach ($mechanics as $mechanic) {
                // Update week dates if not set
                if (empty($mechanic->pivot->week_start) || empty($mechanic->pivot->week_end)) {
                    Log::info("UpdateMechanicReports: Setting week dates for mechanic #{$mechanic->id}");

                    $service->mechanics()->updateExistingPivot($mechanic->id, [
                        'week_start' => $weekStart,
                        'week_end' => $weekEnd,
                    ]);
                } else {
                    // Use existing week dates
                    $weekStart = $mechanic->pivot->week_start;
                    $weekEnd = $mechanic->pivot->week_end;
                }

                // Check if labor_cost is set
                $laborCost = $mechanic->pivot->labor_cost;

                // If labor_cost is not set or is 0, set a default value
                if (empty($laborCost) || $laborCost == 0) {
                    $defaultLaborCost = 50000; // Default labor cost
                    Log::info("UpdateMechanicReports: Setting default labor cost for mechanic #{$mechanic->id}: {$defaultLaborCost}");

                    $service->mechanics()->updateExistingPivot($mechanic->id, [
                        'labor_cost' => $defaultLaborCost,
                    ]);

                    $laborCost = $defaultLaborCost;
                }

                Log::info("UpdateMechanicReports: Generating report for mechanic #{$mechanic->id} with labor cost {$laborCost}");

                // Generate or update weekly report for this mechanic
                $this->generateOrUpdateReport($mechanic, $weekStart, $weekEnd);
            }
        });
    }

    /**
     * Remove labor costs from mechanics for a service.
     */
    private function removeLaborCostsFromMechanics(Service $service): void
    {
        // Get all mechanics for this service
        $mechanics = $service->mechanics;

        if ($mechanics->count() === 0) {
            return;
        }

        Log::info("UpdateMechanicReports: Removing labor costs for service #{$service->id} with {$mechanics->count()} mechanics");

        // Process in a transaction to ensure consistency
        DB::transaction(function () use ($service, $mechanics) {
            // Process each mechanic
            foreach ($mechanics as $mechanic) {
                // Get week dates
                $weekStart = $mechanic->pivot->week_start;
                $weekEnd = $mechanic->pivot->week_end;

                if (empty($weekStart) || empty($weekEnd)) {
                    continue;
                }

                // Set labor cost to 0
                $service->mechanics()->updateExistingPivot($mechanic->id, [
                    'labor_cost' => 0,
                ]);

                Log::info("UpdateMechanicReports: Set labor cost to 0 for mechanic #{$mechanic->id} on service #{$service->id}");

                // Update the report
                $this->generateOrUpdateReport($mechanic, $weekStart, $weekEnd);
            }
        });
    }

    /**
     * Remove labor costs for specific mechanics.
     */
    private function removeLaborCostsForMechanics(Service $service, array $mechanicIds): void
    {
        if (empty($mechanicIds)) {
            return;
        }

        Log::info("UpdateMechanicReports: Removing labor costs for specific mechanics on service #{$service->id}", [
            'mechanic_ids' => $mechanicIds
        ]);

        // Process in a transaction to ensure consistency
        DB::transaction(function () use ($service, $mechanicIds) {
            // Get mechanics data from pivot table before they are removed
            $mechanicsData = DB::table('mechanic_service')
                ->where('service_id', $service->id)
                ->whereIn('mechanic_id', $mechanicIds)
                ->get();

            // Process each mechanic
            foreach ($mechanicsData as $mechanicData) {
                $weekStart = $mechanicData->week_start;
                $weekEnd = $mechanicData->week_end;

                if (empty($weekStart) || empty($weekEnd)) {
                    continue;
                }

                // Get the mechanic
                $mechanic = Mechanic::find($mechanicData->mechanic_id);

                if (!$mechanic) {
                    continue;
                }

                Log::info("UpdateMechanicReports: Updating report for removed mechanic #{$mechanic->id}");

                // Update the report
                $this->generateOrUpdateReport($mechanic, $weekStart, $weekEnd);
            }
        });
    }

    /**
     * Generate or update a weekly report for a mechanic.
     */
    private function generateOrUpdateReport(Mechanic $mechanic, string $weekStart, string $weekEnd): void
    {
        try {
            Log::info("UpdateMechanicReports: Generating report for mechanic #{$mechanic->id} for week {$weekStart} to {$weekEnd}");

            // Calculate total labor cost for completed services
            $totalLaborCost = DB::table('mechanic_service')
                ->join('services', 'mechanic_service.service_id', '=', 'services.id')
                ->where('mechanic_service.mechanic_id', $mechanic->id)
                ->where('mechanic_service.week_start', $weekStart)
                ->where('mechanic_service.week_end', $weekEnd)
                ->where('services.status', 'completed')
                ->sum('mechanic_service.labor_cost');

            // Count completed services
            $servicesCount = DB::table('mechanic_service')
                ->join('services', 'mechanic_service.service_id', '=', 'services.id')
                ->where('mechanic_service.mechanic_id', $mechanic->id)
                ->where('mechanic_service.week_start', $weekStart)
                ->where('mechanic_service.week_end', $weekEnd)
                ->where('services.status', 'completed')
                ->count();

            Log::info("UpdateMechanicReports: Calculated for mechanic #{$mechanic->id}: services_count={$servicesCount}, total_labor_cost={$totalLaborCost}");

            // Find or create the report
            $report = DB::table('mechanic_reports')
                ->where('mechanic_id', $mechanic->id)
                ->where('week_start', $weekStart)
                ->where('week_end', $weekEnd)
                ->first();

            if ($report) {
                // Update existing report
                DB::table('mechanic_reports')
                    ->where('id', $report->id)
                    ->update([
                        'services_count' => $servicesCount,
                        'total_labor_cost' => $totalLaborCost,
                        'updated_at' => now(),
                    ]);

                Log::info("UpdateMechanicReports: Updated report #{$report->id} for mechanic #{$mechanic->id}");
            } else {
                // Create new report
                $reportId = DB::table('mechanic_reports')->insertGetId([
                    'mechanic_id' => $mechanic->id,
                    'week_start' => $weekStart,
                    'week_end' => $weekEnd,
                    'services_count' => $servicesCount,
                    'total_labor_cost' => $totalLaborCost,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                Log::info("UpdateMechanicReports: Created new report #{$reportId} for mechanic #{$mechanic->id}");
            }
        } catch (\Exception $e) {
            Log::error("UpdateMechanicReports: Error generating report for mechanic #{$mechanic->id}: " . $e->getMessage(), [
                'exception' => $e,
            ]);
        }
    }
}
