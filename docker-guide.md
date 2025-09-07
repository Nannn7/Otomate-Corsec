# 🐳 Docker Guide - Laravel 12 Project

Panduan lengkap untuk menjalankan project Laravel 12 ini menggunakan Docker Compose dengan dukungan database MySQL atau PostgreSQL.

## 📋 Prasyarat

Pastikan Anda telah menginstall:
- **Docker** (versi 24.0 atau lebih baru)
- **Docker Compose** (versi 2.0 atau lebih baru)
- **Git** (untuk clone repository)

### Cek Versi Docker
```bash
docker --version
docker-compose --version
```

## 🚀 Quick Start

### 1. Clone Repository
```bash
git clone [repository-url]
cd whitelist
```

### 2. Setup Environment
```bash
# Copy environment file
cp .env.example .env

# Generate application key
docker-compose --profile mysql run --rm app php artisan key:generate
```

### 3. Pilih Database & Jalankan

#### 🔵 MySQL (Recommended untuk starter)
```bash
# Jalankan dengan MySQL
docker-compose --profile mysql up -d

# Jalankan migrasi
docker-compose --profile mysql exec app php artisan migrate
```

#### 🟢 PostgreSQL
```bash
# Jalankan dengan PostgreSQL
docker-compose --profile postgres up -d

# Update .env untuk PostgreSQL
sed -i '' 's/DB_CONNECTION=mysql/DB_CONNECTION=pgsql/' .env
sed -i '' 's/DB_HOST=db-mysql/DB_HOST=db-postgres/' .env

# Jalankan migrasi
docker-compose --profile postgres exec app php artisan migrate
```

### 4. Akses Aplikasi
- **Website**: http://localhost:8080
- **Database MySQL**: localhost:3307
- **Database PostgreSQL**: localhost:5432
- **Redis**: localhost:6379

## 📁 Struktur Service Docker

| Service | Container Name | Port | Profile | Deskripsi |
|---------|----------------|------|---------|-----------|
| **app** | app | - | all | PHP 8.3 Laravel |
| **web** | web | 8080:80 | all | Nginx Web Server |
| **db-mysql** | db-mysql | 3307:3306 | mysql | MySQL 8.0 |
| **db-postgres** | db-postgres | 5432:5432 | postgres | PostgreSQL 15 |
| **redis** | redis | 6379:6379 | all | Redis Cache |
| **queue** | queue | - | worker | Laravel Queue Worker |
| **scheduler** | scheduler | - | worker | Laravel Scheduler |

## 🛠️ Perintah yang Sering Digunakan

### Manajemen Container
```bash
# Start services (pilih salah satu)
docker-compose --profile mysql up -d      # MySQL
docker-compose --profile postgres up -d   # PostgreSQL

# Stop all services
docker-compose --profile mysql down
docker-compose --profile postgres down

# Restart services
docker-compose --profile mysql restart

# Lihat status
docker-compose --profile mysql ps

# Lihat logs
docker-compose --profile mysql logs -f app
docker-compose --profile mysql logs -f db-mysql
```

### Database Operations
```bash
# MySQL - Jalankan migrasi
docker-compose --profile mysql exec app php artisan migrate

# PostgreSQL - Jalankan migrasi
docker-compose --profile postgres exec app php artisan migrate

# Fresh migrate + seed
docker-compose --profile mysql exec app php artisan migrate:fresh --seed

# MySQL - Backup database
docker-compose --profile mysql exec db-mysql mysqldump -u laravel -p laravel > backup.sql

# PostgreSQL - Backup database
docker-compose --profile postgres exec db-postgres pg_dump -U laravel laravel > backup.sql
```

### Development Commands
```bash
# Install composer dependencies
docker-compose --profile mysql run --rm app composer install

# Install NPM dependencies
docker-compose --profile mysql run --rm app npm install

# Build assets
docker-compose --profile mysql run --rm app npm run build

# Jalankan testing
docker-compose --profile mysql exec app php artisan test

# Jalankan tinker
docker-compose --profile mysql exec app php artisan tinker
```

## 🗄️ Database Access

### MySQL
```bash
# Connect ke MySQL container
docker-compose --profile mysql exec db-mysql mysql -u laravel -p
# Password: laravel

# Atau menggunakan mysql client lokal
mysql -h 127.0.0.1 -P 3307 -u laravel -p laravel
```

### PostgreSQL
```bash
# Connect ke PostgreSQL container
docker-compose --profile postgres exec db-postgres psql -U laravel -d laravel

# Atau menggunakan psql client lokal
psql -h 127.0.0.1 -p 5432 -U laravel -d laravel
```

## 🔄 Switching Database

### MySQL → PostgreSQL
```bash
# 1. Backup data MySQL (optional)
docker-compose --profile mysql exec db-mysql mysqldump -u laravel -p laravel > mysql-backup.sql

# 2. Stop MySQL services
docker-compose --profile mysql down

# 3. Update .env untuk PostgreSQL
sed -i '' 's/DB_CONNECTION=mysql/DB_CONNECTION=pgsql/' .env
sed -i '' 's/DB_HOST=db-mysql/DB_HOST=db-postgres/' .env

# 4. Start PostgreSQL services
docker-compose --profile postgres up -d

# 5. Jalankan migrasi
docker-compose --profile postgres exec app php artisan migrate
```

### PostgreSQL → MySQL
```bash
# 1. Stop PostgreSQL services
docker-compose --profile postgres down

# 2. Update .env untuk MySQL
sed -i '' 's/DB_CONNECTION=pgsql/DB_CONNECTION=mysql/' .env
sed -i '' 's/DB_HOST=db-postgres/DB_HOST=db-mysql/' .env

# 3. Start MySQL services
docker-compose --profile mysql up -d

# 4. Jalankan migrasi
docker-compose --profile mysql exec app php artisan migrate
```

## 🧹 Cleanup & Maintenance

### Hapus Semua Data
```bash
# Hapus containers, networks, dan volumes
docker-compose --profile mysql down -v
docker-compose --profile postgres down -v

# Hapus images
docker image prune -a
```

### Reset Database
```bash
# MySQL
docker-compose --profile mysql exec app php artisan migrate:fresh

# PostgreSQL
docker-compose --profile postgres exec app php artisan migrate:fresh
```

### Update Dependencies
```bash
# Update composer
docker-compose --profile mysql run --rm app composer update

# Update NPM packages
docker-compose --profile mysql run --rm app npm update
```

## 🐛 Troubleshooting

### Container tidak bisa start
```bash
# Lihat logs untuk debugging
docker-compose --profile mysql logs

# Cek port yang digunakan
netstat -tulpn | grep :8080

# Stop services yang mungkin konflik
docker-compose --profile mysql down
```

### Database connection refused
```bash
# Cek apakah database container sudah ready
docker-compose --profile mysql exec db-mysql mysqladmin ping

# Restart database service
docker-compose --profile mysql restart db-mysql
```

### Permission issues
```bash
# Fix permission di Linux/macOS
sudo chown -R $USER:$USER .
docker-compose --profile mysql exec app chown -R www-data:www-data /var/www/storage
```

## 📝 Environment Variables

### Database Configuration (.env)
```bash
# MySQL Configuration
DB_CONNECTION=mysql
DB_HOST=db-mysql
DB_PORT=3306
DB_DATABASE=laravel
DB_USERNAME=laravel
DB_PASSWORD=laravel

# PostgreSQL Configuration (uncomment untuk PostgreSQL)
# DB_CONNECTION=pgsql
# DB_HOST=db-postgres
# DB_PORT=5432
# DB_DATABASE=laravel
# DB_USERNAME=laravel
# DB_PASSWORD=laravel
```

## 🚀 Production Deployment

Untuk production, pastikan untuk:
1. Menggunakan `.env.production` yang berbeda
2. Menggunakan SSL certificates
3. Mengatur proper user permissions
4. Menggunakan external database service
5. Mengatur backup schedule

## 📞 Support

Jika mengalami masalah:
1. Cek logs: `docker-compose --profile mysql logs`
2. Pastikan semua services running: `docker-compose --profile mysql ps`
3. Cek dokumentasi: [DATABASE_GUIDE.md](./DATABASE_GUIDE.md)

---

**Selamat mencoba! 🎉**