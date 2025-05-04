<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ServiceResource\Pages;
use App\Filament\Resources\ServiceResource\RelationManagers;
use App\Models\Booking;
use App\Models\Customer;
use App\Models\Service;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ServiceResource extends Resource
{
    protected static ?string $model = Service::class;

    protected static ?string $navigationIcon = 'heroicon-o-wrench';

    protected static ?string $navigationLabel = 'Services';

    protected static ?string $modelLabel = 'Servis Kendaraan';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Pelanggan')
                    ->schema([
                        Forms\Components\Select::make('booking_id')
                            ->label('Booking Terkait')
                            ->options(function () {
                                return Booking::where('status', '!=', 'cancelled')
                                    ->whereNotNull('name')
                                    ->where('name', '!=', '')
                                    ->pluck('name', 'id')
                                    ->toArray();
                            })
                            ->searchable()
                            ->preload()
                            ->afterStateUpdated(function ($state, Forms\Set $set) {
                                if ($state) {
                                    $booking = Booking::find($state);
                                    if ($booking) {
                                        $set('customer_name', $booking->name);
                                        $set('phone', $booking->phone);
                                        $set('car_model', $booking->car_model);
                                        $set('service_type', $booking->service_type);

                                        // Check if customer exists with this phone number
                                        $customer = Customer::where('phone', $booking->phone)->first();
                                        if ($customer) {
                                            $set('customer_id', $customer->id);
                                        }
                                    }
                                }
                            })
                            ->nullable(),

                        Forms\Components\Select::make('customer_id')
                            ->label('Pelanggan')
                            ->options(function () {
                                return Customer::whereNotNull('name')
                                    ->where('name', '!=', '')
                                    ->pluck('name', 'id')
                                    ->toArray();
                            })
                            ->searchable()
                            ->preload()
                            ->afterStateUpdated(function ($state, Forms\Set $set) {
                                if ($state) {
                                    $customer = Customer::find($state);
                                    if ($customer) {
                                        $set('customer_name', $customer->name);
                                        $set('phone', $customer->phone);
                                    }
                                }
                            })
                            ->createOptionForm([
                                Forms\Components\TextInput::make('name')
                                    ->label('Nama')
                                    ->required()
                                    ->maxLength(255),

                                Forms\Components\TextInput::make('phone')
                                    ->label('Nomor Telepon')
                                    ->required()
                                    ->tel()
                                    ->unique()
                                    ->maxLength(255),

                                Forms\Components\TextInput::make('email')
                                    ->label('Email')
                                    ->email()
                                    ->unique()
                                    ->maxLength(255),

                                Forms\Components\Select::make('gender')
                                    ->label('Jenis Kelamin')
                                    ->options([
                                        'male' => 'Laki-laki',
                                        'female' => 'Perempuan',
                                        'other' => 'Lainnya',
                                    ])
                                    ->default('male'),

                                Forms\Components\TextInput::make('city')
                                    ->label('Kota')
                                    ->maxLength(255),
                            ])
                            ->nullable(),

                        Forms\Components\TextInput::make('customer_name')
                            ->label('Nama Pelanggan')
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

                        Forms\Components\TextInput::make('license_plate')
                            ->label('Nomor Plat')
                            ->maxLength(255),
                    ])->columns(2),

                Forms\Components\Section::make('Detail Servis')
                    ->schema([
                        Forms\Components\Select::make('service_type')
                            ->label('Jenis Servis')
                            ->options([
                                'Servis Berkala' => 'Servis Berkala',
                                'Tune Up Mesin' => 'Tune Up Mesin',
                                'Servis AC' => 'Servis AC',
                                'Ganti Oli' => 'Ganti Oli',
                                'Perbaikan Rem' => 'Perbaikan Rem',
                                'Balancing' => 'Balancing',
                                'Cuci' => 'Cuci',
                                'Lainnya' => 'Lainnya',
                            ])
                            ->default('Servis Berkala')
                            ->required(),

                        Forms\Components\Textarea::make('description')
                            ->label('Deskripsi Servis')
                            ->placeholder('Jelaskan detail servis yang dilakukan')
                            ->rows(3),

                        Forms\Components\Textarea::make('parts_used')
                            ->label('Sparepart yang Digunakan')
                            ->placeholder('Daftar sparepart yang digunakan')
                            ->rows(3),
                    ]),

                Forms\Components\Section::make('Biaya')
                    ->schema([
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

                        Forms\Components\TextInput::make('total_cost')
                            ->label('Total Biaya')
                            ->numeric()
                            ->prefix('Rp')
                            ->default(0)
                            ->disabled()
                            ->dehydrated(),
                    ])->columns(3),

                Forms\Components\Section::make('Status')
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->label('Status Servis')
                            ->options([
                                'in_progress' => 'Dalam Pengerjaan',
                                'completed' => 'Selesai',
                                'cancelled' => 'Dibatalkan',
                            ])
                            ->default('in_progress')
                            ->selectablePlaceholder(false)
                            ->required(),

                        Forms\Components\Textarea::make('notes')
                            ->label('Catatan')
                            ->placeholder('Catatan tambahan tentang servis ini')
                            ->rows(3),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('customer_name')
                    ->label('Nama Pelanggan')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('phone')
                    ->label('Nomor Telepon')
                    ->searchable(),

                Tables\Columns\TextColumn::make('car_model')
                    ->label('Model Mobil')
                    ->searchable(),

                Tables\Columns\TextColumn::make('license_plate')
                    ->label('Nomor Plat')
                    ->searchable(),

                Tables\Columns\TextColumn::make('service_type')
                    ->label('Jenis Servis')
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
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tanggal Dibuat')
                    ->dateTime('d F Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Terakhir Diupdate')
                    ->dateTime('d F Y H:i')
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
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('markAsCompleted')
                    ->label('Selesai')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn(Service $record) => $record->status === 'in_progress')
                    ->action(function (Service $record) {
                        $record->status = 'completed';
                        $record->completed_at = now();
                        $record->save();

                        Notification::make()
                            ->title('Servis telah ditandai sebagai selesai')
                            ->success()
                            ->send();
                    }),
                Tables\Actions\Action::make('sendFollowUpWhatsApp')
                    ->label('Kirim Follow-up')
                    ->icon('heroicon-o-chat-bubble-left-ellipsis')
                    ->color('success')
                    ->visible(fn(Service $record) => $record->status === 'completed')
                    ->form([
                        Forms\Components\Select::make('template')
                            ->label('Template Pesan')
                            ->options([
                                'follow_up' => 'Follow-up Standar',
                                'feedback' => 'Minta Feedback',
                                'promo' => 'Tawarkan Promo Berikutnya',
                            ])
                            ->default('follow_up')
                            ->selectablePlaceholder(false)
                            ->required(),
                        Forms\Components\Textarea::make('custom_message')
                            ->label('Pesan Tambahan (Opsional)')
                            ->placeholder('Tambahkan pesan khusus di sini (opsional)')
                            ->rows(3),
                    ])
                    ->action(function (array $data, Service $record): void {
                        // Format nomor telepon untuk WhatsApp
                        $phone = preg_replace('/[^0-9]/', '', $record->phone);
                        if (substr($phone, 0, 1) === '0') {
                            $phone = '62' . substr($phone, 1);
                        } elseif (substr($phone, 0, 2) !== '62') {
                            $phone = '62' . $phone;
                        }

                        // Buat pesan berdasarkan template yang dipilih
                        $message = match ($data['template']) {
                            'follow_up' => "Halo {$record->customer_name},\n\n" .
                                "Terima kasih telah mempercayakan kendaraan Anda kepada Hartono Motor. " .
                                "Servis {$record->service_type} untuk mobil {$record->car_model} Anda telah selesai.\n\n" .
                                "Bagaimana kondisi kendaraan Anda setelah servis? Apakah ada masalah atau pertanyaan yang ingin Anda sampaikan?\n\n" .
                                "Kami sangat menghargai umpan balik Anda untuk meningkatkan layanan kami.\n\n" .
                                "Terima kasih,\nTim Hartono Motor",

                            'feedback' => "Halo {$record->customer_name},\n\n" .
                                "Terima kasih telah mempercayakan servis {$record->service_type} untuk mobil {$record->car_model} Anda kepada Hartono Motor.\n\n" .
                                "Kami ingin mengetahui pendapat Anda tentang layanan kami. Mohon berikan penilaian Anda dengan membalas pesan ini dengan angka 1-5 (1: Sangat Tidak Puas, 5: Sangat Puas).\n\n" .
                                "Kami juga sangat menghargai saran dan masukan Anda untuk meningkatkan kualitas layanan kami.\n\n" .
                                "Terima kasih,\nTim Hartono Motor",

                            'promo' => "Halo {$record->customer_name},\n\n" .
                                "Terima kasih telah mempercayakan servis {$record->service_type} untuk mobil {$record->car_model} Anda kepada Hartono Motor.\n\n" .
                                "Sebagai pelanggan setia kami, Anda berhak mendapatkan DISKON 10% untuk servis berikutnya dalam 3 bulan ke depan.\n\n" .
                                "Gunakan kode promo: HARTONO10\n\n" .
                                "Jangan lewatkan kesempatan ini untuk merawat kendaraan Anda dengan harga spesial!\n\n" .
                                "Terima kasih,\nTim Hartono Motor",

                            default => "Halo {$record->customer_name},\n\n" .
                                "Terima kasih telah mempercayakan kendaraan Anda kepada Hartono Motor.\n\n" .
                                "Terima kasih,\nTim Hartono Motor",
                        };

                        // Tambahkan pesan kustom jika ada
                        if (!empty($data['custom_message'])) {
                            $message .= "\n\n" . $data['custom_message'];
                        }

                        // Encode pesan untuk URL
                        $encodedMessage = urlencode($message);

                        // Buat URL WhatsApp
                        $whatsappUrl = "https://wa.me/{$phone}?text={$encodedMessage}";

                        // Tampilkan notifikasi sukses
                        Notification::make()
                            ->title('Pesan follow-up siap dikirim')
                            ->body('WhatsApp akan terbuka dengan pesan yang sudah disiapkan.')
                            ->success()
                            ->send();

                        // Redirect ke URL WhatsApp
                        redirect()->away($whatsappUrl);
                    }),
                Tables\Actions\Action::make('markAsCancelled')
                    ->label('Batalkan')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn(Service $record) => $record->status === 'in_progress')
                    ->requiresConfirmation()
                    ->action(function (Service $record) {
                        $record->status = 'cancelled';
                        $record->save();

                        Notification::make()
                            ->title('Servis telah dibatalkan')
                            ->success()
                            ->send();
                    }),
                Tables\Actions\Action::make('sendWhatsApp')
                    ->label('WhatsApp')
                    ->icon('heroicon-o-chat-bubble-left-right')
                    ->color('success')
                    ->url(function (Service $record) {
                        // Format the phone number
                        $phone = preg_replace('/[^0-9]/', '', $record->phone);

                        // If the number starts with 0, replace it with 62
                        if (substr($phone, 0, 1) === '0') {
                            $phone = '62' . substr($phone, 1);
                        }
                        // If the number doesn't start with 62, add it
                        elseif (substr($phone, 0, 2) !== '62') {
                            $phone = '62' . $phone;
                        }

                        // Create the message based on service status
                        $message = match ($record->status) {
                            'in_progress' => "Halo {$record->customer_name},\n\nMobil Anda ({$record->car_model}) sedang dalam proses servis di Hartono Motor.\n\nJenis Servis: {$record->service_type}\n\nKami akan menghubungi Anda kembali ketika servis telah selesai.\n\nTerima kasih,\nTim Hartono Motor",

                            'completed' => "Halo {$record->customer_name},\n\nServis mobil Anda ({$record->car_model}) di Hartono Motor telah SELESAI.\n\nJenis Servis: {$record->service_type}\n\nTotal Biaya: Rp " . number_format($record->total_cost, 0, ',', '.') . "\n\nMobil Anda sudah siap untuk diambil. Terima kasih telah mempercayakan kendaraan Anda pada kami.\n\nSalam,\nTim Hartono Motor",

                            'cancelled' => "Halo {$record->customer_name},\n\nKami ingin menginformasikan bahwa servis mobil Anda ({$record->car_model}) di Hartono Motor telah DIBATALKAN.\n\nJika Anda memiliki pertanyaan, silakan hubungi kami.\n\nTerima kasih,\nTim Hartono Motor",

                            default => "Halo {$record->customer_name},\n\nTerima kasih telah mempercayakan servis mobil Anda ({$record->car_model}) di Hartono Motor.\n\nJika ada pertanyaan, silakan hubungi kami.\n\nTerima kasih,\nTim Hartono Motor",
                        };

                        // URL encode the message
                        $encodedMessage = urlencode($message);

                        // Return the WhatsApp URL
                        return "https://wa.me/{$phone}?text={$encodedMessage}";
                    })
                    ->openUrlInNewTab(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make('markAsCompletedBulk')
                        ->label('Tandai Selesai')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->action(function (\Illuminate\Database\Eloquent\Collection $records) {
                            $records->each(function ($record) {
                                if ($record->status === 'in_progress') {
                                    $record->status = 'completed';
                                    $record->completed_at = now();
                                    $record->save();
                                }
                            });

                            Notification::make()
                                ->title('Servis telah ditandai sebagai selesai')
                                ->success()
                                ->send();
                        })
                        ->deselectRecordsAfterCompletion(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\UpdatesRelationManager::class,
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->latest();
    }

    // Add a hook to calculate the total cost before saving
    public static function beforeSave(Forms\Form $form): void
    {
        $form->getState();

        $form->model->total_cost = $form->model->labor_cost + $form->model->parts_cost;

        if ($form->model->status === 'completed' && !$form->model->completed_at) {
            $form->model->completed_at = now();
        }

        // Check if customer_id is not set but we have customer_name and phone
        if (!$form->model->customer_id && $form->model->customer_name && $form->model->phone) {
            // Check if customer exists with this phone number
            $customer = Customer::where('phone', $form->model->phone)->first();

            if ($customer) {
                // If customer exists, associate service with this customer
                $form->model->customer_id = $customer->id;
            } else {
                // If customer doesn't exist, create a new one
                $customer = Customer::create([
                    'name' => $form->model->customer_name,
                    'phone' => $form->model->phone,
                    'is_active' => true,
                ]);

                // Associate service with the new customer
                $form->model->customer_id = $customer->id;
            }
        }
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListServices::route('/'),
            'create' => Pages\CreateService::route('/create'),
            'edit' => Pages\EditService::route('/{record}/edit'),
        ];
    }
}
