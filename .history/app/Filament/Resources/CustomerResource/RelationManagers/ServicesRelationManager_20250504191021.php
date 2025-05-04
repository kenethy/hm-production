<?php

namespace App\Filament\Resources\CustomerResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
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
                        'Servis Berkala' => 'Servis Berkala',
                        'Tune Up Mesin' => 'Tune Up Mesin',
                        'Servis AC' => 'Servis AC',
                        'Ganti Oli' => 'Ganti Oli',
                        'Perbaikan Rem' => 'Perbaikan Rem',
                        'Balancing ' => 'Balancing & Spooring',
                        'Lainnya' => 'Lainnya',
                    ])
                    ->required(),

                Forms\Components\TextInput::make('car_model')
                    ->label('Model Mobil')
                    ->required()
                    ->maxLength(255),

                Forms\Components\TextInput::make('license_plate')
                    ->label('Plat Nomor')
                    ->maxLength(255),

                Forms\Components\Textarea::make('description')
                    ->label('Deskripsi')
                    ->rows(3),

                Forms\Components\Textarea::make('parts_used')
                    ->label('Sparepart yang Digunakan')
                    ->rows(3),

                Forms\Components\TextInput::make('labor_cost')
                    ->label('Biaya Jasa')
                    ->numeric()
                    ->prefix('Rp'),

                Forms\Components\TextInput::make('parts_cost')
                    ->label('Biaya Sparepart')
                    ->numeric()
                    ->prefix('Rp'),

                Forms\Components\Select::make('status')
                    ->label('Status')
                    ->options([
                        'in_progress' => 'Dalam Pengerjaan',
                        'completed' => 'Selesai',
                        'cancelled' => 'Dibatalkan',
                    ])
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
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('car_model')
                    ->label('Model Mobil')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('license_plate')
                    ->label('Plat Nomor')
                    ->searchable(),

                Tables\Columns\TextColumn::make('total_cost')
                    ->label('Total Biaya')
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

                Tables\Columns\TextColumn::make('completed_at')
                    ->label('Selesai Pada')
                    ->dateTime('d M Y H:i')
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat Pada')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
                    ->multiple(),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['customer_id'] = $this->ownerRecord->id;
                        $data['customer_name'] = $this->ownerRecord->name;
                        $data['phone'] = $this->ownerRecord->phone;
                        return $data;
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\Action::make('viewDetails')
                    ->label('Lihat Detail')
                    ->icon('heroicon-o-eye')
                    ->url(fn($record) => route('filament.admin.resources.services.edit', $record))
                    ->openUrlInNewTab(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
