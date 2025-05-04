<?php

namespace App\Filament\Widgets;

use App\Models\Customer;
use App\Models\Service;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class CustomerStatsWidget extends BaseWidget
{
    protected static ?string $pollingInterval = null;

    protected function getStats(): array
    {
        $totalCustomers = Customer::count();
        $activeCustomers = Customer::where('is_active', true)->count();
        $inactiveCustomers = $totalCustomers - $activeCustomers;
        
        $customersWithService = Customer::where('service_count', '>', 0)->count();
        $customersWithoutService = Customer::where('service_count', 0)->count();
        
        $totalRevenue = Service::sum('total_cost');
        $averageRevenuePerCustomer = $customersWithService > 0 
            ? $totalRevenue / $customersWithService 
            : 0;
        
        $topCities = Customer::select('city', DB::raw('count(*) as total'))
            ->whereNotNull('city')
            ->groupBy('city')
            ->orderByDesc('total')
            ->limit(3)
            ->get();
        
        $topCitiesText = $topCities->map(function ($city) {
            return "{$city->city}: {$city->total}";
        })->join(', ');
        
        return [
            Stat::make('Total Pelanggan', $totalCustomers)
                ->description('Jumlah seluruh pelanggan')
                ->descriptionIcon('heroicon-m-users')
                ->color('primary'),
                
            Stat::make('Pelanggan Aktif', $activeCustomers)
                ->description($activeCustomers > 0 ? number_format($activeCustomers / $totalCustomers * 100, 1) . '% dari total' : '0% dari total')
                ->descriptionIcon('heroicon-m-user-circle')
                ->color('success'),
                
            Stat::make('Pelanggan Tidak Aktif', $inactiveCustomers)
                ->description($inactiveCustomers > 0 ? number_format($inactiveCustomers / $totalCustomers * 100, 1) . '% dari total' : '0% dari total')
                ->descriptionIcon('heroicon-m-user-minus')
                ->color('danger'),
                
            Stat::make('Pelanggan dengan Servis', $customersWithService)
                ->description($customersWithService > 0 ? number_format($customersWithService / $totalCustomers * 100, 1) . '% dari total' : '0% dari total')
                ->descriptionIcon('heroicon-m-wrench-screwdriver')
                ->color('success'),
                
            Stat::make('Pelanggan Belum Servis', $customersWithoutService)
                ->description($customersWithoutService > 0 ? number_format($customersWithoutService / $totalCustomers * 100, 1) . '% dari total' : '0% dari total')
                ->descriptionIcon('heroicon-m-exclamation-circle')
                ->color('warning'),
                
            Stat::make('Rata-rata Pengeluaran', 'Rp ' . number_format($averageRevenuePerCustomer, 0, ',', '.'))
                ->description('Per pelanggan')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success'),
                
            Stat::make('Kota Terbanyak', $topCities->isNotEmpty() ? $topCities->first()->city : '-')
                ->description($topCitiesText)
                ->descriptionIcon('heroicon-m-map-pin')
                ->color('primary'),
        ];
    }
}
