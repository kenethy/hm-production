<?php

namespace App\Filament\Widgets;

use App\Models\Customer;
use App\Models\Service;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class CustomerChartWidget extends ChartWidget
{
    protected static ?string $heading = 'Pertumbuhan Pelanggan';

    protected function getData(): array
    {
        $data = Customer::select(
            DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
            DB::raw('COUNT(*) as count')
        )
            ->where('created_at', '>=', now()->subMonths(12))
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $labels = [];
        $counts = [];

        // Create an array of the last 12 months
        for ($i = 11; $i >= 0; $i--) {
            $month = now()->subMonths($i)->format('Y-m');
            $labels[] = now()->subMonths($i)->format('M Y');
            
            $monthData = $data->firstWhere('month', $month);
            $counts[] = $monthData ? $monthData->count : 0;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Pelanggan Baru',
                    'data' => $counts,
                    'backgroundColor' => '#36A2EB',
                    'borderColor' => '#36A2EB',
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
