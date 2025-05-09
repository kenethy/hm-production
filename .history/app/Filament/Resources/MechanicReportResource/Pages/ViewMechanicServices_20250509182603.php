<?php

namespace App\Filament\Resources\MechanicReportResource\Pages;

use App\Filament\Resources\MechanicReportResource;
use App\Models\MechanicReport;
use App\Models\Service;
use Filament\Actions;
use Filament\Resources\Pages\Page;
use Filament\Tables;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ViewMechanicServices extends Page
{
    protected static string $resource = MechanicReportResource::class;

    protected static string $view = 'filament.resources.mechanic-report-resource.pages.view-mechanic-services';

    public MechanicReport $record;

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Service::query()
                    ->join('mechanic_service', 'services.id', '=', 'mechanic_service.service_id')
                    ->where('mechanic_service.mechanic_id', $this->record->mechanic_id)
                    ->where('mechanic_service.week_start', $this->record->week_start)
                    ->where('mechanic_service.week_end', $this->record->week_end)
                    ->select('services.*', 'mechanic_service.invoice_number', 'mechanic_service.labor_cost')
            )
            ->columns([
                Tables\Columns\TextColumn::make('service_type')
                    ->label('Jenis Servis')
                    ->searchable(),

                Tables\Columns\TextColumn::make('customer_name')
                    ->label('Nama Pelanggan')
                    ->searchable(),

                Tables\Columns\TextColumn::make('license_plate')
                    ->label('Nomor Plat')
                    ->searchable(),

                Tables\Columns\TextColumn::make('invoice_number')
                    ->label('Nomor Nota')
                    ->searchable(),

                Tables\Columns\TextColumn::make('labor_cost')
                    ->label('Biaya Jasa')
                    ->money('IDR')
                    ->sortable(),

                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'in_progress' => 'warning',
                        'completed' => 'success',
                        'cancelled' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'in_progress' => 'Dalam Pengerjaan',
                        'completed' => 'Selesai',
                        'cancelled' => 'Dibatalkan',
                        default => $state,
                    }),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tanggal Masuk')
                    ->dateTime('d M Y H:i')
                    ->sortable(),

                Tables\Columns\TextColumn::make('completed_at')
                    ->label('Tanggal Selesai')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'in_progress' => 'Dalam Pengerjaan',
                        'completed' => 'Selesai',
                        'cancelled' => 'Dibatalkan',
                    ])
                    ->placeholder('Semua Status')
                    ->multiple()
                    ->default(['completed']),
            ])
            ->filtersFormWidth('sm')
            ->tabs([
                'completed' => fn(Table $table): Table => $table
                    ->modifyQueryUsing(fn(Builder $query) => $query->where('status', 'completed'))
                    ->heading('Selesai'),
                'in_progress' => fn(Table $table): Table => $table
                    ->modifyQueryUsing(fn(Builder $query) => $query->where('status', 'in_progress'))
                    ->heading('Dalam Pengerjaan'),
                'cancelled' => fn(Table $table): Table => $table
                    ->modifyQueryUsing(fn(Builder $query) => $query->where('status', 'cancelled'))
                    ->heading('Dibatalkan'),
                'all' => fn(Table $table): Table => $table
                    ->heading('Semua Status'),
            ])
            ->tabLabel('completed', 'Selesai')
            ->tabLabel('in_progress', 'Dalam Pengerjaan')
            ->tabLabel('cancelled', 'Dibatalkan')
            ->tabLabel('all', 'Semua Status')
            ->persistTabInQueryString()
            ->defaultTab('completed')
            ->defaultSort('created_at', 'desc')
            ->actions([
                // View action to see service details
                Tables\Actions\ViewAction::make()
                    ->url(fn(Service $record): string => route('filament.admin.resources.services.edit', ['record' => $record])),
            ]);
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
