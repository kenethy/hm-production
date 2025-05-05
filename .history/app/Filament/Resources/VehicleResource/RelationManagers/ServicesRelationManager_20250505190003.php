<?php

namespace App\Filament\Resources\VehicleResource\RelationManagers;

use App\Models\Service;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ServicesRelationManager extends RelationManager
{
    protected static string $relationship = 'services';

    protected static ?string $recordTitleAttribute = 'service_type';

    protected static ?string $title = 'Riwayat Servis';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('service_type')
                    ->label('Jenis Servis')
                    ->options([
                        'tune_up' => 'Tune Up',
                        'oil_change' => 'Ganti Oli',
                        'brake_service' => 'Servis Rem',
                        'tire_service' => 'Servis Ban',
                        'battery_service' => 'Servis Aki',
                        'ac_service' => 'Servis AC',
                        'electrical_repair' => 'Perbaikan Kelistrikan',
                        'engine_repair' => 'Perbaikan Mesin',
                        'transmission_repair' => 'Perbaikan Transmisi',
                        'balancing' => 'Balancing',
                        'car_wash' => 'Cuci',
                        'other' => 'Lainnya',
                    ])
                    ->required(),

                Forms\Components\Textarea::make('description')
                    ->label('Deskripsi')
                    ->rows(3),

                Forms\Components\TextInput::make('labor_cost')
                    ->label('Biaya Jasa')
                    ->numeric()
                    ->prefix('Rp')
                    ->default(0),

                Forms\Components\TextInput::make('parts_cost')
                    ->label('Biaya Sparepart')
                    ->numeric()
                    ->prefix('Rp')
                    ->default(0),

                Forms\Components\Select::make('status')
                    ->label('Status')
                    ->options([
                        'in_progress' => 'Dalam Pengerjaan',
                        'completed' => 'Selesai',
                        'cancelled' => 'Dibatalkan',
                    ])
                    ->default('in_progress')
                    ->required(),

                Forms\Components\Textarea::make('notes')
                    ->label('Catatan')
                    ->rows(3),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('service_type')
            ->columns([
                Tables\Columns\TextColumn::make('service_type')
                    ->label('Jenis Servis')
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'tune_up' => 'Tune Up',
                        'oil_change' => 'Ganti Oli',
                        'brake_service' => 'Servis Rem',
                        'tire_service' => 'Servis Ban',
                        'battery_service' => 'Servis Aki',
                        'ac_service' => 'Servis AC',
                        'electrical_repair' => 'Perbaikan Kelistrikan',
                        'engine_repair' => 'Perbaikan Mesin',
                        'transmission_repair' => 'Perbaikan Transmisi',
                        'balancing' => 'Balancing',
                        'car_wash' => 'Cuci',
                        'other' => 'Lainnya',
                        default => $state,
                    })
                    ->searchable(),

                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'in_progress' => 'Dalam Pengerjaan',
                        'completed' => 'Selesai',
                        'cancelled' => 'Dibatalkan',
                        default => $state,
                    })
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'in_progress' => 'warning',
                        'completed' => 'success',
                        'cancelled' => 'danger',
                        default => 'secondary',
                    }),

                Tables\Columns\TextColumn::make('total_cost')
                    ->label('Total Biaya')
                    ->money('IDR')
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tanggal')
                    ->dateTime('d F Y H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'in_progress' => 'Dalam Pengerjaan',
                        'completed' => 'Selesai',
                        'cancelled' => 'Dibatalkan',
                    ]),

                Tables\Filters\SelectFilter::make('service_type')
                    ->label('Jenis Servis')
                    ->options([
                        'tune_up' => 'Tune Up',
                        'oil_change' => 'Ganti Oli',
                        'brake_service' => 'Servis Rem',
                        'tire_service' => 'Servis Ban',
                        'battery_service' => 'Servis Aki',
                        'ac_service' => 'Servis AC',
                        'electrical_repair' => 'Perbaikan Kelistrikan',
                        'engine_repair' => 'Perbaikan Mesin',
                        'transmission_repair' => 'Perbaikan Transmisi',
                        'balancing' => 'Balancing',
                        'car_wash' => 'Cuci',
                        'other' => 'Lainnya',
                    ]),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->using(function (array $data, string $model): Service {
                        $data['customer_id'] = $this->getOwnerRecord()->customer_id;
                        $data['customer_name'] = $this->getOwnerRecord()->customer->name;
                        $data['phone'] = $this->getOwnerRecord()->customer->phone;
                        $data['vehicle_id'] = $this->getOwnerRecord()->id;
                        $data['car_model'] = $this->getOwnerRecord()->model;
                        $data['license_plate'] = $this->getOwnerRecord()->license_plate;
                        $data['total_cost'] = $data['labor_cost'] + $data['parts_cost'];
                        $data['entry_time'] = now();

                        return $model::create($data);
                    })
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title('Servis berhasil dibuat')
                            ->body('Servis baru telah berhasil dibuat untuk kendaraan ini.')
                    ),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
