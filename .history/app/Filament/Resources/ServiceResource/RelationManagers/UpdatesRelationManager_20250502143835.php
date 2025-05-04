<?php

namespace App\Filament\Resources\ServiceResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Storage;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class UpdatesRelationManager extends RelationManager
{
    protected static string $relationship = 'updates';

    protected static ?string $title = 'Update Servis';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('update_type')
                    ->label('Jenis Update')
                    ->options([
                        'inspection' => 'Inspeksi Awal',
                        'in_progress' => 'Proses Pengerjaan',
                        'parts_replaced' => 'Penggantian Sparepart',
                        'testing' => 'Pengujian',
                        'completed' => 'Selesai',
                        'other' => 'Lainnya',
                    ])
                    ->default('in_progress')
                    ->required(),

                Forms\Components\TextInput::make('title')
                    ->label('Judul Update')
                    ->required()
                    ->maxLength(255)
                    ->placeholder('Contoh: Penggantian Oli Mesin')
                    ->afterStateUpdated(function ($state, callable $set, $get) {
                        if (!$get('description')) {
                            $updateType = $get('update_type');
                            $description = match ($updateType) {
                                'inspection' => "Kami telah melakukan inspeksi awal pada kendaraan Anda. {$state}",
                                'in_progress' => "Saat ini kami sedang mengerjakan {$state} pada kendaraan Anda.",
                                'parts_replaced' => "Kami telah melakukan {$state} dengan sparepart baru.",
                                'testing' => "Kami sedang melakukan pengujian setelah {$state}.",
                                'completed' => "Proses {$state} telah selesai dilakukan.",
                                default => $state,
                            };
                            $set('description', $description);
                        }
                    }),

                Forms\Components\Textarea::make('description')
                    ->label('Deskripsi')
                    ->required()
                    ->rows(4)
                    ->placeholder('Jelaskan detail update yang dilakukan'),

                Forms\Components\FileUpload::make('image_path')
                    ->label('Foto Update')
                    ->image()
                    ->directory('service-updates')
                    ->visibility('public')
                    ->imageResizeMode('cover')
                    ->imageCropAspectRatio('16:9')
                    ->imageResizeTargetWidth('1200')
                    ->imageResizeTargetHeight('675'),

                Forms\Components\Toggle::make('sent_to_customer')
                    ->label('Kirim ke Pelanggan')
                    ->helperText('Aktifkan untuk mengirim update ini ke pelanggan via WhatsApp')
                    ->default(false),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('title')
            ->columns([
                Tables\Columns\TextColumn::make('title'),
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
