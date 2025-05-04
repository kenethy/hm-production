#!/bin/bash

# Pastikan script berhenti jika ada error
set -e

# Variabel
DOMAIN="hartonomotor.xyz"

# Pesan
echo "Memperpanjang sertifikat SSL untuk $DOMAIN..."

# Renew sertifikat
docker-compose run --rm certbot renew

# Restart webserver
docker-compose restart webserver

echo "Perpanjangan sertifikat SSL selesai!"
