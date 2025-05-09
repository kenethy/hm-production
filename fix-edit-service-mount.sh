#!/bin/bash
# fix-edit-service-mount.sh - Script untuk memperbaiki metode mount di EditService.php

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

echo -e "${YELLOW}Memulai perbaikan metode mount di EditService.php...${NC}\n"

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

# Gunakan sed untuk mengganti metode mount
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

# 5. Perbaiki metode fillMechanicCosts
echo -e "\n${YELLOW}5. Memperbaiki metode fillMechanicCosts...${NC}"

# Gunakan sed untuk mengganti metode fillMechanicCosts
sed -i '
/protected function fillMechanicCosts(): void/,/}/c\
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
        Log::info("EditService: Filling mechanic costs for service #{$service->getKey()}");\
        \
        // Ambil data form saat ini\
        $data = $this->data;\
        \
        // Jika mechanic_costs sudah diisi, keluar\
        if (isset($data["mechanic_costs"]) && is_array($data["mechanic_costs"]) && !empty($data["mechanic_costs"])) {\
            Log::info("EditService: Mechanic costs already filled", $data["mechanic_costs"]);\
            return;\
        }\
        \
        // Siapkan mechanic_costs berdasarkan montir yang ada di database\
        if (method_exists($service, "mechanics") && $service->mechanics()->count() > 0) {\
            $mechanicCosts = [];\
            \
            foreach ($service->mechanics as $mechanic) {\
                $laborCost = $mechanic->pivot->labor_cost;\
                \
                // Pastikan labor_cost tidak 0, tapi jangan override nilai yang sudah diisi\
                if (empty($laborCost) || $laborCost == 0) {\
                    $laborCost = 50000; // Default labor cost\
                } else {\
                    // Gunakan nilai yang sudah diisi\
                    Log::info("EditService: Using existing labor cost for mechanic #{$mechanic->id}: {$laborCost}");\
                }\
                \
                $mechanicCosts[] = [\
                    "mechanic_id" => $mechanic->id,\
                    "labor_cost" => $laborCost,\
                ];\
            }\
            \
            // Log mechanic_costs yang akan diisi ke form\
            Log::info("EditService: Setting mechanic costs in mount", $mechanicCosts);\
            \
            // Tambahkan mechanic_costs ke data\
            $data["mechanic_costs"] = $mechanicCosts;\
            \
            // Pastikan mechanics juga diisi dengan benar\
            if (!isset($data["mechanics"]) || !is_array($data["mechanics"]) || empty($data["mechanics"])) {\
                $data["mechanics"] = $service->mechanics()->pluck("mechanic_id")->toArray();\
                Log::info("EditService: Setting mechanics in mount", $data["mechanics"]);\
            }\
            \
            // Update data form\
            $this->form->fill($data);\
        }\
    }
' "$EDIT_SERVICE_PATH"

# 6. Validasi file PHP lagi
echo -e "\n${YELLOW}6. Memvalidasi file PHP lagi...${NC}"
php -l "$EDIT_SERVICE_PATH"
if [ $? -ne 0 ]; then
    echo -e "   ${RED}✗${NC} File PHP tidak valid"
    echo -e "   ${YELLOW}Mengembalikan file dari backup...${NC}"
    cp "$BACKUP_FILE" "$EDIT_SERVICE_PATH"
    echo -e "   ${GREEN}✓${NC} File berhasil dikembalikan dari backup"
    exit 1
fi
echo -e "   ${GREEN}✓${NC} File PHP valid"

# 7. Clear cache dan optimize
echo -e "\n${YELLOW}7. Membersihkan cache dan mengoptimalkan aplikasi...${NC}"
docker-compose exec -T app php artisan cache:clear
docker-compose exec -T app php artisan config:clear
docker-compose exec -T app php artisan view:clear
docker-compose exec -T app php artisan route:clear
docker-compose exec -T app php artisan optimize
echo -e "   ${GREEN}✓${NC} Cache dibersihkan dan aplikasi dioptimalkan"

# 8. Restart container aplikasi
echo -e "\n${YELLOW}8. Me-restart container aplikasi...${NC}"
docker-compose restart app
if [ $? -ne 0 ]; then
  echo -e "   ${RED}✗${NC} Gagal me-restart container aplikasi"
  exit 1
fi
echo -e "   ${GREEN}✓${NC} Container aplikasi berhasil di-restart"

echo -e "\n${GREEN}Perbaikan metode mount di EditService.php selesai!${NC}"
echo -e "${YELLOW}Catatan:${NC}"
echo -e "1. Metode mount telah diperbaiki untuk menangani kasus ketika \$record adalah string"
echo -e "2. Metode fillMechanicCosts telah diperbaiki untuk menangani kasus ketika \$service tidak valid"
echo -e "3. Backup file asli disimpan di $BACKUP_FILE"
echo -e "4. Jika terjadi masalah, Anda dapat mengembalikan file dari backup"
echo -e "5. Untuk menguji perbaikan:"
echo -e "   - Buka halaman edit servis"
echo -e "   - Pastikan tidak ada error 'Attempt to read property \"id\" on string'"
