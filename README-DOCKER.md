# Docker Setup untuk Aplikasi IndoserakoSeminar

## Prasyarat

- Docker dan Docker Compose terinstal di komputer Anda
- Git untuk clone repository (jika belum memiliki repository)

## Cara Menjalankan Aplikasi dengan Docker

### 1. Persiapan Awal

Jika Anda menggunakan Docker untuk pertama kali dengan aplikasi ini, ikuti langkah-langkah berikut:

```bash
# Salin file .env.docker menjadi .env (jika belum ada .env)
cp .env.docker .env
```

**PENTING:** Pastikan file `.env` sudah ada sebelum membangun image Docker. File ini diperlukan untuk proses build yang sukses.

### 2. Membangun dan Menjalankan Container

```bash
# Membangun dan menjalankan container
docker-compose up -d --build

# Cek status container
docker-compose ps
```

### Mengatasi Masalah Build

#### Masalah dengan .env tidak ditemukan

Jika terjadi error "Failed to open stream: No such file or directory" saat generate key:

```bash
# Pastikan file .env.docker tersedia di root project
ls -la .env.docker

# Salin manual file .env.docker menjadi .env
cp .env.docker .env

# Jalankan container dengan volume yang sudah diupdate
docker-compose down
docker-compose up -d --build
```

Atau jalankan perintah berikut untuk men-setup .env secara manual di dalam container:

```bash
# Masuk ke container
docker-compose exec app bash

# Salin .env.docker ke .env
cp .env.docker .env

# Generate key secara manual
php artisan key:generate --ansi

# Keluar dari container
exit
```

#### Masalah dengan Dependensi PHP

```bash
# Jika terjadi error composer karena versi PHP atau ekstensi
# Masuk ke container aplikasi
docker-compose exec app bash

# Update composer dengan mengabaikan persyaratan platform
composer update --ignore-platform-reqs --no-interaction

# Atau gunakan skrip yang telah didefinisikan
composer run-script docker-build

# Keluar dari container
exit

# Rebuild container
docker-compose up -d --build
```

### 3. Setup Aplikasi Laravel

```bash
# Masuk ke container aplikasi
docker-compose exec app bash

# Instal dependencies PHP (jika belum)
composer install

# Generate application key (jika belum)
php artisan key:generate

# Jalankan migrasi database
php artisan migrate

# Jalankan seeder (opsional)
php artisan db:seed

# Optimalkan
php artisan optimize

# Keluar dari container
exit
```

### 4. Akses Aplikasi

Setelah container berjalan, Anda dapat mengakses aplikasi di:

- Web: `http://localhost:8002`

### 5. Mengelola Container

```bash
# Menghentikan container
docker-compose stop

# Menjalankan kembali container
docker-compose start

# Menghentikan dan menghapus container
docker-compose down

# Menghentikan dan menghapus container beserta volume (akan menghapus database)
docker-compose down -v
```

### 6. Melihat Log

```bash
# Melihat log dari semua container
docker-compose logs

# Melihat log secara real-time
docker-compose logs -f

# Melihat log dari container tertentu
docker-compose logs app
docker-compose logs db
docker-compose logs webserver
```

## Struktur Docker

- **app**: Container PHP untuk menjalankan aplikasi Laravel
- **webserver**: Container Nginx untuk webserver
- **db**: Container PostgreSQL untuk database
- **redis**: Container Redis untuk caching

## Volume

- **postgres-data**: Menyimpan data PostgreSQL
- **redis-data**: Menyimpan data Redis

## Ports

- Web: 8002 (`http://localhost:8002`)
- PostgreSQL: 5432
- Redis: 6379

## Akses Remote Database PostgreSQL

Database PostgreSQL telah dikonfigurasi untuk dapat diakses dari jarak jauh dengan detail berikut:

- **Host**: Alamat IP server Anda
- **Port**: 5432
- **Database**: bogasaridata
- **Username**: postgres
- **Password**: db=4ever
- **Connection Type**: PostgreSQL

Untuk mengakses database dari luar container:

1. Menggunakan pgAdmin atau DBeaver:
   - Tambahkan koneksi baru dengan detail di atas
   - Pastikan port 5432 terbuka di firewall jika berjalan pada server

2. Menggunakan command line:

   ```bash
   psql -h <alamat_ip_server> -p 5432 -U postgres -d bogasaridata
   ```

3. Jika menjalankan di lingkungan produksi, disarankan untuk:
   - Membatasi akses ke port 5432 hanya dari IP yang dipercaya
   - Menggunakan SSL untuk enkripsi komunikasi
   - Melakukan backup database secara berkala
