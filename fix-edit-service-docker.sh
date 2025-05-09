#!/bin/bash
# fix-edit-service-docker.sh - Script untuk memperbaiki masalah "Attempt to read property id on string" di Docker

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

echo -e "${YELLOW}Memulai perbaikan masalah 'Attempt to read property id on string' di Docker...${NC}\n"

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
    exit 1
fi

# 3. Perbaiki metode mount
echo -e "\n${YELLOW}3. Memperbaiki metode mount...${NC}"
sed -i '
/public function mount($record): void/,/}/c\
    public function mount($record): void\
    {\
        parent::mount($record);\
        \
        // Log untuk debugging\
        if (is_object($record) && method_exists($record, "getKey")) {\
            Log::info("EditService: Mounting edit page for service #{$record->getKey()}");\
        } else {\
            Log::info("EditService: Mounting edit page for service", ["record_type" => gettype($record)]);\
        }\
        \
        // Pastikan mechanic_costs diisi dengan benar\
        $this->fillMechanicCosts();\
    }
' "$EDIT_SERVICE_PATH"

# 4. Perbaiki metode fillMechanicCosts
echo -e "\n${YELLOW}4. Memperbaiki metode fillMechanicCosts...${NC}"
sed -i '
/protected function fillMechanicCosts(): void/,/Log::info("EditService: Filling mechanic costs for service #{$service->id}");/c\
    protected function fillMechanicCosts(): void\
    {\
        // Ambil data service\
        $service = $this->record;\
        \
        // Jika tidak ada service atau bukan objek, keluar\
        if (!$service || !is_object($service)) {\
            Log::info("EditService: No valid service record found", ["record_type" => gettype($service)]);\
            return;\
        }\
        \
        // Log untuk debugging\
        Log::info("EditService: Filling mechanic costs for service #{$service->getKey()}");
' "$EDIT_SERVICE_PATH"

# 5. Perbaiki metode mutateFormDataBeforeFill
echo -e "\n${YELLOW}5. Memperbaiki metode mutateFormDataBeforeFill...${NC}"
sed -i '
/        \/\/ Ambil data service/,/        if ($service && $service->mechanics()->count() > 0) {/c\
        // Ambil data service\
        $service = $this->record;\
        \
        // Jika tidak ada service atau bukan objek, keluar\
        if (!$service || !is_object($service)) {\
            Log::info("EditService: No valid service record in mutateFormDataBeforeFill", ["record_type" => gettype($service)]);\
            return $data;\
        }\
        \
        // Siapkan mechanic_costs berdasarkan montir yang ada di database\
        if (method_exists($service, "mechanics") && $service->mechanics()->count() > 0) {
' "$EDIT_SERVICE_PATH"

# 6. Perbaiki metode afterSave
echo -e "\n${YELLOW}6. Memperbaiki metode afterSave...${NC}"
sed -i '
/        \/\/ Ambil data service yang baru disimpan/,/        ]);/c\
        // Ambil data service yang baru disimpan\
        $service = $this->record;\
        \
        // Jika tidak ada service atau bukan objek, keluar\
        if (!$service || !is_object($service)) {\
            Log::info("EditService: No valid service record in afterSave", ["record_type" => gettype($service)]);\
            return;\
        }\
        \
        // Log untuk debugging\
        Log::info("EditService: After save for service #{$service->getKey()}", [\
            "status" => $service->status ?? "unknown",\
            "mechanics" => method_exists($service, "mechanics") ? $service->mechanics()->pluck("mechanic_id")->toArray() : [],\
        ]);
' "$EDIT_SERVICE_PATH"

# 7. Validasi file PHP
echo -e "\n${YELLOW}7. Memvalidasi file PHP...${NC}"
docker-compose exec app php -l "$EDIT_SERVICE_PATH"
if [ $? -ne 0 ]; then
    echo -e "   ${RED}✗${NC} File PHP tidak valid"
    echo -e "   ${YELLOW}Mengembalikan file dari backup...${NC}"
    cp "$BACKUP_FILE" "$EDIT_SERVICE_PATH"
    echo -e "   ${GREEN}✓${NC} File berhasil dikembalikan dari backup"
    exit 1
fi
echo -e "   ${GREEN}✓${NC} File PHP valid"

# 8. Restart container aplikasi
echo -e "\n${YELLOW}8. Me-restart container aplikasi...${NC}"
docker-compose restart app
if [ $? -ne 0 ]; then
  echo -e "   ${RED}✗${NC} Gagal me-restart container aplikasi"
  exit 1
fi
echo -e "   ${GREEN}✓${NC} Container aplikasi berhasil di-restart"

# 9. Bersihkan cache Laravel
echo -e "\n${YELLOW}9. Membersihkan cache Laravel...${NC}"
docker-compose exec app php artisan cache:clear
docker-compose exec app php artisan config:clear
docker-compose exec app php artisan view:clear
docker-compose exec app php artisan route:clear
docker-compose exec app php artisan optimize
echo -e "   ${GREEN}✓${NC} Cache Laravel berhasil dibersihkan"

echo -e "\n${GREEN}Perbaikan masalah 'Attempt to read property id on string' selesai!${NC}"
echo -e "${YELLOW}Catatan:${NC}"
echo -e "1. File EditService.php telah diperbaiki untuk menangani kasus ketika \$record adalah string"
echo -e "2. Backup file asli disimpan di $BACKUP_FILE"
echo -e "3. Container aplikasi telah di-restart dan cache Laravel telah dibersihkan"
echo -e "4. Untuk menguji perbaikan:"
echo -e "   - Buka halaman edit servis di browser"
echo -e "   - Pastikan tidak ada error 'Attempt to read property \"id\" on string'"
