<?php

namespace App\Filament\Resources\MechanicReportResource\Pages;

use App\Filament\Resources\MechanicReportResource;
use App\Models\MechanicReport;
use App\Models\Service;
use Filament\Actions;
use Filament\Resources\Pages\Page;

class ViewMechanicServices extends Page
{
    protected static string $resource = MechanicReportResource::class;

    protected static string $view = 'filament.resources.mechanic-report-resource.pages.view-mechanic-services-simple';

    public MechanicReport $record;

    public $services = [];

    public function mount(int | string $record): void
    {
        $this->record = MechanicReport::findOrFail($record);

        $this->services = Service::query()
            ->join('mechanic_service', 'services.id', '=', 'mechanic_service.service_id')
            ->where('mechanic_service.mechanic_id', $this->record->mechanic_id)
            ->where('mechanic_service.week_start', $this->record->week_start)
            ->where('mechanic_service.week_end', $this->record->week_end)
            ->select('services.*', 'mechanic_service.invoice_number', 'mechanic_service.labor_cost')
            ->orderBy('services.created_at', 'desc')
            ->get();
    }
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('back')
                ->label('Kembali')
                ->url(fn() => MechanicReportResource::getUrl('edit', ['record' => $this->record]))
                ->color('gray'),
        ];
    }
}
