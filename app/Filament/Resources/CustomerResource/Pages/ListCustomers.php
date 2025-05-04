<?php

namespace App\Filament\Resources\CustomerResource\Pages;

use App\Filament\Resources\CustomerResource;
use App\Models\Customer;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ListCustomers extends ListRecords
{
    protected static string $resource = CustomerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            Actions\Action::make('import')
                ->label('Import CSV')
                ->icon('heroicon-o-arrow-up-tray')
                ->form([
                    Forms\Components\FileUpload::make('csv_file')
                        ->label('File CSV')
                        ->acceptedFileTypes(['text/csv', 'application/vnd.ms-excel'])
                        ->required(),
                    Forms\Components\Toggle::make('has_header_row')
                        ->label('File memiliki baris header')
                        ->default(true),
                    Forms\Components\Select::make('name_column')
                        ->label('Kolom Nama')
                        ->options([
                            'A' => 'Kolom A',
                            'B' => 'Kolom B',
                            'C' => 'Kolom C',
                            'D' => 'Kolom D',
                            'E' => 'Kolom E',
                        ])
                        ->required(),
                    Forms\Components\Select::make('phone_column')
                        ->label('Kolom Nomor Telepon')
                        ->options([
                            'A' => 'Kolom A',
                            'B' => 'Kolom B',
                            'C' => 'Kolom C',
                            'D' => 'Kolom D',
                            'E' => 'Kolom E',
                        ])
                        ->required(),
                    Forms\Components\Select::make('email_column')
                        ->label('Kolom Email (opsional)')
                        ->options([
                            '' => 'Tidak Ada',
                            'A' => 'Kolom A',
                            'B' => 'Kolom B',
                            'C' => 'Kolom C',
                            'D' => 'Kolom D',
                            'E' => 'Kolom E',
                        ]),
                    Forms\Components\Select::make('address_column')
                        ->label('Kolom Alamat (opsional)')
                        ->options([
                            '' => 'Tidak Ada',
                            'A' => 'Kolom A',
                            'B' => 'Kolom B',
                            'C' => 'Kolom C',
                            'D' => 'Kolom D',
                            'E' => 'Kolom E',
                        ]),
                    Forms\Components\Select::make('city_column')
                        ->label('Kolom Kota (opsional)')
                        ->options([
                            '' => 'Tidak Ada',
                            'A' => 'Kolom A',
                            'B' => 'Kolom B',
                            'C' => 'Kolom C',
                            'D' => 'Kolom D',
                            'E' => 'Kolom E',
                        ]),
                ])
                ->action(function (array $data): void {
                    $file = Storage::disk('public')->path($data['csv_file']);
                    $hasHeader = $data['has_header_row'];

                    $nameColumn = $this->getColumnIndex($data['name_column']);
                    $phoneColumn = $this->getColumnIndex($data['phone_column']);
                    $emailColumn = !empty($data['email_column']) ? $this->getColumnIndex($data['email_column']) : null;
                    $addressColumn = !empty($data['address_column']) ? $this->getColumnIndex($data['address_column']) : null;
                    $cityColumn = !empty($data['city_column']) ? $this->getColumnIndex($data['city_column']) : null;

                    $handle = fopen($file, 'r');

                    // Skip header row if needed
                    if ($hasHeader) {
                        fgetcsv($handle);
                    }

                    $imported = 0;
                    $skipped = 0;

                    while (($row = fgetcsv($handle)) !== false) {
                        $name = $row[$nameColumn] ?? null;
                        $phone = $row[$phoneColumn] ?? null;

                        // Skip if required fields are empty
                        if (empty($name) || empty($phone)) {
                            $skipped++;
                            continue;
                        }

                        // Format phone number
                        $phone = preg_replace('/[^0-9]/', '', $phone);

                        // Check if customer already exists
                        $exists = Customer::where('phone', $phone)->exists();
                        if ($exists) {
                            $skipped++;
                            continue;
                        }

                        // Create new customer
                        Customer::create([
                            'name' => $name,
                            'phone' => $phone,
                            'email' => $emailColumn !== null ? ($row[$emailColumn] ?? null) : null,
                            'address' => $addressColumn !== null ? ($row[$addressColumn] ?? null) : null,
                            'city' => $cityColumn !== null ? ($row[$cityColumn] ?? null) : null,
                            'is_active' => true,
                        ]);

                        $imported++;
                    }

                    fclose($handle);

                    // Delete the temporary file
                    Storage::disk('public')->delete($data['csv_file']);

                    Notification::make()
                        ->title("Import selesai: {$imported} pelanggan berhasil diimpor, {$skipped} dilewati")
                        ->success()
                        ->send();
                }),
        ];
    }

    private function getColumnIndex(string $column): int
    {
        return ord(strtoupper($column)) - ord('A');
    }
}
