<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\CustomerChartWidget;
use App\Filament\Widgets\CustomerStatsWidget;
use App\Filament\Widgets\CustomersNeedingFollowUpWidget;
use Filament\Pages\Page;

class CustomerDashboard extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-presentation-chart-line';

    protected static string $view = 'filament.pages.customer-dashboard';
    
    protected static ?string $navigationLabel = 'Dashboard Pelanggan';
    
    protected static ?string $title = 'Dashboard Pelanggan';
    
    protected static ?string $navigationGroup = 'Manajemen Pelanggan';
    
    protected static ?int $navigationSort = 0;

    protected function getHeaderWidgets(): array
    {
        return [
            CustomerStatsWidget::class,
            CustomerChartWidget::class,
        ];
    }

    protected function getFooterWidgets(): array
    {
        return [
            CustomersNeedingFollowUpWidget::class,
        ];
    }
}
