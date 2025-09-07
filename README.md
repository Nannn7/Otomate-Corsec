# 🚀 Laravel 12 Dashboard Template

Template dashboard modern yang dibangun dengan **Laravel 12**, **TailwindCSS 3**, dan **VanillaJS**. Dilengkapi dengan Docker untuk development yang mudah dan scalable.

## 📋 Daftar Isi
- [Persyaratan](#persyaratan)
- [Cara Install](#cara-install)
  - [🐳 Dengan Docker Compose (Recommended)](#-dengan-docker-compose-recommended)
  - [🔧 Manual Tanpa Docker](#-manual-tanpa-docker)
- [Menjalankan Aplikasi](#menjalankan-aplikasi)
- [Seed Data](#seed-data)
- [Troubleshooting](#troubleshooting)

## Persyaratan

### Dengan Docker (Recommended)
- **Docker** 24.0+
- **Docker Compose** 2.0+
- **Make** (optional, untuk perintah shortcut)

### Tanpa Docker
- **PHP** 8.3 atau lebih baru
- **Composer**
- **Node.js** 21+
- **npm** atau **Yarn**
- **Git**
- **Database** salah satu:
  - **MySQL** 8.0+ atau **MariaDB** 10.6+
  - **PostgreSQL** 15+
- **Redis** (optional, untuk cache & queue)

---
## Clone Repository
```bash
git clone http://10.0.7.60:83/daengdeni/template.git [NAMA_PROJECT]
cd [NAMA_PROJECT]
```

## 🐳 Cara Install dengan Docker Compose (Recommended)

### 1. Setup dengan Make (Recommended)
```bash
# Setup otomatis dengan MySQL (default)
make setup-dev

# Setup dengan PostgreSQL
make use-postgres
make setup-dev
```

### 2. Setup Manual dengan Docker Compose
```bash
# Copy environment file
cp .env.example .env

# Jalankan dengan MySQL
docker-compose --profile mysql up -d

# Atau dengan PostgreSQL
docker-compose --profile postgres up -d

# Jalankan migrasi
docker-compose --profile mysql exec app php artisan migrate:fresh --seed
```

### 3. Akses Aplikasi
- **Website**: http://localhost:8080
- **Telescope**: http://localhost:8080/telescope
- **Pulse**: http://localhost:8080/pulse

### Perintah Make yang Tersedia
```bash
make help           # Lihat semua perintah
make up            # Jalankan services
make down          # Stop services
make restart       # Restart services
make migrate       # Jalankan migrasi
make migrate-fresh # Fresh migrate + seed
make test          # Jalankan tests
make shell         # Masuk ke container app
make logs          # Lihat logs
make cache-clear   # Clear cache
```

---

## 🔧 Cara Install Manual (Tanpa Docker)

### 1. Install Dependencies
```bash
# Install PHP dependencies
composer install

# Install Node.js dependencies
npm install
# atau
yarn install
```

### 2. Environment Setup
```bash
# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate

# Create storage symlink
php artisan storage:link
```

### 3. Database Configuration
Edit file `.env` sesuai database yang digunakan:

**MySQL/MariaDB:**
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel
DB_USERNAME=root
DB_PASSWORD=
```

**PostgreSQL:**
```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=laravel
DB_USERNAME=postgres
DB_PASSWORD=
```

### 4. Database Setup
```bash
# Buat database terlebih dahulu (MySQL/PostgreSQL)
# Lalu jalankan migrasi dan seeding
php artisan migrate:fresh --seed

# Jika hanya migrasi tanpa seed
php artisan migrate
```

### 5. Build Assets
```bash
# Build assets
npm run build
# atau
yarn build
```

---

## 🚀 Menjalankan Aplikasi

### Dengan Docker
```bash
# MySQL (default)
make up

# PostgreSQL
make up DB_PROFILE=postgres

# Atau manual
# MySQL: docker-compose --profile mysql up -d
# PostgreSQL: docker-compose --profile postgres up -d
```

### Tanpa Docker
```bash
# Jalankan Laravel development server
php artisan serve

# Akses di browser: http://localhost:8000
```

---

## 🌱 Seed Data

### Seed Data Utama (Sudah termasuk di migrate:fresh --seed)
```bash
# Dengan Docker
make migrate-fresh

# Tanpa Docker
php artisan migrate:fresh --seed
```

### Seed Data Per Module (Opsional)
```bash
# Dengan Docker
make shell
# Di dalam container:
php artisan module:seed location
php artisan module:seed basicdata
php artisan module:seed usermanagement

# Tanpa Docker
php artisan module:seed location
php artisan module:seed basicdata
php artisan module:seed usermanagement

# Semua modules
php artisan module:seed
```

---

## 🧪 Testing

### Dengan Docker
```bash
make test
```

### Tanpa Docker
```bash
php artisan test
```

---

## 🔍 Monitoring & Debugging

### Dengan Docker
- **Laravel Telescope**: http://localhost:8080/telescope
- **Laravel Pulse**: http://localhost:8080/pulse
- **Logs**: `make logs`

### Tanpa Docker
- **Laravel Telescope**: http://localhost:8000/telescope
- **Laravel Pulse**: http://localhost:8000/pulse
- **Logs**: `storage/logs/laravel.log`

---

## 🐛 Troubleshooting

### Docker Issues
```bash
# Cek status services
make help

# Restart services
make restart

# Cek logs
make logs

# Clear cache
make cache-clear

# Permission issues (Linux/Mac)
sudo chown -R $USER:$USER .
docker-compose --profile mysql exec app chown -R www-data:www-data /var/www/storage
```

### Manual Issues
```bash
# Clear cache
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Permission issues
sudo chown -R $USER:$USER storage/
sudo chown -R $USER:$USER bootstrap/cache/
chmod -R 775 storage/
chmod -R 775 bootstrap/cache/
```

### Common Issues
1. **Port 8080 used**: Edit `docker-compose.yml` ubah port mapping
2. **Database connection failed**: Pastikan database service running
3. **Permission denied**: Jalankan command permission di atas
4. **Node modules error**: Delete `node_modules` dan `package-lock.json`, lalu `npm install`
5. **Composer error**: Delete `vendor` dan `composer.lock`, lalu `composer install`

---

## 📚 Dokumentasi Tambahan

- **[DOCKER_GUIDE.md](./docker-docker-guide.md)** - Panduan Docker lengkap
- **[DATABASE_GUIDE.md](./docker-database-guide.md)** - Panduan database MySQL/PostgreSQL
- **[Makefile](./Makefile)** - Shortcut commands untuk development

---

## 📄 Environment Variables

### Docker Environment (Auto-configured)
```env
# Database (sudah terkonfigurasi untuk Docker)
DB_CONNECTION=mysql
DB_HOST=db-mysql  # atau db-postgres
DB_PORT=3306      # atau 5432
DB_DATABASE=laravel
DB_USERNAME=laravel
DB_PASSWORD=laravel

# Redis
REDIS_HOST=redis
REDIS_PORT=6379

# App URL
APP_URL=http://localhost:8080
```

### Manual Environment
```env
# Sesuaikan dengan setup local Anda
DB_HOST=127.0.0.1
DB_PORT=3306  # atau 5432 untuk PostgreSQL
REDIS_HOST=127.0.0.1
REDIS_PORT=6379
APP_URL=http://localhost:8000
```

---

## 🤝 Contributing

1. Fork repository
2. Buat branch feature (`git checkout -b feature/AmazingFeature`)
3. Commit perubahan (`git commit -m 'Add some AmazingFeature'`)
4. Push ke branch (`git push origin feature/AmazingFeature`)
5. Buat Pull Request

---

**⭐ Jika project ini membantu, berikan star di repository!**
**📝 Catatan**: Project ini masih dalam tahap pengembangan, ada banyak fitur yang bisa diimprove.

## 📖 Additional Information

Untuk informasi dan dokumentasi lebih lengkap, silakan kunjungi [Halaman Wiki](https://git.putrakuningan.com/daengdeni/template/wiki) kami.
Detail fitur dan penggunaan dapat dilihat di masing-masing repository [Modules](https://git.putrakuningan.com/daengdeni/template/wiki/Modules).

