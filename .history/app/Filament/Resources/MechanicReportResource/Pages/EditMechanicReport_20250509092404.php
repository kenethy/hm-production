<?php

namespace App\Filament\Resources\MechanicReportResource\Pages;

use App\Filament\Resources\MechanicReportResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Artisan;

class EditMechanicReport extends EditRecord
{
    protected static string $resource = MechanicReportResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
