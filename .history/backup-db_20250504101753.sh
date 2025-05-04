#!/bin/bash

# Pastikan script berhenti jika ada error
set -e

# Variabel
REMOTE_USER="root"
REMOTE_HOST="your_vps_ip"
REMOTE_DIR="/var/www/hartono-motor"
BACKUP_DIR="$REMOTE_DIR/backups"
DATE=$(date +%Y-%m-%d_%H-%M-%S)
BACKUP_FILE="$BACKUP_DIR/hartono_motor_$DATE.sql"

# Pesan
echo "Memulai backup database..."

# Buat direktori backup jika belum ada
ssh $REMOTE_USER@$REMOTE_HOST "mkdir -p $BACKUP_DIR"

# Backup database
ssh $REMOTE_USER@$REMOTE_HOST "cd $REMOTE_DIR && \
    source .env.docker && \
    docker-compose exec -T db mysqldump -u root -p\$DB_ROOT_PASSWORD hartono_motor > $BACKUP_FILE && \
    gzip $BACKUP_FILE"

# Download backup
echo "Mendownload backup..."
scp $REMOTE_USER@$REMOTE_HOST:$BACKUP_FILE.gz ./backups/

echo "Backup selesai! File tersimpan di ./backups/hartono_motor_$DATE.sql.gz"
