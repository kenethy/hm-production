#!/bin/bash

# Script untuk membersihkan file backup di lingkungan Docker

echo "====================================================="
echo "  PEMBERSIHAN FILE BACKUP (DOCKER)                   "
echo "====================================================="
echo ""
echo "Script ini akan:"
echo "1. Mencari dan menghapus semua file backup (*.bak, *.save, *.backup, dll)"
echo "2. Mencari dan menghapus file backup dengan tanggal (contoh: file_20250505.php)"
echo "3. Menghapus direktori .history"
echo ""
echo "PERINGATAN: Pastikan Anda memiliki backup proyek sebelum melanjutkan!"
echo ""
read -p "Lanjutkan? (y/n): " confirm
if [[ $confirm != "y" && $confirm != "Y" ]]; then
    echo "Operasi dibatalkan."
    exit 1
fi

# Mendapatkan nama container web (biasanya berisi php/nginx/apache)
WEB_CONTAINER=$(docker ps --format '{{.Names}}' | grep -E 'web|app|php|nginx|apache' | head -1)

if [ -z "$WEB_CONTAINER" ]; then
    echo "Tidak dapat menemukan container web. Silakan masukkan nama container secara manual:"
    read -p "Nama container: " WEB_CONTAINER
    
    if [ -z "$WEB_CONTAINER" ]; then
        echo "Nama container tidak valid. Keluar."
        exit 1
    fi
fi

echo "Menggunakan container: $WEB_CONTAINER"
echo ""

# 1. Mencari dan menghapus file backup dengan ekstensi umum
echo ">> Mencari file backup dengan ekstensi umum..."
BACKUP_FILES=$(docker exec $WEB_CONTAINER find /var/www/html -type f \( -name "*.bak" -o -name "*.backup" -o -name "*.save" -o -name "*.old" -o -name "*.orig" -o -name "*.copy" \))

if [ -z "$BACKUP_FILES" ]; then
    echo "Tidak ditemukan file backup dengan ekstensi umum."
else
    echo "Ditemukan file backup dengan ekstensi umum:"
    echo "$BACKUP_FILES"
    echo ""
    
    read -p "Hapus file-file ini? (y/n): " confirm_delete
    if [[ $confirm_delete == "y" || $confirm_delete == "Y" ]]; then
        docker exec $WEB_CONTAINER find /var/www/html -type f \( -name "*.bak" -o -name "*.backup" -o -name "*.save" -o -name "*.old" -o -name "*.orig" -o -name "*.copy" \) -delete
        echo "File backup dengan ekstensi umum telah dihapus."
    else
        echo "File backup dengan ekstensi umum tidak dihapus."
    fi
fi
echo ""

# 2. Mencari dan menghapus file backup dengan pola tanggal
echo ">> Mencari file backup dengan pola tanggal..."
DATE_PATTERN_FILES=$(docker exec $WEB_CONTAINER find /var/www/html -type f -regextype posix-extended -regex ".*_[0-9]{8,14}\.[a-zA-Z0-9]+$")

if [ -z "$DATE_PATTERN_FILES" ]; then
    echo "Tidak ditemukan file backup dengan pola tanggal."
else
    echo "Ditemukan file backup dengan pola tanggal:"
    echo "$DATE_PATTERN_FILES"
    echo ""
    
    read -p "Hapus file-file ini? (y/n): " confirm_delete_date
    if [[ $confirm_delete_date == "y" || $confirm_delete_date == "Y" ]]; then
        docker exec $WEB_CONTAINER find /var/www/html -type f -regextype posix-extended -regex ".*_[0-9]{8,14}\.[a-zA-Z0-9]+$" -delete
        echo "File backup dengan pola tanggal telah dihapus."
    else
        echo "File backup dengan pola tanggal tidak dihapus."
    fi
fi
echo ""

# 3. Mencari dan menghapus file di direktori .history
echo ">> Mencari file di direktori .history..."
HISTORY_EXISTS=$(docker exec $WEB_CONTAINER bash -c "if [ -d /var/www/html/.history ]; then echo 'yes'; else echo 'no'; fi")

if [ "$HISTORY_EXISTS" == "yes" ]; then
    HISTORY_FILES_COUNT=$(docker exec $WEB_CONTAINER find /var/www/html/.history -type f | wc -l)
    echo "Ditemukan $HISTORY_FILES_COUNT file di direktori .history."
    
    read -p "Hapus direktori .history? (y/n): " confirm_delete_history
    if [[ $confirm_delete_history == "y" || $confirm_delete_history == "Y" ]]; then
        docker exec $WEB_CONTAINER rm -rf /var/www/html/.history
        echo "Direktori .history telah dihapus."
    else
        echo "Direktori .history tidak dihapus."
    fi
else
    echo "Direktori .history tidak ditemukan."
fi
echo ""

# 4. Mencari dan menghapus file index.php.bak yang mungkin dibuat oleh script perbaikan
echo ">> Mencari file index.php.bak..."
INDEX_BAK_EXISTS=$(docker exec $WEB_CONTAINER bash -c "if [ -f /var/www/html/public/index.php.bak ]; then echo 'yes'; else echo 'no'; fi")

if [ "$INDEX_BAK_EXISTS" == "yes" ]; then
    echo "Ditemukan file index.php.bak."
    
    read -p "Hapus file index.php.bak? (y/n): " confirm_delete_index
    if [[ $confirm_delete_index == "y" || $confirm_delete_index == "Y" ]]; then
        docker exec $WEB_CONTAINER rm /var/www/html/public/index.php.bak
        echo "File index.php.bak telah dihapus."
    else
        echo "File index.php.bak tidak dihapus."
    fi
else
    echo "File index.php.bak tidak ditemukan."
fi
echo ""

# 5. Mencari dan menghapus file .htaccess.bak yang mungkin dibuat oleh script perbaikan
echo ">> Mencari file .htaccess.bak..."
HTACCESS_BAK_EXISTS=$(docker exec $WEB_CONTAINER bash -c "if [ -f /var/www/html/public/.htaccess.bak ]; then echo 'yes'; else echo 'no'; fi")

if [ "$HTACCESS_BAK_EXISTS" == "yes" ]; then
    echo "Ditemukan file .htaccess.bak."
    
    read -p "Hapus file .htaccess.bak? (y/n): " confirm_delete_htaccess
    if [[ $confirm_delete_htaccess == "y" || $confirm_delete_htaccess == "Y" ]]; then
        docker exec $WEB_CONTAINER rm /var/www/html/public/.htaccess.bak
        echo "File .htaccess.bak telah dihapus."
    else
        echo "File .htaccess.bak tidak dihapus."
    fi
else
    echo "File .htaccess.bak tidak ditemukan."
fi
echo ""

# 6. Jalankan script PHP untuk menonaktifkan fitur backup otomatis
echo ">> Menonaktifkan fitur backup otomatis..."
read -p "Jalankan script untuk menonaktifkan fitur backup otomatis? (y/n): " confirm_disable
if [[ $confirm_disable == "y" || $confirm_disable == "Y" ]]; then
    # Salin script ke container
    docker cp disable-auto-backup.php $WEB_CONTAINER:/var/www/html/
    
    # Jalankan script
    docker exec $WEB_CONTAINER php /var/www/html/disable-auto-backup.php
    
    # Hapus script setelah dijalankan
    docker exec $WEB_CONTAINER rm /var/www/html/disable-auto-backup.php
    
    echo "Fitur backup otomatis telah dinonaktifkan."
else
    echo "Fitur backup otomatis tidak dinonaktifkan."
fi
echo ""

echo "====================================================="
echo "      PEMBERSIHAN SELESAI                            "
echo "====================================================="
echo ""
echo "Pembersihan file backup telah selesai."
echo "====================================================="
