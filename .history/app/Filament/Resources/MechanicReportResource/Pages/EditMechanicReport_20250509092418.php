<?php

namespace App\Filament\Resources\MechanicReportResource\Pages;

use App\Filament\Resources\MechanicReportResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Artisan;

class EditMechanicReport extends EditRecord
{
    protected static string $resource = MechanicReportResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('refreshReport')
                ->label('Refresh Rekap')
                ->icon('heroicon-o-arrow-path')
                ->color('success')
                ->tooltip('Memperbarui rekap montir ini berdasarkan data servis terbaru')
                ->action(function () {
                    // Jalankan command untuk memperbarui rekap montir ini
                    $output = '';
                    try {
                        Artisan::call('mechanic:sync-reports', [
                            '--mechanic_id' => $this->record->mechanic_id,
                        ], $output);

                        // Refresh halaman untuk menampilkan data terbaru
                        $this->refresh();

                        Notification::make()
                            ->title('Rekap montir berhasil diperbarui')
                            ->success()
                            ->body('Rekap montir telah diperbarui berdasarkan data servis terbaru.')
                            ->send();
                    } catch (\Exception $e) {
                        Notification::make()
                            ->title('Gagal memperbarui rekap montir')
                            ->danger()
                            ->body('Terjadi kesalahan saat memperbarui rekap montir: ' . $e->getMessage())
                            ->send();
                    }
                }),
            Actions\DeleteAction::make(),
        ];
    }
}
