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
                //
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
