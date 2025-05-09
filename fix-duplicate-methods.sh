#!/bin/bash
# fix-duplicate-methods.sh - Script untuk memperbaiki masalah duplikasi metode di file PHP

# Warna untuk output
GREEN='\033[0;32m'
RED='\033[0;31m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Fungsi untuk memperbaiki duplikasi metode di file
fix_duplicate_methods() {
    local file_path="$1"
    local method_name="$2"
    local temp_file="/tmp/fixed_file.php"
    local backup_file="${file_path}.bak.$(date +%Y%m%d%H%M%S)"
    
    echo -e "${BLUE}Memeriksa duplikasi metode ${method_name} di ${file_path}...${NC}"
    
    # Hitung jumlah deklarasi metode
    local count=$(grep -c "function ${method_name}(" "$file_path")
    
    if [ "$count" -gt 1 ]; then
        echo -e "${YELLOW}Ditemukan ${count} deklarasi metode ${method_name}!${NC}"
        
        # Buat backup file asli
        cp "$file_path" "$backup_file"
        echo -e "${BLUE}Backup file asli disimpan di ${backup_file}${NC}"
        
        # Cari baris awal dan akhir dari setiap deklarasi metode
        declare -a start_lines
        declare -a end_lines
        
        # Temukan semua baris awal deklarasi metode
        while read -r line; do
            start_lines+=("$line")
        done < <(grep -n "function ${method_name}(" "$file_path" | cut -d: -f1)
        
        # Untuk setiap deklarasi metode, temukan baris akhirnya (tutup kurung kurawal)
        for start_line in "${start_lines[@]}"; do
            # Mulai dari baris deklarasi metode
            local line_num=$start_line
            local brace_count=0
            local found_end=false
            
            # Baca file baris per baris mulai dari deklarasi metode
            while IFS= read -r line && [ "$found_end" = false ]; do
                # Hitung kurung kurawal buka
                local open_braces=$(echo "$line" | grep -o "{" | wc -l)
                brace_count=$((brace_count + open_braces))
                
                # Hitung kurung kurawal tutup
                local close_braces=$(echo "$line" | grep -o "}" | wc -l)
                brace_count=$((brace_count - close_braces))
                
                # Jika jumlah kurung kurawal kembali ke 0, kita telah menemukan akhir metode
                if [ "$brace_count" -eq 0 ] && [ "$open_braces" -gt 0 -o "$close_braces" -gt 0 ]; then
                    end_lines+=("$line_num")
                    found_end=true
                fi
                
                line_num=$((line_num + 1))
            done < <(tail -n +$start_line "$file_path")
            
            # Jika tidak menemukan akhir metode, gunakan baris terakhir file
            if [ "$found_end" = false ]; then
                end_lines+=("$(wc -l < "$file_path")")
            fi
        done
        
        echo -e "${BLUE}Menemukan ${#start_lines[@]} deklarasi metode dengan baris awal: ${start_lines[*]}${NC}"
        echo -e "${BLUE}Baris akhir metode: ${end_lines[*]}${NC}"
        
        # Pilih deklarasi metode terlengkap (biasanya yang terakhir)
        local keep_start=${start_lines[-1]}
        local keep_end=${end_lines[-1]}
        
        echo -e "${YELLOW}Mempertahankan deklarasi metode dari baris ${keep_start} hingga ${keep_end}${NC}"
        
        # Buat file sementara tanpa deklarasi metode duplikat
        awk -v method="function ${method_name}(" \
            -v keep_start="$keep_start" \
            -v keep_end="$keep_end" \
            '
            BEGIN { printing = 1; skip_mode = 0; }
            {
                if ($0 ~ method && NR != keep_start) {
                    printing = 0;
                    skip_mode = 1;
                    next;
                }
                
                if (skip_mode == 1 && $0 ~ /}/) {
                    skip_mode = 0;
                    printing = 1;
                    next;
                }
                
                if (printing == 1) {
                    print $0;
                }
            }
            ' "$file_path" > "$temp_file"
        
        # Periksa apakah file sementara valid
        php -l "$temp_file" > /dev/null 2>&1
        if [ $? -eq 0 ]; then
            # Ganti file asli dengan file yang sudah diperbaiki
            mv "$temp_file" "$file_path"
            echo -e "${GREEN}Berhasil memperbaiki duplikasi metode ${method_name} di ${file_path}${NC}"
            return 0
        else
            echo -e "${RED}File hasil perbaikan tidak valid! Membatalkan perubahan.${NC}"
            echo -e "${YELLOW}Silakan perbaiki file secara manual: ${file_path}${NC}"
            return 1
        fi
    else
        echo -e "${GREEN}Tidak ditemukan duplikasi metode ${method_name} di ${file_path}${NC}"
        return 0
    fi
}

# Fungsi untuk memperbaiki file backup yang mungkin menyebabkan konflik
fix_backup_files() {
    local dir_path="$1"
    local pattern="*.php.*"
    
    echo -e "${BLUE}Memeriksa file backup di ${dir_path}...${NC}"
    
    # Cari semua file backup PHP
    local backup_files=$(find "$dir_path" -name "$pattern" 2>/dev/null)
    
    if [ -n "$backup_files" ]; then
        echo -e "${YELLOW}Ditemukan file backup PHP:${NC}"
        echo "$backup_files"
        
        # Buat direktori untuk memindahkan file backup
        local backup_dir="${dir_path}/php_backups"
        mkdir -p "$backup_dir"
        
        # Pindahkan semua file backup ke direktori backup
        for file in $backup_files; do
            mv "$file" "${backup_dir}/$(basename "$file")"
            echo -e "${BLUE}Memindahkan $(basename "$file") ke ${backup_dir}${NC}"
        done
        
        echo -e "${GREEN}Semua file backup PHP telah dipindahkan ke ${backup_dir}${NC}"
    else
        echo -e "${GREEN}Tidak ditemukan file backup PHP di ${dir_path}${NC}"
    fi
}

# Main script
echo -e "${YELLOW}Memulai perbaikan masalah duplikasi metode...${NC}\n"

# 1. Perbaiki file EditService.php
echo -e "${YELLOW}1. Memperbaiki file EditService.php...${NC}"
fix_duplicate_methods "app/Filament/Resources/ServiceResource/Pages/EditService.php" "afterSave"
fix_duplicate_methods "app/Filament/Resources/ServiceResource/Pages/EditService.php" "mount"
fix_duplicate_methods "app/Filament/Resources/ServiceResource/Pages/EditService.php" "mutateFormDataBeforeFill"
fix_duplicate_methods "app/Filament/Resources/ServiceResource/Pages/EditService.php" "fillMechanicCosts"

# 2. Perbaiki file backup yang mungkin menyebabkan konflik
echo -e "\n${YELLOW}2. Memperbaiki file backup yang mungkin menyebabkan konflik...${NC}"
fix_backup_files "app/Filament/Resources/ServiceResource/Pages"

# 3. Clear cache dan optimize
echo -e "\n${YELLOW}3. Membersihkan cache dan mengoptimalkan aplikasi...${NC}"
docker-compose exec -T app php artisan cache:clear
docker-compose exec -T app php artisan config:clear
docker-compose exec -T app php artisan view:clear
docker-compose exec -T app php artisan route:clear
docker-compose exec -T app php artisan optimize
echo -e "   ${GREEN}✓${NC} Cache dibersihkan dan aplikasi dioptimalkan"

# 4. Restart container aplikasi
echo -e "\n${YELLOW}4. Me-restart container aplikasi...${NC}"
docker-compose restart app
if [ $? -ne 0 ]; then
  echo -e "   ${RED}✗${NC} Gagal me-restart container aplikasi"
  exit 1
fi
echo -e "   ${GREEN}✓${NC} Container aplikasi berhasil di-restart"

echo -e "\n${GREEN}Perbaikan masalah duplikasi metode selesai!${NC}"
echo -e "${YELLOW}Catatan:${NC}"
echo -e "1. Script ini telah memperbaiki:"
echo -e "   - Duplikasi metode afterSave() di EditService.php"
echo -e "   - Duplikasi metode mount() di EditService.php"
echo -e "   - Duplikasi metode mutateFormDataBeforeFill() di EditService.php"
echo -e "   - Duplikasi metode fillMechanicCosts() di EditService.php"
echo -e "   - File backup PHP yang mungkin menyebabkan konflik"
echo -e "2. Jika masih terjadi error, jalankan script ini lagi atau perbaiki file secara manual"
echo -e "3. Backup file asli disimpan dengan ekstensi .bak.YYYYMMDDHHMMSS"
