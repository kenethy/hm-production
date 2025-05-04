<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PromoResource\Pages;
use App\Filament\Resources\PromoResource\RelationManagers;
use App\Models\Promo;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PromoResource extends Resource
{
    protected static ?string $model = Promo::class;

    protected static ?string $navigationIcon = 'heroicon-o-megaphone';

    protected static ?string $navigationLabel = 'Promo';

    protected static ?string $modelLabel = 'Promo';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Promo')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->label('Judul Promo')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\Textarea::make('description')
                            ->label('Deskripsi')
                            ->required()
                            ->rows(4),

                        Forms\Components\FileUpload::make('image_path')
                            ->label('Gambar Promo')
                            ->image()
                            ->directory('promos')
                            ->visibility('public')
                            ->imageResizeMode('cover')
                            ->imageCropAspectRatio('16:9')
                            ->imageResizeTargetWidth('1200')
                            ->imageResizeTargetHeight('675'),
                    ])->columns(1),

                Forms\Components\Section::make('Harga & Diskon')
                    ->schema([
                        Forms\Components\TextInput::make('original_price')
                            ->label('Harga Asli')
                            ->numeric()
                            ->prefix('Rp'),

                        Forms\Components\TextInput::make('promo_price')
                            ->label('Harga Promo')
                            ->numeric()
                            ->prefix('Rp')
                            ->afterStateUpdated(function ($state, callable $set, $get) {
                                if ($get('original_price') && $state) {
                                    $originalPrice = $get('original_price');
                                    $discount = round((($originalPrice - $state) / $originalPrice) * 100);
                                    $set('discount_percentage', $discount);
                                }
                            }),

                        Forms\Components\TextInput::make('discount_percentage')
                            ->label('Persentase Diskon')
                            ->suffix('%')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(100)
                            ->afterStateUpdated(function ($state, callable $set, $get) {
                                if ($get('original_price') && $state) {
                                    $originalPrice = $get('original_price');
                                    $promoPrice = $originalPrice - ($originalPrice * $state / 100);
                                    $set('promo_price', round($promoPrice, 2));
                                }
                            }),
                    ])->columns(3),

                Forms\Components\Section::make('Periode & Status')
                    ->schema([
                        Forms\Components\DateTimePicker::make('start_date')
                            ->label('Tanggal Mulai')
                            ->required(),

                        Forms\Components\DateTimePicker::make('end_date')
                            ->label('Tanggal Berakhir')
                            ->required()
                            ->after('start_date'),

                        Forms\Components\TextInput::make('promo_code')
                            ->label('Kode Promo')
                            ->maxLength(50),

                        Forms\Components\TextInput::make('remaining_slots')
                            ->label('Sisa Slot')
                            ->numeric()
                            ->minValue(0)
                            ->helperText('Kosongkan jika tidak ada batasan slot'),

                        Forms\Components\Toggle::make('is_featured')
                            ->label('Tampilkan di Halaman Utama')
                            ->default(false),

                        Forms\Components\Toggle::make('is_active')
                            ->label('Aktif')
                            ->default(true),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image_path')
                    ->label('Gambar')
                    ->square(),

                Tables\Columns\TextColumn::make('title')
                    ->label('Judul')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('original_price')
                    ->label('Harga Asli')
                    ->money('IDR')
                    ->sortable(),

                Tables\Columns\TextColumn::make('promo_price')
                    ->label('Harga Promo')
                    ->money('IDR')
                    ->sortable(),

                Tables\Columns\TextColumn::make('discount_percentage')
                    ->label('Diskon')
                    ->formatStateUsing(fn(int $state): string => "{$state}%")
                    ->sortable(),

                Tables\Columns\TextColumn::make('remaining_slots')
                    ->label('Sisa Slot')
                    ->formatStateUsing(function ($state) {
                        if ($state === null) {
                            return 'Tidak Terbatas';
                        }
                        return $state;
                    }),

                Tables\Columns\TextColumn::make('end_date')
                    ->label('Berakhir Pada')
                    ->dateTime('d M Y H:i')
                    ->sortable(),

                Tables\Columns\IconColumn::make('is_featured')
                    ->label('Featured')
                    ->boolean(),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('is_active')
                    ->label('Status')
                    ->options([
                        '1' => 'Aktif',
                        '0' => 'Tidak Aktif',
                    ]),

                Tables\Filters\Filter::make('is_featured')
                    ->label('Featured')
                    ->query(fn(Builder $query): Builder => $query->where('is_featured', true)),

                Tables\Filters\Filter::make('active_period')
                    ->label('Periode Aktif')
                    ->query(fn(Builder $query): Builder => $query->where('start_date', '<=', now())->where('end_date', '>=', now())),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make('toggleFeatured')
                        ->label('Toggle Featured')
                        ->icon('heroicon-o-star')
                        ->action(function (Collection $records): void {
                            foreach ($records as $record) {
                                $record->update(['is_featured' => !$record->is_featured]);
                            }
                        }),
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
            'index' => Pages\ListPromos::route('/'),
            'create' => Pages\CreatePromo::route('/create'),
            'edit' => Pages\EditPromo::route('/{record}/edit'),
        ];
    }
}
