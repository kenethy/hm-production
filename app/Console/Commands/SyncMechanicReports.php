<?php

namespace App\Console\Commands;

use App\Models\Mechanic;
use App\Models\Service;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SyncMechanicReports extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mechanic:sync-reports {--force : Force rebuild all reports} {--mechanic_id= : Sync reports for specific mechanic} {--service_id= : Sync reports for specific service}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Synchronize mechanic reports with actual service data';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting mechanic reports synchronization...');
        Log::info('SyncMechanicReports: Starting mechanic reports synchronization');

        $force = $this->option('force');
        $mechanicId = $this->option('mechanic_id');
        $serviceId = $this->option('service_id');

        if ($force) {
            $this->info('Force rebuilding all reports...');
            Log::info('SyncMechanicReports: Force rebuilding all reports');
            
            // Truncate the mechanic_reports table
            DB::table('mechanic_reports')->truncate();
            
            // Process all completed services
            $this->processAllCompletedServices();
        } elseif ($mechanicId) {
            $this->info("Syncing reports for mechanic #{$mechanicId}...");
            Log::info("SyncMechanicReports: Syncing reports for mechanic #{$mechanicId}");
            
            // Process specific mechanic
            $this->processMechanic(Mechanic::find($mechanicId));
        } elseif ($serviceId) {
            $this->info("Syncing reports for service #{$serviceId}...");
            Log::info("SyncMechanicReports: Syncing reports for service #{$serviceId}");
            
            // Process specific service
            $this->processService(Service::find($serviceId));
        } else {
            $this->info('Validating and updating all mechanic reports...');
            Log::info('SyncMechanicReports: Validating and updating all mechanic reports');
            
            // Validate all mechanic reports
            $this->validateAllMechanicReports();
        }

        $this->info('Mechanic reports synchronization completed!');
        Log::info('SyncMechanicReports: Mechanic reports synchronization completed');
    }

    /**
     * Process all completed services.
     */
    private function processAllCompletedServices()
    {
        $completedServices = Service::where('status', 'completed')
            ->whereHas('mechanics')
            ->get();

        $this->info("Found {$completedServices->count()} completed services with mechanics");
        Log::info("SyncMechanicReports: Found {$completedServices->count()} completed services with mechanics");

        $bar = $this->output->createProgressBar($completedServices->count());
        $bar->start();

        foreach ($completedServices as $service) {
            $this->processService($service);
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
    }

    /**
     * Process a specific service.
     */
    private function processService($service)
    {
        if (!$service) {
            $this->error('Service not found');
            Log::error('SyncMechanicReports: Service not found');
            return;
        }

        // Skip if service is not completed
        if ($service->status !== 'completed') {
            Log::info("SyncMechanicReports: Skipping service #{$service->id} because status is {$service->status}");
            return;
        }

        // Skip if service has no mechanics
        if ($service->mechanics->count() === 0) {
            Log::info("SyncMechanicReports: Skipping service #{$service->id} because it has no mechanics");
            return;
        }

        Log::info("SyncMechanicReports: Processing service #{$service->id}");

        // Process each mechanic
        foreach ($service->mechanics as $mechanic) {
            try {
                // Check if mechanic is valid
                if (!$mechanic || !$mechanic->id) {
                    Log::error("SyncMechanicReports: Invalid mechanic for service #{$service->id}");
                    continue;
                }
                
                // Check if pivot exists
                if (!isset($mechanic->pivot)) {
                    Log::error("SyncMechanicReports: Pivot data missing for mechanic #{$mechanic->id} on service #{$service->id}");
                    
                    // Try to reload the relationship
                    $service = Service::with('mechanics')->find($service->id);
                    if (!$service) {
                        Log::error("SyncMechanicReports: Could not reload service #{$service->id}");
                        continue;
                    }
                    
                    // Find the mechanic in the reloaded service
                    $foundMechanic = false;
                    foreach ($service->mechanics as $reloadedMechanic) {
                        if ($reloadedMechanic->id == $mechanic->id) {
                            $mechanic = $reloadedMechanic;
                            $foundMechanic = true;
                            break;
                        }
                    }
                    
                    if (!$foundMechanic || !isset($mechanic->pivot)) {
                        Log::error("SyncMechanicReports: Still could not find pivot data for mechanic #{$mechanic->id} on service #{$service->id}");
                        continue;
                    }
                }
                
                // Set week dates if not set
                $weekStart = null;
                $weekEnd = null;
                
                if (empty($mechanic->pivot->week_start) || empty($mechanic->pivot->week_end)) {
                    $weekStart = Carbon::now()->startOfWeek()->format('Y-m-d');
                    $weekEnd = Carbon::now()->endOfWeek()->format('Y-m-d');
                    
                    Log::info("SyncMechanicReports: Setting week dates for mechanic #{$mechanic->id} on service #{$service->id}");
                    
                    $service->mechanics()->updateExistingPivot($mechanic->id, [
                        'week_start' => $weekStart,
                        'week_end' => $weekEnd,
                    ]);
                } else {
                    $weekStart = $mechanic->pivot->week_start;
                    $weekEnd = $mechanic->pivot->week_end;
                }
                
                // Set labor_cost if not set
                $laborCost = $mechanic->pivot->labor_cost ?? 0;
                if (empty($laborCost) || $laborCost == 0) {
                    $defaultLaborCost = 50000;
                    
                    Log::info("SyncMechanicReports: Setting default labor cost for mechanic #{$mechanic->id} on service #{$service->id}");
                    
                    $service->mechanics()->updateExistingPivot($mechanic->id, [
                        'labor_cost' => $defaultLaborCost,
                    ]);
                    
                    $laborCost = $defaultLaborCost;
                }
                
                // Verify week dates are set
                if (empty($weekStart) || empty($weekEnd)) {
                    Log::error("SyncMechanicReports: Week dates still not set for mechanic #{$mechanic->id} on service #{$service->id}");
                    
                    // Use current week as fallback
                    $weekStart = Carbon::now()->startOfWeek()->format('Y-m-d');
                    $weekEnd = Carbon::now()->endOfWeek()->format('Y-m-d');
                }
                
                // Update mechanic report
                $this->updateMechanicReport($mechanic, $weekStart, $weekEnd);
            } catch (\Exception $e) {
                Log::error("SyncMechanicReports: Error processing mechanic for service #{$service->id}: " . $e->getMessage(), [
                    'mechanic_id' => $mechanic->id ?? 'unknown',
                    'trace' => $e->getTraceAsString(),
                ]);
            }
        }
    }

    /**
     * Process a specific mechanic.
     */
    private function processMechanic($mechanic)
    {
        if (!$mechanic) {
            $this->error('Mechanic not found');
            Log::error('SyncMechanicReports: Mechanic not found');
            return;
        }

        Log::info("SyncMechanicReports: Processing mechanic #{$mechanic->id}");

        // Get all week periods for this mechanic
        $weekPeriods = DB::table('mechanic_service')
            ->where('mechanic_id', $mechanic->id)
            ->join('services', 'mechanic_service.service_id', '=', 'services.id')
            ->where('services.status', 'completed')
            ->select('mechanic_service.week_start', 'mechanic_service.week_end')
            ->distinct()
            ->get();

        foreach ($weekPeriods as $period) {
            $weekStart = $period->week_start;
            $weekEnd = $period->week_end;
            
            if (empty($weekStart) || empty($weekEnd)) {
                continue;
            }
            
            // Update mechanic report for this period
            $this->updateMechanicReport($mechanic, $weekStart, $weekEnd);
        }
    }

    /**
     * Update mechanic report for a specific period.
     */
    private function updateMechanicReport($mechanic, $weekStart, $weekEnd)
    {
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
        
        Log::info("SyncMechanicReports: Calculated for mechanic #{$mechanic->id}: services_count={$servicesCount}, total_labor_cost={$totalLaborCost}");
        
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
            
            Log::info("SyncMechanicReports: Updated report #{$report->id} for mechanic #{$mechanic->id}");
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
            
            Log::info("SyncMechanicReports: Created new report #{$reportId} for mechanic #{$mechanic->id}");
        }
    }

    /**
     * Validate all mechanic reports.
     */
    private function validateAllMechanicReports()
    {
        // Get all mechanic reports
        $reports = DB::table('mechanic_reports')->get();
        
        $this->info("Found {$reports->count()} mechanic reports to validate");
        Log::info("SyncMechanicReports: Found {$reports->count()} mechanic reports to validate");
        
        $bar = $this->output->createProgressBar($reports->count());
        $bar->start();
        
        foreach ($reports as $report) {
            // Calculate actual values
            $totalLaborCost = DB::table('mechanic_service')
                ->join('services', 'mechanic_service.service_id', '=', 'services.id')
                ->where('mechanic_service.mechanic_id', $report->mechanic_id)
                ->where('mechanic_service.week_start', $report->week_start)
                ->where('mechanic_service.week_end', $report->week_end)
                ->where('services.status', 'completed')
                ->sum('mechanic_service.labor_cost');
            
            $servicesCount = DB::table('mechanic_service')
                ->join('services', 'mechanic_service.service_id', '=', 'services.id')
                ->where('mechanic_service.mechanic_id', $report->mechanic_id)
                ->where('mechanic_service.week_start', $report->week_start)
                ->where('mechanic_service.week_end', $report->week_end)
                ->where('services.status', 'completed')
                ->count();
            
            // Check if values match
            if ($report->services_count != $servicesCount || $report->total_labor_cost != $totalLaborCost) {
                Log::info("SyncMechanicReports: Mismatch found for report #{$report->id}", [
                    'mechanic_id' => $report->mechanic_id,
                    'week_start' => $report->week_start,
                    'week_end' => $report->week_end,
                    'current_services_count' => $report->services_count,
                    'actual_services_count' => $servicesCount,
                    'current_total_labor_cost' => $report->total_labor_cost,
                    'actual_total_labor_cost' => $totalLaborCost,
                ]);
                
                // Update report with correct values
                DB::table('mechanic_reports')
                    ->where('id', $report->id)
                    ->update([
                        'services_count' => $servicesCount,
                        'total_labor_cost' => $totalLaborCost,
                        'updated_at' => now(),
                    ]);
                
                Log::info("SyncMechanicReports: Updated report #{$report->id} with correct values");
            }
            
            $bar->advance();
        }
        
        $bar->finish();
        $this->newLine();
    }
}
