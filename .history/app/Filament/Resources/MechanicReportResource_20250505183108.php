<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MechanicReportResource\Pages;
use App\Filament\Resources\MechanicReportResource\RelationManagers;
use App\Models\Mechanic;
use App\Models\MechanicReport;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;

class MechanicReportResource extends Resource
{
    protected static ?string $model = MechanicReport::class;

    protected static ?string $navigationIcon = 'heroicon-o-calculator';

    protected static ?string $navigationGroup = 'Servis & Booking';

    protected static ?string $navigationLabel = 'Rekap Montir';

    protected static ?string $modelLabel = 'Rekap Montir';

    protected static ?int $navigationSort = 3;

    public static function canAccess(): bool
    {
        $user = Auth::user();
        return $user && ($user->role === 'admin' || $user->role === 'staff');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Rekap')
                    ->schema([
                        Forms\Components\Select::make('mechanic_id')
                            ->label('Montir')
                            ->relationship('mechanic', 'name')
                            ->required()
                            ->searchable()
                            ->preload(),

                        Forms\Components\DatePicker::make('week_start')
                            ->label('Tanggal Mulai Minggu')
                            ->required()
                            ->default(fn() => Carbon::now()->startOfWeek())
                            ->disabled(),

                        Forms\Components\DatePicker::make('week_end')
                            ->label('Tanggal Akhir Minggu')
                            ->required()
                            ->default(fn() => Carbon::now()->endOfWeek())
                            ->disabled(),

                        Forms\Components\TextInput::make('services_count')
                            ->label('Jumlah Servis')
                            ->numeric()
                            ->default(0)
                            ->disabled(),

                        Forms\Components\TextInput::make('total_labor_cost')
                            ->label('Total Biaya Jasa')
                            ->numeric()
                            ->prefix('Rp')
                            ->disabled(),
                    ])->columns(2),

                Forms\Components\Section::make('Status Pembayaran')
                    ->schema([
                        Forms\Components\Toggle::make('is_paid')
                            ->label('Sudah Dibayar')
                            ->default(false)
                            ->reactive()
                            ->afterStateUpdated(function ($state, Forms\Set $set) {
                                if ($state) {
                                    $set('paid_at', now());
                                } else {
                                    $set('paid_at', null);
                                }
                            }),

                        Forms\Components\DateTimePicker::make('paid_at')
                            ->label('Tanggal Pembayaran')
                            ->visible(fn(callable $get) => $get('is_paid'))
                            ->disabled(),

                        Forms\Components\Textarea::make('notes')
                            ->label('Catatan')
                            ->placeholder('Catatan tambahan tentang pembayaran')
                            ->rows(3)
                            ->columnSpanFull(),
                    ])->columns(2),
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
            'index' => Pages\ListMechanicReports::route('/'),
            'create' => Pages\CreateMechanicReport::route('/create'),
            'edit' => Pages\EditMechanicReport::route('/{record}/edit'),
        ];
    }
}
