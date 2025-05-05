<?php

namespace App\Filament\Resources\CustomerResource\RelationManagers;

use App\Models\Vehicle;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class VehiclesRelationManager extends RelationManager
{
    protected static string $relationship = 'vehicles';
    
    protected static ?string $recordTitleAttribute = 'model';
    
    protected static ?string $title = 'Kendaraan';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Kendaraan')
                    ->schema([
                        Forms\Components\TextInput::make('model')
                            ->label('Model Mobil')
                            ->required()
                            ->maxLength(255),
                            
                        Forms\Components\TextInput::make('license_plate')
                            ->label('Nomor Plat')
                            ->required()
                            ->maxLength(20)
                            ->unique(ignoreRecord: true),
                            
                        Forms\Components\TextInput::make('year')
                            ->label('Tahun Pembuatan')
                            ->maxLength(4),
                            
                        Forms\Components\TextInput::make('color')
                            ->label('Warna')
                            ->maxLength(50),
                    ])->columns(2),
                    
                Forms\Components\Section::make('Informasi Tambahan')
                    ->schema([
                        Forms\Components\TextInput::make('vin')
                            ->label('Nomor Rangka (VIN)')
                            ->maxLength(50),
                            
                        Forms\Components\TextInput::make('engine_number')
                            ->label('Nomor Mesin')
                            ->maxLength(50),
                            
                        Forms\Components\Select::make('transmission')
                            ->label('Transmisi')
                            ->options([
                                'manual' => 'Manual',
                                'automatic' => 'Otomatis',
                                'cvt' => 'CVT',
                                'dct' => 'DCT',
                            ]),
                            
                        Forms\Components\Select::make('fuel_type')
                            ->label('Bahan Bakar')
                            ->options([
                                'gasoline' => 'Bensin',
                                'diesel' => 'Solar',
                                'electric' => 'Listrik',
                                'hybrid' => 'Hybrid',
                            ]),
                            
                        Forms\Components\Toggle::make('is_active')
                            ->label('Aktif')
                            ->default(true),
                            
                        Forms\Components\Textarea::make('notes')
                            ->label('Catatan')
                            ->rows(3)
                            ->columnSpanFull(),
                    ])->columns(2),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('model')
            ->columns([
                Tables\Columns\TextColumn::make('model')
                    ->label('Model Mobil')
                    ->searchable()
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('license_plate')
                    ->label('Nomor Plat')
                    ->searchable()
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('year')
                    ->label('Tahun')
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('color')
                    ->label('Warna')
                    ->searchable(),
                    
                Tables\Columns\TextColumn::make('transmission')
                    ->label('Transmisi')
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'manual' => 'Manual',
                        'automatic' => 'Otomatis',
                        'cvt' => 'CVT',
                        'dct' => 'DCT',
                        default => $state,
                    })
                    ->sortable(),
                    
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean()
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d F Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('transmission')
                    ->label('Transmisi')
                    ->options([
                        'manual' => 'Manual',
                        'automatic' => 'Otomatis',
                        'cvt' => 'CVT',
                        'dct' => 'DCT',
                    ]),
                    
                Tables\Filters\SelectFilter::make('fuel_type')
                    ->label('Bahan Bakar')
                    ->options([
                        'gasoline' => 'Bensin',
                        'diesel' => 'Solar',
                        'electric' => 'Listrik',
                        'hybrid' => 'Hybrid',
                    ]),
                    
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Aktif')
                    ->default(true),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title('Kendaraan berhasil ditambahkan')
                            ->body('Kendaraan baru telah berhasil ditambahkan untuk pelanggan ini.')
                    ),
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
