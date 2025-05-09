#!/bin/bash
# recreate-edit-service.sh - Script untuk membuat ulang file EditService.php dengan versi terbaru

# Warna untuk output
GREEN='\033[0;32m'
RED='\033[0;31m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Path file
EDIT_SERVICE_PATH="app/Filament/Resources/ServiceResource/Pages/EditService.php"
BACKUP_DIR="app/Filament/Resources/ServiceResource/Pages/backups"
BACKUP_FILE="${BACKUP_DIR}/EditService.php.bak.$(date +%Y%m%d%H%M%S)"

echo -e "${YELLOW}Memulai pembuatan ulang file EditService.php...${NC}\n"

# 1. Buat direktori backup jika belum ada
echo -e "${YELLOW}1. Membuat direktori backup...${NC}"
mkdir -p "$BACKUP_DIR"
echo -e "   ${GREEN}✓${NC} Direktori backup berhasil dibuat"

# 2. Backup file asli
echo -e "\n${YELLOW}2. Membuat backup file asli...${NC}"
if [ -f "$EDIT_SERVICE_PATH" ]; then
    cp "$EDIT_SERVICE_PATH" "$BACKUP_FILE"
    echo -e "   ${GREEN}✓${NC} File asli berhasil dibackup ke $BACKUP_FILE"
else
    echo -e "   ${RED}✗${NC} File asli tidak ditemukan"
fi

# 3. Buat file EditService.php baru
echo -e "\n${YELLOW}3. Membuat file EditService.php baru...${NC}"

cat > "$EDIT_SERVICE_PATH" << 'EOL'
<?php

namespace App\Filament\Resources\ServiceResource\Pages;

use App\Filament\Resources\ServiceResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Log;
use Filament\Notifications\Notification;

class EditService extends EditRecord
{
    protected static string $resource = ServiceResource::class;
    
    public function mount($record): void
    {
        parent::mount($record);
        
        // Log untuk debugging
        Log::info("EditService: Mounting edit page for service #{$record->id}");
        
        // Pastikan mechanic_costs diisi dengan benar
        $this->fillMechanicCosts();
    }
    
    protected function fillMechanicCosts(): void
    {
        // Ambil data service
        $service = $this->record;
        
        // Jika tidak ada service, keluar
        if (!$service) {
            return;
        }
        
        // Log untuk debugging
        Log::info("EditService: Filling mechanic costs for service #{$service->id}");
        
        // Ambil data form saat ini
        $data = $this->data;
        
        // Jika mechanic_costs sudah diisi, keluar
        if (isset($data['mechanic_costs']) && is_array($data['mechanic_costs']) && !empty($data['mechanic_costs'])) {
            Log::info("EditService: Mechanic costs already filled", $data['mechanic_costs']);
            return;
        }
        
        // Siapkan mechanic_costs berdasarkan montir yang ada di database
        if ($service->mechanics()->count() > 0) {
            $mechanicCosts = [];
            
            foreach ($service->mechanics as $mechanic) {
                $laborCost = $mechanic->pivot->labor_cost;
                
                // Pastikan labor_cost tidak 0, tapi jangan override nilai yang sudah diisi
                if (empty($laborCost) || $laborCost == 0) {
                    $laborCost = 50000; // Default labor cost
                } else {
                    // Gunakan nilai yang sudah diisi
                    Log::info("EditService: Using existing labor cost for mechanic #{$mechanic->id}: {$laborCost}");
                }
                
                $mechanicCosts[] = [
                    'mechanic_id' => $mechanic->id,
                    'labor_cost' => $laborCost,
                ];
            }
            
            // Log mechanic_costs yang akan diisi ke form
            Log::info("EditService: Setting mechanic costs in mount", $mechanicCosts);
            
            // Tambahkan mechanic_costs ke data
            $data['mechanic_costs'] = $mechanicCosts;
            
            // Pastikan mechanics juga diisi dengan benar
            if (!isset($data['mechanics']) || !is_array($data['mechanics']) || empty($data['mechanics'])) {
                $data['mechanics'] = $service->mechanics()->pluck('mechanic_id')->toArray();
                Log::info("EditService: Setting mechanics in mount", $data['mechanics']);
            }
            
            // Update data form
            $this->form->fill($data);
        }
    }
    
    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Log data yang akan diisi ke form
        Log::info("EditService: Form data before fill", $data);
        
        // Ambil data service
        $service = $this->record;
        
        // Siapkan mechanic_costs berdasarkan montir yang ada di database
        if ($service && $service->mechanics()->count() > 0) {
            $mechanicCosts = [];
            
            foreach ($service->mechanics as $mechanic) {
                $laborCost = $mechanic->pivot->labor_cost;
                
                // Pastikan labor_cost tidak 0, tapi jangan override nilai yang sudah diisi
                if (empty($laborCost) || $laborCost == 0) {
                    $laborCost = 50000; // Default labor cost
                } else {
                    // Gunakan nilai yang sudah diisi
                    Log::info("EditService: Using existing labor cost for mechanic #{$mechanic->id}: {$laborCost}");
                }
                
                $mechanicCosts[] = [
                    'mechanic_id' => $mechanic->id,
                    'labor_cost' => $laborCost,
                ];
            }
            
            // Log mechanic_costs yang akan diisi ke form
            Log::info("EditService: Mechanic costs data from database", $mechanicCosts);
            
            // Tambahkan mechanic_costs ke data
            $data['mechanic_costs'] = $mechanicCosts;
            
            // Pastikan mechanics juga diisi dengan benar
            if (!isset($data['mechanics']) || !is_array($data['mechanics']) || empty($data['mechanics'])) {
                $data['mechanics'] = $service->mechanics()->pluck('mechanic_id')->toArray();
                Log::info("EditService: Setting mechanics from database", $data['mechanics']);
            }
        }
        
        return $data;
    }
    
    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('refreshMechanicCosts')
                ->label('Refresh Biaya Jasa')
                ->icon('heroicon-o-arrow-path')
                ->color('warning')
                ->action(function () {
                    // Tidak perlu melakukan apa-apa, hanya untuk memicu refresh halaman
                    Notification::make()
                        ->title('Refresh biaya jasa berhasil')
                        ->success()
                        ->send();
                }),
            Actions\Action::make('sendFollowUpWhatsApp')
                ->label('Kirim Follow-up WhatsApp')
                ->icon('heroicon-o-chat-bubble-left-ellipsis')
                ->color('success')
                ->visible(fn() => $this->record->status === 'completed')
                ->form([
                    \Filament\Forms\Components\Select::make('template')
                        ->label('Template Pesan')
                        ->options([
                            'follow_up' => 'Follow-up Standar',
                            'feedback' => 'Minta Feedback',
                            'promo' => 'Tawarkan Promo Berikutnya',
                        ])
                        ->default('follow_up')
                        ->required(),
                    \Filament\Forms\Components\Textarea::make('custom_message')
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

                    // Tampilkan notifikasi sukses
                    Notification::make()
                        ->title('Pesan follow-up siap dikirim')
                        ->body('WhatsApp akan terbuka dengan pesan yang sudah disiapkan.')
                        ->success()
                        ->send();

                    // Redirect ke URL WhatsApp
                    redirect()->away($whatsappUrl);
                }),
        ];
    }
    
    protected function afterSave(): void
    {
        // Ambil data form
        $formData = $this->form->getState();
        
        // Ambil service
        $service = $this->record;
        
        // Log untuk debugging
        Log::info("EditService: afterSave called for service #{$service->id}", [
            'formData' => $formData,
        ]);
        
        // Jika ada mechanic_costs, update pivot table
        if (isset($formData['mechanic_costs']) && is_array($formData['mechanic_costs'])) {
            foreach ($formData['mechanic_costs'] as $cost) {
                if (isset($cost['mechanic_id']) && isset($cost['labor_cost'])) {
                    $mechanicId = $cost['mechanic_id'];
                    $laborCost = $cost['labor_cost'];
                    
                    // Pastikan biaya jasa tidak 0, tapi jangan override nilai yang sudah diisi
                    if ($laborCost == 0) {
                        $laborCost = 50000; // Default labor cost
                    } else {
                        // Gunakan nilai yang sudah diisi
                        Log::info("Using existing labor cost for mechanic #{$mechanicId}: {$laborCost}");
                    }
                    
                    // Dapatkan tanggal awal dan akhir minggu saat ini (Senin-Minggu)
                    $now = now();
                    $weekStart = $now->copy()->startOfWeek();
                    $weekEnd = $now->copy()->endOfWeek();
                    
                    // Update pivot table
                    $service->mechanics()->updateExistingPivot($mechanicId, [
                        'labor_cost' => $laborCost,
                        'week_start' => $weekStart,
                        'week_end' => $weekEnd,
                    ]);
                    
                    // Hitung ulang total biaya jasa pada service
                    $totalLaborCost = 0;
                    foreach ($formData['mechanic_costs'] as $cost) {
                        if (isset($cost['labor_cost']) && $cost['labor_cost'] > 0) {
                            $totalLaborCost += (int)$cost['labor_cost'];
                            Log::info("EditService: Adding labor cost: " . (int)$cost['labor_cost'] . " for mechanic ID: " . ($cost['mechanic_id'] ?? 'unknown'));
                        }
                    }
                    
                    // Update total biaya
                    $service->labor_cost = $totalLaborCost;
                    $service->total_cost = $totalLaborCost;
                    $service->save();
                    
                    Log::info("EditService: Updated total labor cost for service #{$service->id} to {$totalLaborCost}");
                }
            }
        }
    }
}
EOL

# Periksa apakah file berhasil dibuat
if [ -f "$EDIT_SERVICE_PATH" ]; then
    echo -e "   ${GREEN}✓${NC} File EditService.php baru berhasil dibuat"
else
    echo -e "   ${RED}✗${NC} Gagal membuat file EditService.php baru"
    exit 1
fi

# 4. Validasi file PHP
echo -e "\n${YELLOW}4. Memvalidasi file PHP...${NC}"
php -l "$EDIT_SERVICE_PATH"
if [ $? -ne 0 ]; then
    echo -e "   ${RED}✗${NC} File PHP tidak valid"
    echo -e "   ${YELLOW}Mengembalikan file dari backup...${NC}"
    cp "$BACKUP_FILE" "$EDIT_SERVICE_PATH"
    echo -e "   ${GREEN}✓${NC} File berhasil dikembalikan dari backup"
    exit 1
fi
echo -e "   ${GREEN}✓${NC} File PHP valid"

# 5. Clear cache dan optimize
echo -e "\n${YELLOW}5. Membersihkan cache dan mengoptimalkan aplikasi...${NC}"
docker-compose exec -T app php artisan cache:clear
docker-compose exec -T app php artisan config:clear
docker-compose exec -T app php artisan view:clear
docker-compose exec -T app php artisan route:clear
docker-compose exec -T app php artisan optimize
echo -e "   ${GREEN}✓${NC} Cache dibersihkan dan aplikasi dioptimalkan"

# 6. Restart container aplikasi
echo -e "\n${YELLOW}6. Me-restart container aplikasi...${NC}"
docker-compose restart app
if [ $? -ne 0 ]; then
  echo -e "   ${RED}✗${NC} Gagal me-restart container aplikasi"
  exit 1
fi
echo -e "   ${GREEN}✓${NC} Container aplikasi berhasil di-restart"

echo -e "\n${GREEN}Pembuatan ulang file EditService.php selesai!${NC}"
echo -e "${YELLOW}Catatan:${NC}"
echo -e "1. File EditService.php telah dibuat ulang dengan versi terbaru"
echo -e "2. Backup file asli disimpan di $BACKUP_FILE"
echo -e "3. Jika terjadi masalah, Anda dapat mengembalikan file dari backup"
echo -e "4. Untuk menguji perbaikan:"
echo -e "   - Buka halaman edit servis"
echo -e "   - Pastikan tidak ada error 'Cannot redeclare'"
echo -e "   - Pastikan biaya jasa montir muncul dengan benar"
echo -e "   - Pastikan total biaya servis dihitung dengan benar"
