<?php

namespace App\Filament\Resources\ServiceResource\Pages;

use App\Filament\Resources\ServiceResource;
use App\Models\Service;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Redirect;

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
                ->action(function () {
                    // Format nomor telepon untuk WhatsApp (hapus karakter non-digit dan pastikan format internasional)
                    $phone = preg_replace('/[^0-9]/', '', $this->record->phone);
                    if (substr($phone, 0, 1) === '0') {
                        $phone = '62' . substr($phone, 1);
                    } elseif (substr($phone, 0, 2) !== '62') {
                        $phone = '62' . $phone;
                    }

                    // Buat pesan follow-up
                    $message = "Halo {$this->record->customer_name},\n\n";
                    $message .= "Terima kasih telah mempercayakan kendaraan Anda kepada Hartono Motor. ";
                    $message .= "Servis {$this->record->service_type} untuk mobil {$this->record->car_model} Anda telah selesai.\n\n";
                    $message .= "Bagaimana kondisi kendaraan Anda setelah servis? Apakah ada masalah atau pertanyaan yang ingin Anda sampaikan?\n\n";
                    $message .= "Kami sangat menghargai umpan balik Anda untuk meningkatkan layanan kami.\n\n";
                    $message .= "Terima kasih,\nTim Hartono Motor";

                    // Encode pesan untuk URL
                    $encodedMessage = urlencode($message);

                    // Buat URL WhatsApp
                    $whatsappUrl = "https://wa.me/{$phone}?text={$encodedMessage}";

                    // Redirect ke URL WhatsApp
                    return redirect()->away($whatsappUrl);
                }),
            Actions\DeleteAction::make(),
        ];
    }
}
