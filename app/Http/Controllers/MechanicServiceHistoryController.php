<?php

namespace App\Http\Controllers;

use App\Models\MechanicReport;
use App\Models\Service;
use Illuminate\Http\Request;

class MechanicServiceHistoryController extends Controller
{
    /**
     * Display the service history for a mechanic report.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function show($id)
    {
        // Find the mechanic report
        $record = MechanicReport::findOrFail($id);
        
        // Get all services for this mechanic in this period
        $allServices = Service::query()
            ->join('mechanic_service', 'services.id', '=', 'mechanic_service.service_id')
            ->where('mechanic_service.mechanic_id', $record->mechanic_id)
            ->where('mechanic_service.week_start', $record->week_start)
            ->where('mechanic_service.week_end', $record->week_end)
            ->select('services.*', 'mechanic_service.invoice_number', 'mechanic_service.labor_cost')
            ->orderBy('services.created_at', 'desc')
            ->get();
        
        // Filter services by status (default: completed)
        $status = request()->query('status', 'completed');
        
        if ($status === 'all') {
            $services = $allServices;
        } else {
            $services = $allServices->where('status', $status);
        }
        
        return view('mechanic-services.history', compact('record', 'services', 'status'));
    }
}
