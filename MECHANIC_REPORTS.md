# Sistem Rekap Montir

Dokumen ini menjelaskan cara kerja sistem rekap montir di aplikasi Hartono Motor.

## Konsep Dasar

Sistem rekap montir dirancang untuk menghitung total biaya jasa yang dikerjakan oleh setiap montir dalam periode mingguan (Senin-Minggu). Sistem ini bekerja dengan prinsip-prinsip berikut:

1. **Periode Mingguan**: Rekap dihitung berdasarkan minggu (Senin-Minggu)
2. **Status Servis**: Hanya servis dengan status 'completed' yang dihitung dalam rekap
3. **Biaya Jasa**: Setiap montir mendapatkan biaya jasa penuh untuk setiap servis yang dikerjakannya
4. **Perubahan Status**: Jika status servis berubah, rekap montir diperbarui secara otomatis
5. **Perubahan Montir**: Jika montir yang mengerjakan servis berubah, rekap montir diperbarui secara otomatis

## Komponen Sistem

Sistem rekap montir terdiri dari beberapa komponen:

### 1. Model

- **MechanicReport**: Model untuk menyimpan rekap montir mingguan
- **Mechanic**: Model untuk montir, memiliki relasi dengan MechanicReport
- **Service**: Model untuk servis, memiliki relasi dengan Mechanic melalui tabel pivot mechanic_service

### 2. Event

- **ServiceStatusChanged**: Event yang dipicu ketika status servis berubah
- **MechanicsAssigned**: Event yang dipicu ketika montir ditambahkan atau dihapus dari servis

### 3. Listener

- **UpdateMechanicReports**: Listener yang menangani event ServiceStatusChanged dan MechanicsAssigned untuk memperbarui rekap montir

## Alur Kerja

### 1. Servis Ditandai Sebagai Selesai

1. Status servis diubah menjadi 'completed'
2. Event ServiceStatusChanged dipicu
3. Listener UpdateMechanicReports menangani event
4. Biaya jasa ditambahkan ke rekap montir untuk minggu saat ini

### 2. Servis Dibatalkan atau Dikembalikan ke Dalam Pengerjaan

1. Status servis diubah dari 'completed' menjadi 'cancelled' atau 'in_progress'
2. Event ServiceStatusChanged dipicu
3. Listener UpdateMechanicReports menangani event
4. Biaya jasa dihapus dari rekap montir

### 3. Montir Diganti

1. Montir baru ditambahkan atau montir lama dihapus dari servis
2. Event MechanicsAssigned dipicu
3. Listener UpdateMechanicReports menangani event
4. Biaya jasa dipindahkan dari montir lama ke montir baru (jika servis berstatus 'completed')

## Kasus Penggunaan

### 1. Menandai Servis Sebagai Selesai

Ketika servis ditandai sebagai selesai:

1. Pilih montir yang mengerjakan servis (maksimal 2 montir)
2. Masukkan biaya jasa untuk setiap montir
3. Klik tombol "Selesai"
4. Sistem akan otomatis memperbarui rekap montir

### 2. Mengubah Status Servis

Ketika status servis diubah:

1. Edit servis
2. Ubah status servis
3. Simpan perubahan
4. Sistem akan otomatis memperbarui rekap montir

### 3. Mengganti Montir

Ketika montir diganti:

1. Edit servis
2. Ubah montir yang mengerjakan servis
3. Simpan perubahan
4. Sistem akan otomatis memperbarui rekap montir

## Pemecahan Masalah

### 1. Rekap Montir Tidak Diperbarui

Jika rekap montir tidak diperbarui secara otomatis:

1. Periksa log Laravel untuk melihat apakah ada error
2. Pastikan event ServiceStatusChanged dan MechanicsAssigned dipicu dengan benar
3. Pastikan listener UpdateMechanicReports berjalan dengan benar
4. Jalankan script rebuild-mechanic-reports.sh untuk membangun ulang rekap montir

### 2. Biaya Jasa Tidak Dihitung dengan Benar

Jika biaya jasa tidak dihitung dengan benar:

1. Periksa apakah servis berstatus 'completed'
2. Periksa apakah montir terkait dengan servis
3. Periksa apakah biaya jasa diisi dengan benar
4. Jalankan script rebuild-mechanic-reports.sh untuk membangun ulang rekap montir

## Perintah Berguna

### 1. Membangun Ulang Rekap Montir

```bash
chmod +x rebuild-mechanic-reports.sh
./rebuild-mechanic-reports.sh
```

### 2. Memeriksa Rekap Montir di Database

```bash
docker-compose exec app php artisan tinker --execute="DB::table('mechanic_reports')->get()"
```

### 3. Memeriksa Log Laravel

```bash
docker-compose exec app cat storage/logs/laravel.log | tail -n 100
```
