<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BookingResource\Pages;
use App\Models\Booking;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

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
                        'Balancing' => 'Balancing & Spooring',
                        'Cuci' => 'Cuci',
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
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama')
                    ->searchable(),
                Tables\Columns\TextColumn::make('phone')
                    ->label('Nomor Telepon')
                    ->searchable(),
                Tables\Columns\TextColumn::make('car_model')
                    ->label('Model Mobil')
                    ->searchable(),
                Tables\Columns\TextColumn::make('service_type')
                    ->label('Jenis Servis')
                    ->searchable(),
                Tables\Columns\TextColumn::make('date')
                    ->label('Tanggal Servis')
                    ->date('d F Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('time')
                    ->label('Waktu Servis'),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'pending' => 'warning',
                        'confirmed' => 'info',
                        'completed' => 'success',
                        'cancelled' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'pending' => 'Menunggu Konfirmasi',
                        'confirmed' => 'Terkonfirmasi',
                        'completed' => 'Selesai',
                        'cancelled' => 'Dibatalkan',
                        default => $state,
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat Pada')
                    ->dateTime('d F Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Diperbarui Pada')
                    ->dateTime('d F Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'pending' => 'Menunggu Konfirmasi',
                        'confirmed' => 'Terkonfirmasi',
                        'completed' => 'Selesai',
                        'cancelled' => 'Dibatalkan',
                    ]),
                Tables\Filters\Filter::make('date')
                    ->form([
                        Forms\Components\DatePicker::make('date_from')
                            ->label('Dari Tanggal'),
                        Forms\Components\DatePicker::make('date_to')
                            ->label('Sampai Tanggal'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['date_from'],
                                fn(Builder $query, $date): Builder => $query->whereDate('date', '>=', $date),
                            )
                            ->when(
                                $data['date_to'],
                                fn(Builder $query, $date): Builder => $query->whereDate('date', '<=', $date),
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\Action::make('sendWhatsApp')
                    ->label('Kirim WhatsApp')
                    ->icon('heroicon-o-chat-bubble-left-right')
                    ->color('success')
                    ->url(function ($record) {
                        // Format the phone number (remove any non-numeric characters and ensure it starts with 62)
                        $phone = preg_replace('/[^0-9]/', '', $record->phone);

                        // If the number starts with 0, replace it with 62
                        if (substr($phone, 0, 1) === '0') {
                            $phone = '62' . substr($phone, 1);
                        }
                        // If the number doesn't start with 62, add it
                        elseif (substr($phone, 0, 2) !== '62') {
                            $phone = '62' . $phone;
                        }

                        // Create the message based on booking status
                        $message = match ($record->status) {
                            'pending' => "Halo {$record->name},\n\nTerima kasih telah melakukan booking servis di Hartono Motor.\n\nDetail booking Anda:\n- Jenis Servis: {$record->service_type}\n- Tanggal: " . date('d F Y', strtotime($record->date)) . "\n- Waktu: {$record->time}\n- Mobil: {$record->car_model}\n\nMohon konfirmasi apakah Anda akan datang sesuai jadwal tersebut?\n\nTerima kasih,\nTim Hartono Motor",

                            'confirmed' => "Halo {$record->name},\n\nKami mengkonfirmasi booking servis Anda di Hartono Motor telah DIKONFIRMASI.\n\nDetail booking:\n- Jenis Servis: {$record->service_type}\n- Tanggal: " . date('d F Y', strtotime($record->date)) . "\n- Waktu: {$record->time}\n- Mobil: {$record->car_model}\n\nMohon datang tepat waktu. Jika ada perubahan, harap beritahu kami sebelumnya.\n\nTerima kasih,\nTim Hartono Motor",

                            'completed' => "Halo {$record->name},\n\nTerima kasih telah menggunakan jasa servis Hartono Motor. Servis mobil Anda telah SELESAI.\n\nKami berharap Anda puas dengan layanan kami. Jika ada pertanyaan atau masukan, jangan ragu untuk menghubungi kami.\n\nTerima kasih atas kepercayaan Anda!\n\nSalam,\nTim Hartono Motor",

                            'cancelled' => "Halo {$record->name},\n\nKami ingin menginformasikan bahwa booking servis Anda di Hartono Motor telah DIBATALKAN.\n\nDetail booking:\n- Jenis Servis: {$record->service_type}\n- Tanggal: " . date('d F Y', strtotime($record->date)) . "\n- Waktu: {$record->time}\n- Mobil: {$record->car_model}\n\nJika Anda ingin menjadwalkan ulang, silakan hubungi kami atau buat booking baru melalui website kami.\n\nTerima kasih,\nTim Hartono Motor",

                            default => "Halo {$record->name},\n\nTerima kasih telah melakukan booking servis di Hartono Motor.\n\nDetail booking Anda:\n- Jenis Servis: {$record->service_type}\n- Tanggal: " . date('d F Y', strtotime($record->date)) . "\n- Waktu: {$record->time}\n- Mobil: {$record->car_model}\n\nJika ada pertanyaan, silakan hubungi kami.\n\nTerima kasih,\nTim Hartono Motor",
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
                    Tables\Actions\BulkAction::make('sendWhatsAppBulk')
                        ->label('Kirim WhatsApp (Massal)')
                        ->icon('heroicon-o-chat-bubble-left-right')
                        ->color('success')
                        ->action(function ($records) {
                            // Get the first record to determine the status
                            $firstRecord = $records->first();
                            $status = $firstRecord->status;

                            // Create a generic message based on status
                            $message = match ($status) {
                                'pending' => "Kepada pelanggan Hartono Motor yang terhormat,\n\nKami ingin mengingatkan tentang booking servis Anda di Hartono Motor.\n\nMohon konfirmasi kehadiran Anda sesuai jadwal yang telah ditentukan.\n\nTerima kasih,\nTim Hartono Motor",

                                'confirmed' => "Kepada pelanggan Hartono Motor yang terhormat,\n\nKami mengkonfirmasi bahwa booking servis Anda di Hartono Motor telah DIKONFIRMASI.\n\nMohon datang tepat waktu sesuai jadwal yang telah ditentukan. Jika ada perubahan, harap beritahu kami sebelumnya.\n\nTerima kasih,\nTim Hartono Motor",

                                'completed' => "Kepada pelanggan Hartono Motor yang terhormat,\n\nTerima kasih telah menggunakan jasa servis Hartono Motor. Servis mobil Anda telah SELESAI.\n\nKami berharap Anda puas dengan layanan kami. Jika ada pertanyaan atau masukan, jangan ragu untuk menghubungi kami.\n\nTerima kasih atas kepercayaan Anda!\n\nSalam,\nTim Hartono Motor",

                                'cancelled' => "Kepada pelanggan Hartono Motor yang terhormat,\n\nKami ingin menginformasikan bahwa booking servis Anda di Hartono Motor telah DIBATALKAN.\n\nJika Anda ingin menjadwalkan ulang, silakan hubungi kami atau buat booking baru melalui website kami.\n\nTerima kasih,\nTim Hartono Motor",

                                default => "Kepada pelanggan Hartono Motor yang terhormat,\n\nTerima kasih telah melakukan booking servis di Hartono Motor.\n\nJika ada pertanyaan, silakan hubungi kami.\n\nTerima kasih,\nTim Hartono Motor",
                            };

                            // Encode the message
                            $encodedMessage = urlencode($message);

                            // Create an array of WhatsApp URLs
                            $urls = [];
                            foreach ($records as $record) {
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

                                $urls[] = "https://wa.me/{$phone}?text={$encodedMessage}";
                            }

                            // Open the first URL in a new tab
                            if (!empty($urls)) {
                                // Return a JavaScript snippet that will open each URL in a new tab
                                $jsCode = "
                                    <script>
                                        const urls = " . json_encode($urls) . ";
                                        urls.forEach(url => {
                                            window.open(url, '_blank');
                                        });
                                    </script>
                                ";

                                // Display a notification
                                Notification::make()
                                    ->title('WhatsApp')
                                    ->body('WhatsApp messages are being opened in new tabs.')
                                    ->success()
                                    ->send();

                                // Return the JavaScript code
                                return $jsCode;
                            }
                        })
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
