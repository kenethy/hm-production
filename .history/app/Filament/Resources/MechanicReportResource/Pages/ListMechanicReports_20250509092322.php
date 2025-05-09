<?php

namespace App\Filament\Resources\MechanicReportResource\Pages;

use App\Filament\Resources\MechanicReportResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Artisan;

class ListMechanicReports extends ListRecords
{
    protected static string $resource = MechanicReportResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
