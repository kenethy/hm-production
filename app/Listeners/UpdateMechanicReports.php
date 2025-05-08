<?php

namespace App\Listeners;

use App\Events\ServiceUpdated;
use App\Models\Mechanic;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UpdateMechanicReports implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     *
     * @param  \App\Events\ServiceUpdated  $event
     * @return void
     */
    public function handle(ServiceUpdated $event)
    {
        $service = $event->service;
        
        Log::info("UpdateMechanicReports: Processing service #{$service->id} with status {$service->status}");
        
        // Get all mechanics for this service
        $mechanics = $service->mechanics;
        
        if ($mechanics->count() === 0) {
            Log::info("UpdateMechanicReports: No mechanics found for service #{$service->id}");
            return;
        }
        
        Log::info("UpdateMechanicReports: Service #{$service->id} has {$mechanics->count()} mechanics");
        
        // Process in a transaction to ensure consistency
        DB::transaction(function () use ($service, $mechanics) {
            // Get the week start and end dates
            $weekStart = now()->startOfWeek();
            $weekEnd = now()->endOfWeek();
            
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
                
                // If service is completed, ensure labor_cost is set
                if ($service->status === 'completed') {
                    // If labor_cost is not set or is 0, set a default value
                    if (empty($laborCost) || $laborCost == 0) {
                        $defaultLaborCost = 50000; // Default labor cost
                        Log::info("UpdateMechanicReports: Setting default labor cost for mechanic #{$mechanic->id}: {$defaultLaborCost}");
                        
                        $service->mechanics()->updateExistingPivot($mechanic->id, [
                            'labor_cost' => $defaultLaborCost,
                        ]);
                        
                        $laborCost = $defaultLaborCost;
                    }
                } 
                // If service is cancelled or in_progress, set labor_cost to 0
                else if ($service->status === 'cancelled' || $service->status === 'in_progress') {
                    if ($laborCost > 0) {
                        Log::info("UpdateMechanicReports: Setting labor cost to 0 for mechanic #{$mechanic->id} (service is {$service->status})");
                        
                        $service->mechanics()->updateExistingPivot($mechanic->id, [
                            'labor_cost' => 0,
                        ]);
                        
                        $laborCost = 0;
                    }
                }
                
                Log::info("UpdateMechanicReports: Generating report for mechanic #{$mechanic->id} with labor cost {$laborCost}");
                
                // Generate or update weekly report for this mechanic
                $this->generateOrUpdateReport($mechanic, $weekStart, $weekEnd);
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
            Log::info("UpdateMechanicReports: Generating report for mechanic #{$mechanic->id} for week {$weekStart} to {$weekEnd}");
            
            // Force refresh mechanic from database
            $freshMechanic = Mechanic::find($mechanic->id);
            
            // Generate or update weekly report for this mechanic
            $report = $freshMechanic->generateWeeklyReport($weekStart, $weekEnd);
            
            Log::info("UpdateMechanicReports: Report generated for mechanic #{$mechanic->id}", [
                'report_id' => $report->id,
                'services_count' => $report->services_count,
                'total_labor_cost' => $report->total_labor_cost,
            ]);
        } catch (\Exception $e) {
            Log::error("UpdateMechanicReports: Error generating report for mechanic #{$mechanic->id}: " . $e->getMessage(), [
                'mechanic_id' => $mechanic->id,
                'week_start' => $weekStart,
                'week_end' => $weekEnd,
                'exception' => $e
            ]);
        }
    }
}
