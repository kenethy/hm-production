<?php

namespace App\Helpers;

use App\Models\Mechanic;
use App\Models\Service;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MechanicReportHelper
{
    /**
     * Update mechanic reports for a service
     *
     * @param Service $service
     * @return void
     */
    public static function updateReports(Service $service): void
    {
        Log::info("MechanicReportHelper: Updating reports for service #{$service->id} with status {$service->status}");
        
        // Get all mechanics for this service
        $mechanics = $service->mechanics;
        
        if ($mechanics->count() === 0) {
            Log::info("MechanicReportHelper: No mechanics found for service #{$service->id}");
            return;
        }
        
        Log::info("MechanicReportHelper: Service #{$service->id} has {$mechanics->count()} mechanics");
        
        // Process in a transaction to ensure consistency
        DB::transaction(function () use ($service, $mechanics) {
            // Get the week start and end dates
            $weekStart = now()->startOfWeek();
            $weekEnd = now()->endOfWeek();
            
            // Process each mechanic
            foreach ($mechanics as $mechanic) {
                // Update week dates if not set
                if (empty($mechanic->pivot->week_start) || empty($mechanic->pivot->week_end)) {
                    Log::info("MechanicReportHelper: Setting week dates for mechanic #{$mechanic->id}");
                    
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
                
                // If service is completed, ensure labor_cost is set
                if ($service->status === 'completed') {
                    // If labor_cost is not set or is 0, set a default value
                    if (empty($laborCost) || $laborCost == 0) {
                        $defaultLaborCost = 50000; // Default labor cost
                        Log::info("MechanicReportHelper: Setting default labor cost for mechanic #{$mechanic->id}: {$defaultLaborCost}");
                        
                        $service->mechanics()->updateExistingPivot($mechanic->id, [
                            'labor_cost' => $defaultLaborCost,
                        ]);
                        
                        $laborCost = $defaultLaborCost;
                    }
                } 
                // If service is cancelled or in_progress, set labor_cost to 0
                else if ($service->status === 'cancelled' || $service->status === 'in_progress') {
                    if ($laborCost > 0) {
                        Log::info("MechanicReportHelper: Setting labor cost to 0 for mechanic #{$mechanic->id} (service is {$service->status})");
                        
                        $service->mechanics()->updateExistingPivot($mechanic->id, [
                            'labor_cost' => 0,
                        ]);
                        
                        $laborCost = 0;
                    }
                }
                
                Log::info("MechanicReportHelper: Generating report for mechanic #{$mechanic->id} with labor cost {$laborCost}");
                
                // Force refresh mechanic from database
                $freshMechanic = Mechanic::find($mechanic->id);
                
                // Generate or update weekly report for this mechanic
                $report = $freshMechanic->generateWeeklyReport($weekStart, $weekEnd);
                
                Log::info("MechanicReportHelper: Report generated for mechanic #{$mechanic->id}", [
                    'report_id' => $report->id,
                    'services_count' => $report->services_count,
                    'total_labor_cost' => $report->total_labor_cost,
                ]);
            }
        });
    }
}
