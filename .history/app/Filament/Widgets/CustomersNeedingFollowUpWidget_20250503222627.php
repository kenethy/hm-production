<?php

namespace App\Filament\Widgets;

use App\Models\Customer;
use Carbon\Carbon;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

class CustomersNeedingFollowUpWidget extends BaseWidget
{
    protected static ?string $heading = 'Pelanggan yang Perlu Follow-up';

    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Customer::query()
                    ->where('is_active', true)
                    ->where('service_count', '>', 0)
                    ->where(function (Builder $query) {
                        $query->whereNull('last_service_date')
                            ->orWhere('last_service_date', '<=', now()->subMonths(3));
                    })
                    ->orderBy('last_service_date')
            )
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('phone')
                    ->label('Nomor Telepon')
                    ->searchable()
                    ->copyable()
                    ->copyMessage('Nomor telepon disalin!'),

                Tables\Columns\TextColumn::make('last_service_date')
                    ->label('Servis Terakhir')
                    ->date('d M Y')
                    ->sortable()
                    ->formatStateUsing(function ($state) {
                        if (!$state) {
                            return 'Belum pernah servis';
                        }

                        $date = Carbon::parse($state);
                        $diffInDays = $date->diffInDays(now());

                        if ($diffInDays > 180) {
                            return $date->format('d M Y') . ' (' . $date->diffForHumans() . ')';
                        }

                        return $date->format('d M Y');
                    }),

                Tables\Columns\TextColumn::make('service_count')
                    ->label('Jumlah Servis')
                    ->sortable(),

                Tables\Columns\TextColumn::make('total_spent')
                    ->label('Total Pengeluaran')
                    ->money('IDR')
                    ->sortable(),
            ])
            ->actions([
                Tables\Actions\Action::make('sendWhatsApp')
                    ->label('WhatsApp')
                    ->icon('heroicon-o-chat-bubble-left-right')
                    ->color('success')
                    ->url(function (Customer $record) {
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

                        $message = "Halo {$record->name},\n\n";

                        if ($record->last_service_date) {
                            $lastServiceDate = Carbon::parse($record->last_service_date)->format('d F Y');
                            $message .= "Kami melihat bahwa Anda terakhir melakukan servis mobil pada tanggal {$lastServiceDate}. ";
                            $message .= "Untuk menjaga performa dan keamanan kendaraan Anda, kami menyarankan untuk melakukan servis berkala.\n\n";
                        } else {
                            $message .= "Kami ingin mengingatkan bahwa servis berkala sangat penting untuk menjaga performa dan keamanan kendaraan Anda.\n\n";
                        }

                        $message .= "Apakah Anda ingin membuat janji untuk servis berikutnya? Kami memiliki promo spesial untuk pelanggan setia seperti Anda.\n\n";
                        $message .= "Terima kasih,\nTim Hartono Motor";

                        // URL encode the message
                        $encodedMessage = urlencode($message);

                        return "https://wa.me/{$phone}?text={$encodedMessage}";
                    })
                    ->openUrlInNewTab(),

                Tables\Actions\Action::make('viewDetails')
                    ->label('Lihat Detail')
                    ->icon('heroicon-o-eye')
                    ->url(fn(Customer $record) => route('filament.admin.resources.customers.edit', $record))
                    ->openUrlInNewTab(),
            ])
            ->defaultPaginationPageOption(5);
    }
}
