<?php

namespace App\Filament\Resources\MechanicReportResource\Pages;

use App\Filament\Resources\MechanicReportResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Artisan;

class ListMechanicReports extends ListRecords
{
    protected static string $resource = MechanicReportResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            Actions\Action::make('refreshReports')
                ->label('Refresh Rekap Montir')
                ->icon('heroicon-o-arrow-path')
                ->color('success')
                ->tooltip('Memperbarui semua rekap montir berdasarkan data servis terbaru')
                ->action(function () {
                    // Jalankan command untuk memperbarui rekap montir
                    $output = '';
                    try {
                        Artisan::call('mechanic:sync-reports', [
                            '--force' => true,
                        ], $output);

                        Notification::make()
                            ->title('Rekap montir berhasil diperbarui')
                            ->success()
                            ->body('Semua rekap montir telah diperbarui berdasarkan data servis terbaru.')
                            ->send();
                    } catch (\Exception $e) {
                        Notification::make()
                            ->title('Gagal memperbarui rekap montir')
                            ->danger()
                            ->body('Terjadi kesalahan saat memperbarui rekap montir: ' . $e->getMessage())
                            ->send();
                    }
                }),
        ];
    }
}
