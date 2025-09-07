# Panduan Penggunaan Database

Proyek ini mendukung dua jenis database: **MySQL** dan **PostgreSQL**. Anda dapat memilih salah satu sesuai kebutuhan.

## Cara Menggunakan MySQL (Default)

MySQL adalah database default yang digunakan dalam proyek ini.

### 1. Jalankan Docker dengan Profile MySQL
```bash
docker-compose --profile mysql up -d
```

### 2. Pastikan Konfigurasi .env
Pastikan file `.env` Anda sudah mengatur konfigurasi MySQL:
```env
DB_CONNECTION=mysql
DB_HOST=db-mysql
DB_PORT=3306
DB_DATABASE=laravel
DB_USERNAME=laravel
DB_PASSWORD=laravel
```

## Cara Menggunakan PostgreSQL

### 1. Jalankan Docker dengan Profile PostgreSQL
```bash
docker-compose --profile postgres up -d
```

### 2. Update Konfigurasi .env
Ubah file `.env` Anda untuk menggunakan PostgreSQL:
```env
DB_CONNECTION=pgsql
DB_HOST=db-postgres
DB_PORT=5432
DB_DATABASE=laravel
DB_USERNAME=laravel
DB_PASSWORD=laravel
```

### 3. Install PostgreSQL Driver untuk Laravel
Jika belum terinstall, jalankan:
```bash
composer require pdo_pgsql
```

## Perintah Docker yang Berguna

### MySQL
```bash
# Start dengan MySQL
docker-compose --profile mysql up -d

# Stop
docker-compose --profile mysql down

# Lihat logs MySQL
docker-compose --profile mysql logs db-mysql
```

### PostgreSQL
```bash
# Start dengan PostgreSQL
docker-compose --profile postgres up -d

# Stop
docker-compose --profile postgres down

# Lihat logs PostgreSQL
docker-compose --profile postgres logs db-postgres
```

### Database Access

#### MySQL
- **Host**: localhost
- **Port**: 3307
- **Database**: laravel
- **Username**: laravel
- **Password**: laravel
- **Root Password**: root

#### PostgreSQL
- **Host**: localhost
- **Port**: 5432
- **Database**: laravel
- **Username**: laravel
- **Password**: laravel
- **Root Password**: root

## Migrasi Database

Setelah container berjalan, jalankan migrasi:
```bash
# Untuk MySQL
docker-compose --profile mysql exec app php artisan migrate

# Untuk PostgreSQL
docker-compose --profile postgres exec app php artisan migrate
```

## Switching Between Databases

Untuk berpindah dari MySQL ke PostgreSQL atau sebaliknya:

1. Stop container yang sedang berjalan
2. Hapus volume database jika ingin fresh start
3. Jalankan dengan profile yang berbeda
4. Update konfigurasi `.env`
5. Jalankan migrasi ulang

Contoh switching dari MySQL ke PostgreSQL:
```bash
# Stop MySQL
docker-compose --profile mysql down

# Hapus volume MySQL (optional)
docker volume rm whitelist_dbdata-mysql

# Start PostgreSQL
docker-compose --profile postgres up -d

# Update .env untuk PostgreSQL
# Jalankan migrasi
docker-compose --profile postgres exec app php artisan migrate
```