#!/bin/bash

# Pastikan script berhenti jika ada error
set -e

# Cek apakah file backup diberikan
if [ -z "$1" ]; then
    echo "Penggunaan: $0 <file_backup>"
    exit 1
fi

# Variabel
BACKUP_FILE=$1
TEMP_FILE="./temp_backup.sql"

# Pesan
echo "Memulai restore database dari $BACKUP_FILE..."

# Decompress jika file terkompresi
if [[ $BACKUP_FILE == *.gz ]]; then
    echo "Decompressing backup file..."
    gunzip -c $BACKUP_FILE > $TEMP_FILE
else
    cp $BACKUP_FILE $TEMP_FILE
fi

# Restore database
echo "Restoring database..."
docker-compose exec -T db mysql -u root -pjuanmak123 hartono_motor < $TEMP_FILE

# Hapus file temporary
rm $TEMP_FILE

echo "Restore database selesai!"
