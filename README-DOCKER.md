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

### 2. Membangun dan Menjalankan Container

```bash
# Membangun dan menjalankan container
docker-compose up -d --build

# Cek status container
docker-compose ps
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
