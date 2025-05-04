<?php

namespace App\Filament\Resources\ServiceResource\Pages;

use App\Filament\Resources\ServiceResource;
use Filament\Actions;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditService extends EditRecord
{
    protected static string $resource = ServiceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('sendFollowUpWhatsApp')
                ->label('Kirim Follow-up WhatsApp')
                ->icon('heroicon-o-chat-bubble-left-ellipsis')
                ->color('success')
                ->visible(fn() => $this->record->status === 'completed')
                ->form([
                    Forms\Components\Select::make('template')
                        ->label('Template Pesan')
                        ->options([
                            'follow_up' => 'Follow-up Standar',
                            'feedback' => 'Minta Feedback',
                            'promo' => 'Tawarkan Promo Berikutnya',
                        ])
                        ->default('follow_up')
                        ->required(),
                    Forms\Components\Textarea::make('custom_message')
                        ->label('Pesan Tambahan (Opsional)')
                        ->placeholder('Tambahkan pesan khusus di sini (opsional)')
                        ->rows(3),
                ])
                ->action(function (array $data): void {
                    // Format nomor telepon untuk WhatsApp
                    $phone = preg_replace('/[^0-9]/', '', $this->record->phone);
                    if (substr($phone, 0, 1) === '0') {
                        $phone = '62' . substr($phone, 1);
                    } elseif (substr($phone, 0, 2) !== '62') {
                        $phone = '62' . $phone;
                    }

                    // Buat pesan berdasarkan template yang dipilih
                    $message = match ($data['template']) {
                        'follow_up' => "Halo {$this->record->customer_name},\n\n" .
                            "Terima kasih telah mempercayakan kendaraan Anda kepada Hartono Motor. " .
                            "Servis {$this->record->service_type} untuk mobil {$this->record->car_model} Anda telah selesai.\n\n" .
                            "Bagaimana kondisi kendaraan Anda setelah servis? Apakah ada masalah atau pertanyaan yang ingin Anda sampaikan?\n\n" .
                            "Kami sangat menghargai umpan balik Anda untuk meningkatkan layanan kami.\n\n" .
                            "Terima kasih,\nTim Hartono Motor",

                        'feedback' => "Halo {$this->record->customer_name},\n\n" .
                            "Terima kasih telah mempercayakan servis {$this->record->service_type} untuk mobil {$this->record->car_model} Anda kepada Hartono Motor.\n\n" .
                            "Kami ingin mengetahui pendapat Anda tentang layanan kami. Mohon berikan penilaian Anda dengan membalas pesan ini dengan angka 1-5 (1: Sangat Tidak Puas, 5: Sangat Puas).\n\n" .
                            "Kami juga sangat menghargai saran dan masukan Anda untuk meningkatkan kualitas layanan kami.\n\n" .
                            "Terima kasih,\nTim Hartono Motor",

                        'promo' => "Halo {$this->record->customer_name},\n\n" .
                            "Terima kasih telah mempercayakan servis {$this->record->service_type} untuk mobil {$this->record->car_model} Anda kepada Hartono Motor.\n\n" .
                            "Sebagai pelanggan setia kami, Anda berhak mendapatkan DISKON 10% untuk servis berikutnya dalam 3 bulan ke depan.\n\n" .
                            "Gunakan kode promo: HARTONO10\n\n" .
                            "Jangan lewatkan kesempatan ini untuk merawat kendaraan Anda dengan harga spesial!\n\n" .
                            "Terima kasih,\nTim Hartono Motor",

                        default => "Halo {$this->record->customer_name},\n\n" .
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

                    // Redirect ke URL WhatsApp
                    redirect()->away($whatsappUrl);
                }),
            Actions\DeleteAction::make(),
        ];
    }
}
