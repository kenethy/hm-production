#!/bin/bash

# Script untuk membersihkan file backup di seluruh proyek

echo "====================================================="
echo "      PEMBERSIHAN FILE BACKUP                        "
echo "====================================================="
echo ""
echo "Script ini akan:"
echo "1. Mencari dan menghapus semua file backup (*.bak, *.save, *.backup, dll)"
echo "2. Mencari dan menghapus file backup dengan tanggal (contoh: file_20250505.php)"
echo ""
echo "PERINGATAN: Pastikan Anda memiliki backup proyek sebelum melanjutkan!"
echo ""
read -p "Lanjutkan? (y/n): " confirm
if [[ $confirm != "y" && $confirm != "Y" ]]; then
    echo "Operasi dibatalkan."
    exit 1
fi

# Deteksi apakah berjalan di Docker
IN_DOCKER=false
if [ -f /.dockerenv ] || grep -q docker /proc/1/cgroup 2>/dev/null; then
    IN_DOCKER=true
    echo "Terdeteksi berjalan di dalam container Docker."
    APP_ROOT="/var/www/html"
else
    echo "Terdeteksi berjalan di lingkungan host biasa."
    APP_ROOT="."
fi

echo "Menggunakan path root aplikasi: $APP_ROOT"
echo ""

# 1. Mencari dan menghapus file backup dengan ekstensi umum
echo ">> Mencari file backup dengan ekstensi umum..."
BACKUP_FILES=$(find $APP_ROOT -type f \( -name "*.bak" -o -name "*.backup" -o -name "*.save" -o -name "*.old" -o -name "*.orig" -o -name "*.copy" \))

if [ -z "$BACKUP_FILES" ]; then
    echo "Tidak ditemukan file backup dengan ekstensi umum."
else
    echo "Ditemukan file backup dengan ekstensi umum:"
    echo "$BACKUP_FILES"
    echo ""
    
    read -p "Hapus file-file ini? (y/n): " confirm_delete
    if [[ $confirm_delete == "y" || $confirm_delete == "Y" ]]; then
        find $APP_ROOT -type f \( -name "*.bak" -o -name "*.backup" -o -name "*.save" -o -name "*.old" -o -name "*.orig" -o -name "*.copy" \) -delete
        echo "File backup dengan ekstensi umum telah dihapus."
    else
        echo "File backup dengan ekstensi umum tidak dihapus."
    fi
fi
echo ""

# 2. Mencari dan menghapus file backup dengan pola tanggal
echo ">> Mencari file backup dengan pola tanggal..."
DATE_PATTERN_FILES=$(find $APP_ROOT -type f -regextype posix-extended -regex ".*_[0-9]{8,14}\.[a-zA-Z0-9]+$")

if [ -z "$DATE_PATTERN_FILES" ]; then
    echo "Tidak ditemukan file backup dengan pola tanggal."
else
    echo "Ditemukan file backup dengan pola tanggal:"
    echo "$DATE_PATTERN_FILES"
    echo ""
    
    read -p "Hapus file-file ini? (y/n): " confirm_delete_date
    if [[ $confirm_delete_date == "y" || $confirm_delete_date == "Y" ]]; then
        find $APP_ROOT -type f -regextype posix-extended -regex ".*_[0-9]{8,14}\.[a-zA-Z0-9]+$" -delete
        echo "File backup dengan pola tanggal telah dihapus."
    else
        echo "File backup dengan pola tanggal tidak dihapus."
    fi
fi
echo ""

# 3. Mencari dan menghapus file di direktori .history
echo ">> Mencari file di direktori .history..."
if [ -d "$APP_ROOT/.history" ]; then
    HISTORY_FILES_COUNT=$(find $APP_ROOT/.history -type f | wc -l)
    echo "Ditemukan $HISTORY_FILES_COUNT file di direktori .history."
    
    read -p "Hapus direktori .history? (y/n): " confirm_delete_history
    if [[ $confirm_delete_history == "y" || $confirm_delete_history == "Y" ]]; then
        rm -rf $APP_ROOT/.history
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
if [ -f "$APP_ROOT/public/index.php.bak" ]; then
    echo "Ditemukan file index.php.bak."
    
    read -p "Hapus file index.php.bak? (y/n): " confirm_delete_index
    if [[ $confirm_delete_index == "y" || $confirm_delete_index == "Y" ]]; then
        rm $APP_ROOT/public/index.php.bak
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
if [ -f "$APP_ROOT/public/.htaccess.bak" ]; then
    echo "Ditemukan file .htaccess.bak."
    
    read -p "Hapus file .htaccess.bak? (y/n): " confirm_delete_htaccess
    if [[ $confirm_delete_htaccess == "y" || $confirm_delete_htaccess == "Y" ]]; then
        rm $APP_ROOT/public/.htaccess.bak
        echo "File .htaccess.bak telah dihapus."
    else
        echo "File .htaccess.bak tidak dihapus."
    fi
else
    echo "File .htaccess.bak tidak ditemukan."
fi
echo ""

echo "====================================================="
echo "      PEMBERSIHAN SELESAI                            "
echo "====================================================="
echo ""
echo "Pembersihan file backup telah selesai."
echo "Jika Anda ingin menonaktifkan fitur pembuatan backup otomatis,"
echo "silakan jalankan script disable-auto-backup.php"
echo "====================================================="
