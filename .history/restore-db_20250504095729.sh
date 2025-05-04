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
REMOTE_USER="root"
REMOTE_HOST="your_vps_ip"
REMOTE_DIR="/var/www/hartono-motor"
REMOTE_BACKUP_FILE="$REMOTE_DIR/restore_backup.sql"

# Pesan
echo "Memulai restore database dari $BACKUP_FILE..."

# Upload file backup
echo "Mengupload file backup ke server..."
if [[ $BACKUP_FILE == *.gz ]]; then
    # Jika file terkompresi, decompress dulu
    gunzip -c $BACKUP_FILE > temp_backup.sql
    scp temp_backup.sql $REMOTE_USER@$REMOTE_HOST:$REMOTE_BACKUP_FILE
    rm temp_backup.sql
else
    scp $BACKUP_FILE $REMOTE_USER@$REMOTE_HOST:$REMOTE_BACKUP_FILE
fi

# Restore database
echo "Merestore database..."
ssh $REMOTE_USER@$REMOTE_HOST "cd $REMOTE_DIR && \
    docker-compose exec -T db mysql -u root -p\$MYSQL_ROOT_PASSWORD hartono_motor < $REMOTE_BACKUP_FILE && \
    rm $REMOTE_BACKUP_FILE"

echo "Restore database selesai!"
