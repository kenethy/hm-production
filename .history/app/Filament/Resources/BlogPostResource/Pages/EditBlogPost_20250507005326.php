<?php

namespace App\Filament\Resources\BlogPostResource\Pages;

use App\Filament\Resources\BlogPostResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;

class EditBlogPost extends EditRecord
{
    protected static string $resource = BlogPostResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('editor_guide')
                ->label('Panduan Editor')
                ->color('gray')
                ->icon('heroicon-o-information-circle')
                ->action(function () {
                    Notification::make()
                        ->title('Panduan Penggunaan Editor')
                        ->body('
                            <ul class="list-disc pl-4 space-y-2">
                                <li><strong>Menambahkan Gambar:</strong> Klik tombol "Lampirkan File" pada toolbar editor untuk mengunggah gambar.</li>
                                <li><strong>Mengatur Ukuran Gambar:</strong> Klik gambar yang sudah diunggah, lalu gunakan handle di sudut gambar untuk mengubah ukuran.</li>
                                <li><strong>Mengatur Posisi Gambar:</strong> Klik gambar, lalu gunakan tombol align (rata kiri, tengah, kanan) pada toolbar yang muncul.</li>
                                <li><strong>Menambahkan Teks di Samping Gambar:</strong> Letakkan kursor di samping gambar, lalu ketik teks Anda.</li>
                                <li><strong>Membuat Heading:</strong> Gunakan tombol H2 atau H3 pada toolbar untuk membuat judul atau subjudul.</li>
                                <li><strong>Membuat Link:</strong> Pilih teks, lalu klik tombol link pada toolbar.</li>
                            </ul>
                        ')
                        ->persistent()
                        ->actions([
                            Actions\Action::make('dismiss')
                                ->label('Tutup')
                                ->color('gray'),
                        ])
                        ->send();
                }),
            Actions\DeleteAction::make(),
        ];
    }

    protected function afterSave(): void
    {
        Notification::make()
            ->title('Artikel berhasil diperbarui')
            ->success()
            ->send();
    }
}
