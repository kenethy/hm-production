<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BookingResource\Pages;
use App\Filament\Resources\BookingResource\RelationManagers;
use App\Models\Booking;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class BookingResource extends Resource
{
    protected static ?string $model = Booking::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar';

    protected static ?string $navigationLabel = 'Bookings';

    protected static ?string $modelLabel = 'Booking Servis';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Nama')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('phone')
                    ->label('Nomor Telepon')
                    ->required()
                    ->tel()
                    ->maxLength(20),
                Forms\Components\TextInput::make('car_model')
                    ->label('Model Mobil')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Select::make('service_type')
                    ->label('Jenis Servis')
                    ->options([
                        'Servis Berkala' => 'Servis Berkala',
                        'Tune Up Mesin' => 'Tune Up Mesin',
                        'Servis AC' => 'Servis AC',
                        'Ganti Oli' => 'Ganti Oli',
                        'Perbaikan Rem' => 'Perbaikan Rem',
                        'Balancing & Spooring' => 'Balancing & Spooring',
                        'Lainnya' => 'Lainnya',
                    ])
                    ->required(),
                Forms\Components\DatePicker::make('date')
                    ->label('Tanggal Servis')
                    ->required(),
                Forms\Components\Select::make('time')
                    ->label('Waktu Servis')
                    ->options([
                        '08:00' => '08:00',
                        '09:00' => '09:00',
                        '10:00' => '10:00',
                        '11:00' => '11:00',
                        '13:00' => '13:00',
                        '14:00' => '14:00',
                        '15:00' => '15:00',
                        '16:00' => '16:00',
                    ])
                    ->required(),
                Forms\Components\Textarea::make('message')
                    ->label('Pesan Tambahan')
                    ->maxLength(65535)
                    ->columnSpanFull(),
                Forms\Components\Select::make('status')
                    ->label('Status')
                    ->options([
                        'pending' => 'Menunggu Konfirmasi',
                        'confirmed' => 'Terkonfirmasi',
                        'completed' => 'Selesai',
                        'cancelled' => 'Dibatalkan',
                    ])
                    ->default('pending')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListBookings::route('/'),
            'create' => Pages\CreateBooking::route('/create'),
            'edit' => Pages\EditBooking::route('/{record}/edit'),
        ];
    }
}
