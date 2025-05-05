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
            ->recordTitleAttribute('service')
            ->columns([
                Tables\Columns\TextColumn::make('service'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
