#!/bin/bash

# Pastikan script berhenti jika ada error
set -e

# Variabel
DATE=$(date +%Y-%m-%d_%H-%M-%S)
BACKUP_DIR="./backups"
BACKUP_FILE="$BACKUP_DIR/hartono_motor_$DATE.sql"

# Pesan
echo "Memulai backup database..."

# Buat direktori backup jika belum ada
mkdir -p $BACKUP_DIR

# Backup database
echo "Backing up database..."
docker-compose exec -T db mysqldump -u root -pjuanmak123 hartono_motor > $BACKUP_FILE

# Compress backup
echo "Compressing backup..."
gzip $BACKUP_FILE

echo "Backup selesai! File tersimpan di $BACKUP_FILE.gz"
