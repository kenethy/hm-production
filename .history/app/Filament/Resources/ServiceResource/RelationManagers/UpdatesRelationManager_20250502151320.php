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

    // Variabel untuk menyimpan URL WhatsApp yang akan dibuka setelah membuat update
    public $whatsappUrl = null;

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
                Tables\Columns\TextColumn::make('update_type')
                    ->label('Jenis Update')
                    ->badge()
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'inspection' => 'Inspeksi Awal',
                        'in_progress' => 'Proses Pengerjaan',
                        'parts_replaced' => 'Penggantian Sparepart',
                        'testing' => 'Pengujian',
                        'completed' => 'Selesai',
                        'other' => 'Lainnya',
                        default => $state,
                    })
                    ->color(fn(string $state): string => match ($state) {
                        'inspection' => 'info',
                        'in_progress' => 'warning',
                        'parts_replaced' => 'danger',
                        'testing' => 'gray',
                        'completed' => 'success',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('title')
                    ->label('Judul')
                    ->searchable()
                    ->limit(30),

                Tables\Columns\TextColumn::make('description')
                    ->label('Deskripsi')
                    ->limit(50)
                    ->tooltip(function ($record) {
                        return $record->description;
                    }),

                Tables\Columns\ImageColumn::make('image_path')
                    ->label('Foto')
                    ->circular()
                    ->defaultImageUrl(fn() => asset('images/no-image.jpg')),

                Tables\Columns\IconColumn::make('sent_to_customer')
                    ->label('Terkirim')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat Pada')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Tambah Update')
                    ->modalHeading('Tambah Update Servis')
                    ->successNotificationTitle('Update servis berhasil ditambahkan')
                    ->after(function ($record) {
                        // Jika opsi "Kirim ke Pelanggan" diaktifkan, kirim pesan WhatsApp
                        if ($record->sent_to_customer) {
                            // Get the service
                            $service = $record->service;

                            // Format the phone number
                            $phone = preg_replace('/[^0-9]/', '', $service->phone);

                            // If the number starts with 0, replace it with 62
                            if (substr($phone, 0, 1) === '0') {
                                $phone = '62' . substr($phone, 1);
                            }
                            // If the number doesn't start with 62, add it
                            elseif (substr($phone, 0, 2) !== '62') {
                                $phone = '62' . $phone;
                            }

                            // Create the message
                            $message = "Halo {$service->customer_name},\n\n";
                            $message .= "UPDATE SERVIS KENDARAAN ANDA DI HARTONO MOTOR\n\n";
                            $message .= "Mobil: {$service->car_model}\n";
                            if ($service->license_plate) {
                                $message .= "Plat Nomor: {$service->license_plate}\n";
                            }
                            $message .= "Jenis Servis: {$service->service_type}\n\n";
                            $message .= "UPDATE: {$record->title}\n";
                            $message .= "{$record->description}\n\n";

                            // Add image URL if exists
                            $imageUrl = '';
                            if ($record->image_path) {
                                $imageUrl = config('app.url') . Storage::url($record->image_path);
                                $message .= "Foto Update: {$imageUrl}\n\n";
                            }

                            $message .= "Terima kasih telah mempercayakan kendaraan Anda pada Hartono Motor.\n";
                            $message .= "Jika ada pertanyaan, silakan hubungi kami.\n\n";
                            $message .= "Salam,\nTim Hartono Motor";

                            // URL encode the message
                            $encodedMessage = urlencode($message);

                            // Update the record
                            $record->sent_at = now();
                            $record->save();

                            // Show notification
                            Notification::make()
                                ->title('Update berhasil dikirim ke pelanggan')
                                ->success()
                                ->send();

                            // Set WhatsApp URL to open after form submission
                            $this->whatsappUrl = "https://wa.me/{$phone}?text={$encodedMessage}";

                            // Use JavaScript to open WhatsApp in a new tab
                            echo "<script>window.open('{$this->whatsappUrl}', '_blank');</script>";
                        }
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->modalHeading('Edit Update Servis')
                    ->after(function ($record) {
                        // Jika opsi "Kirim ke Pelanggan" diaktifkan dan belum terkirim, kirim pesan WhatsApp
                        if ($record->sent_to_customer && !$record->sent_at) {
                            // Get the service
                            $service = $record->service;

                            // Format the phone number
                            $phone = preg_replace('/[^0-9]/', '', $service->phone);

                            // If the number starts with 0, replace it with 62
                            if (substr($phone, 0, 1) === '0') {
                                $phone = '62' . substr($phone, 1);
                            }
                            // If the number doesn't start with 62, add it
                            elseif (substr($phone, 0, 2) !== '62') {
                                $phone = '62' . $phone;
                            }

                            // Create the message
                            $message = "Halo {$service->customer_name},\n\n";
                            $message .= "UPDATE SERVIS KENDARAAN ANDA DI HARTONO MOTOR\n\n";
                            $message .= "Mobil: {$service->car_model}\n";
                            if ($service->license_plate) {
                                $message .= "Plat Nomor: {$service->license_plate}\n";
                            }
                            $message .= "Jenis Servis: {$service->service_type}\n\n";
                            $message .= "UPDATE: {$record->title}\n";
                            $message .= "{$record->description}\n\n";

                            // Add image URL if exists
                            $imageUrl = '';
                            if ($record->image_path) {
                                $imageUrl = config('app.url') . Storage::url($record->image_path);
                                $message .= "Foto Update: {$imageUrl}\n\n";
                            }

                            $message .= "Terima kasih telah mempercayakan kendaraan Anda pada Hartono Motor.\n";
                            $message .= "Jika ada pertanyaan, silakan hubungi kami.\n\n";
                            $message .= "Salam,\nTim Hartono Motor";

                            // URL encode the message
                            $encodedMessage = urlencode($message);

                            // Update the record
                            $record->sent_at = now();
                            $record->save();

                            // Show notification
                            Notification::make()
                                ->title('Update berhasil dikirim ke pelanggan')
                                ->success()
                                ->send();

                            // Set WhatsApp URL to open after form submission
                            $this->whatsappUrl = "https://wa.me/{$phone}?text={$encodedMessage}";

                            // Use JavaScript to open WhatsApp in a new tab
                            echo "<script>window.open('{$this->whatsappUrl}', '_blank');</script>";
                        }
                    }),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\Action::make('sendWhatsApp')
                    ->label('Kirim WhatsApp')
                    ->icon('heroicon-o-chat-bubble-left-right')
                    ->color('success')
                    ->visible(fn($record) => !$record->sent_to_customer)
                    ->action(function ($record) {
                        // Get the service
                        $service = $record->service;

                        // Format the phone number
                        $phone = preg_replace('/[^0-9]/', '', $service->phone);

                        // If the number starts with 0, replace it with 62
                        if (substr($phone, 0, 1) === '0') {
                            $phone = '62' . substr($phone, 1);
                        }
                        // If the number doesn't start with 62, add it
                        elseif (substr($phone, 0, 2) !== '62') {
                            $phone = '62' . $phone;
                        }

                        // Create the message
                        $message = "Halo {$service->customer_name},\n\n";
                        $message .= "UPDATE SERVIS KENDARAAN ANDA DI HARTONO MOTOR\n\n";
                        $message .= "Mobil: {$service->car_model}\n";
                        if ($service->license_plate) {
                            $message .= "Plat Nomor: {$service->license_plate}\n";
                        }
                        $message .= "Jenis Servis: {$service->service_type}\n\n";
                        $message .= "UPDATE: {$record->title}\n";
                        $message .= "{$record->description}\n\n";

                        // Add image URL if exists
                        $imageUrl = '';
                        if ($record->image_path) {
                            $imageUrl = config('app.url') . Storage::url($record->image_path);
                            $message .= "Foto Update: {$imageUrl}\n\n";
                        }

                        $message .= "Terima kasih telah mempercayakan kendaraan Anda pada Hartono Motor.\n";
                        $message .= "Jika ada pertanyaan, silakan hubungi kami.\n\n";
                        $message .= "Salam,\nTim Hartono Motor";

                        // URL encode the message
                        $encodedMessage = urlencode($message);

                        // Update the record
                        $record->sent_to_customer = true;
                        $record->sent_at = now();
                        $record->save();

                        // Show notification
                        Notification::make()
                            ->title('Update berhasil dikirim ke pelanggan')
                            ->success()
                            ->send();

                        // Open WhatsApp
                        $whatsappUrl = "https://wa.me/{$phone}?text={$encodedMessage}";
                        // Use JavaScript to open WhatsApp in a new tab
                        echo "<script>window.open('{$whatsappUrl}', '_blank');</script>";
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
