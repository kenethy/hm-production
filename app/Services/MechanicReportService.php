<?php

namespace App\Services;

use App\Models\Mechanic;
use App\Models\Service;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MechanicReportService
{
    /**
     * Update mechanic reports when a service is completed
     *
     * @param Service $service
     * @return void
     */
    public function handleServiceCompleted(Service $service): void
    {
        Log::info("MechanicReportService: Handling completed service #{$service->id}");
        
        // Get all mechanics for this service
        $mechanics = $service->mechanics;
        
        if ($mechanics->count() === 0) {
            Log::info("MechanicReportService: No mechanics found for service #{$service->id}");
            return;
        }
        
        Log::info("MechanicReportService: Service #{$service->id} has {$mechanics->count()} mechanics");
        
        // Process in a transaction to ensure consistency
        DB::transaction(function () use ($service, $mechanics) {
            // Get the week start and end dates
            $weekStart = now()->startOfWeek();
            $weekEnd = now()->endOfWeek();
            
            // Update each mechanic
            foreach ($mechanics as $mechanic) {
                // Update week dates if not set
                if (empty($mechanic->pivot->week_start) || empty($mechanic->pivot->week_end)) {
                    Log::info("MechanicReportService: Setting week dates for mechanic #{$mechanic->id}");
                    
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
                    Log::info("MechanicReportService: Setting default labor cost for mechanic #{$mechanic->id}: {$defaultLaborCost}");
                    
                    $service->mechanics()->updateExistingPivot($mechanic->id, [
                        'labor_cost' => $defaultLaborCost,
                    ]);
                    
                    $laborCost = $defaultLaborCost;
                }
                
                Log::info("MechanicReportService: Generating report for mechanic #{$mechanic->id} with labor cost {$laborCost}");
                
                // Generate or update weekly report for this mechanic
                $this->generateOrUpdateReport($mechanic, $weekStart, $weekEnd);
            }
        });
    }
    
    /**
     * Update mechanic reports when a service is cancelled or changed back to in_progress
     *
     * @param Service $service
     * @return void
     */
    public function handleServiceCancelled(Service $service): void
    {
        Log::info("MechanicReportService: Handling cancelled service #{$service->id}");
        
        // Get all mechanics for this service
        $mechanics = $service->mechanics;
        
        if ($mechanics->count() === 0) {
            Log::info("MechanicReportService: No mechanics found for service #{$service->id}");
            return;
        }
        
        Log::info("MechanicReportService: Service #{$service->id} has {$mechanics->count()} mechanics");
        
        // Process in a transaction to ensure consistency
        DB::transaction(function () use ($service, $mechanics) {
            // Update each mechanic
            foreach ($mechanics as $mechanic) {
                // Get the week start and end from the pivot
                $weekStart = $mechanic->pivot->week_start;
                $weekEnd = $mechanic->pivot->week_end;
                
                // Check if labor_cost is set
                $laborCost = $mechanic->pivot->labor_cost;
                
                // If we have week dates and labor_cost, we need to update the report
                if ($weekStart && $weekEnd && $laborCost > 0) {
                    Log::info("MechanicReportService: Setting labor cost to 0 for mechanic #{$mechanic->id}");
                    
                    // Set labor_cost to 0 for this service-mechanic relationship
                    $service->mechanics()->updateExistingPivot($mechanic->id, [
                        'labor_cost' => 0,
                    ]);
                    
                    // Generate or update weekly report for this mechanic
                    $this->generateOrUpdateReport($mechanic, $weekStart, $weekEnd);
                }
            }
        });
    }
    
    /**
     * Handle mechanics being changed for a service
     *
     * @param Service $service
     * @param array $oldMechanicIds
     * @param array $newMechanicIds
     * @return void
     */
    public function handleMechanicsChanged(Service $service, array $oldMechanicIds, array $newMechanicIds): void
    {
        Log::info("MechanicReportService: Handling mechanics change for service #{$service->id}");
        Log::info("MechanicReportService: Old mechanics: " . implode(', ', $oldMechanicIds));
        Log::info("MechanicReportService: New mechanics: " . implode(', ', $newMechanicIds));
        
        // Find mechanics that were removed
        $removedMechanicIds = array_diff($oldMechanicIds, $newMechanicIds);
        
        // Find mechanics that were added
        $addedMechanicIds = array_diff($newMechanicIds, $oldMechanicIds);
        
        Log::info("MechanicReportService: Removed mechanics: " . implode(', ', $removedMechanicIds));
        Log::info("MechanicReportService: Added mechanics: " . implode(', ', $addedMechanicIds));
        
        // Process in a transaction to ensure consistency
        DB::transaction(function () use ($service, $removedMechanicIds, $addedMechanicIds) {
            // Handle removed mechanics
            foreach ($removedMechanicIds as $mechanicId) {
                $mechanic = Mechanic::find($mechanicId);
                
                if (!$mechanic) {
                    Log::warning("MechanicReportService: Mechanic #{$mechanicId} not found");
                    continue;
                }
                
                // Get the week start and end from the pivot
                $pivotData = DB::table('mechanic_service')
                    ->where('mechanic_id', $mechanicId)
                    ->where('service_id', $service->id)
                    ->first();
                
                if (!$pivotData) {
                    Log::warning("MechanicReportService: Pivot data not found for mechanic #{$mechanicId} and service #{$service->id}");
                    continue;
                }
                
                $weekStart = $pivotData->week_start ?? null;
                $weekEnd = $pivotData->week_end ?? null;
                
                if ($weekStart && $weekEnd) {
                    Log::info("MechanicReportService: Updating report for removed mechanic #{$mechanicId}");
                    
                    // Generate or update weekly report for this mechanic
                    $this->generateOrUpdateReport($mechanic, $weekStart, $weekEnd);
                }
            }
            
            // Handle added mechanics
            if ($service->status === 'completed') {
                foreach ($addedMechanicIds as $mechanicId) {
                    $mechanic = Mechanic::find($mechanicId);
                    
                    if (!$mechanic) {
                        Log::warning("MechanicReportService: Mechanic #{$mechanicId} not found");
                        continue;
                    }
                    
                    // Get the week start and end dates
                    $weekStart = now()->startOfWeek();
                    $weekEnd = now()->endOfWeek();
                    
                    // Update mechanic_service pivot with week dates
                    $service->mechanics()->updateExistingPivot($mechanicId, [
                        'week_start' => $weekStart,
                        'week_end' => $weekEnd,
                    ]);
                    
                    // Check if labor_cost is set
                    $pivotData = DB::table('mechanic_service')
                        ->where('mechanic_id', $mechanicId)
                        ->where('service_id', $service->id)
                        ->first();
                    
                    $laborCost = $pivotData->labor_cost ?? 0;
                    
                    // If labor_cost is not set or is 0, set a default value
                    if (empty($laborCost) || $laborCost == 0) {
                        $defaultLaborCost = 50000; // Default labor cost
                        Log::info("MechanicReportService: Setting default labor cost for added mechanic #{$mechanicId}: {$defaultLaborCost}");
                        
                        $service->mechanics()->updateExistingPivot($mechanicId, [
                            'labor_cost' => $defaultLaborCost,
                        ]);
                    }
                    
                    // Generate or update weekly report for this mechanic
                    $this->generateOrUpdateReport($mechanic, $weekStart, $weekEnd);
                }
            }
        });
    }
    
    /**
     * Generate or update a weekly report for a mechanic
     *
     * @param Mechanic $mechanic
     * @param string $weekStart
     * @param string $weekEnd
     * @return void
     */
    private function generateOrUpdateReport(Mechanic $mechanic, string $weekStart, string $weekEnd): void
    {
        try {
            Log::info("MechanicReportService: Generating report for mechanic #{$mechanic->id} for week {$weekStart} to {$weekEnd}");
            
            // Force refresh mechanic from database
            $freshMechanic = Mechanic::find($mechanic->id);
            
            // Generate or update weekly report for this mechanic
            $report = $freshMechanic->generateWeeklyReport($weekStart, $weekEnd);
            
            Log::info("MechanicReportService: Report generated for mechanic #{$mechanic->id}", [
                'report_id' => $report->id,
                'services_count' => $report->services_count,
                'total_labor_cost' => $report->total_labor_cost,
            ]);
        } catch (\Exception $e) {
            Log::error("MechanicReportService: Error generating report for mechanic #{$mechanic->id}: " . $e->getMessage(), [
                'mechanic_id' => $mechanic->id,
                'week_start' => $weekStart,
                'week_end' => $weekEnd,
                'exception' => $e
            ]);
        }
    }
}
