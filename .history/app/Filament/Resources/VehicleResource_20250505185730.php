<?php

namespace App\Filament\Resources;

use App\Filament\Resources\VehicleResource\Pages;
use App\Filament\Resources\VehicleResource\RelationManagers;
use App\Models\Customer;
use App\Models\Vehicle;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;

class VehicleResource extends Resource
{
    protected static ?string $model = Vehicle::class;

    protected static ?string $navigationIcon = 'heroicon-o-truck';

    protected static ?string $navigationGroup = 'Manajemen Pelanggan';

    protected static ?string $navigationLabel = 'Kendaraan';

    protected static ?string $modelLabel = 'Kendaraan';

    protected static ?int $navigationSort = 15;

    public static function canAccess(): bool
    {
        $user = Auth::user();
        return $user && $user->role === 'admin';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Kendaraan')
                    ->schema([
                        Forms\Components\Select::make('customer_id')
                            ->label('Pelanggan')
                            ->relationship('customer', 'name')
                            ->searchable()
                            ->preload()
                            ->createOptionForm([
                                Forms\Components\TextInput::make('name')
                                    ->label('Nama')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('phone')
                                    ->label('Nomor Telepon')
                                    ->required()
                                    ->tel()
                                    ->maxLength(20),
                                Forms\Components\TextInput::make('email')
                                    ->label('Email')
                                    ->email()
                                    ->maxLength(255),
                                Forms\Components\Textarea::make('address')
                                    ->label('Alamat')
                                    ->rows(3)
                                    ->maxLength(500),
                            ])
                            ->required(),

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

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('customer.name')
                    ->label('Pelanggan')
                    ->searchable()
                    ->sortable(),

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
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'manual' => 'Manual',
                        'automatic' => 'Otomatis',
                        'cvt' => 'CVT',
                        'dct' => 'DCT',
                        default => $state,
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('fuel_type')
                    ->label('Bahan Bakar')
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'gasoline' => 'Bensin',
                        'diesel' => 'Solar',
                        'electric' => 'Listrik',
                        'hybrid' => 'Hybrid',
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

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Diperbarui')
                    ->dateTime('d F Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('customer_id')
                    ->label('Pelanggan')
                    ->relationship('customer', 'name')
                    ->searchable()
                    ->preload(),

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

                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\RestoreAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListVehicles::route('/'),
            'create' => Pages\CreateVehicle::route('/create'),
            'edit' => Pages\EditVehicle::route('/{record}/edit'),
        ];
    }
}
