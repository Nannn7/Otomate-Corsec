# Template Kit


Aplikasi ini adalah template dashboard yang dibangun dengan [Laravel 12, TailwindCss 3, VanilaJS.]. Ini menyediakan dasar yang kokoh untuk memulai proyek dashboard Anda sendiri.

## Persyaratan

Sebelum Anda memulai, pastikan Anda telah menginstal perangkat lunak berikut di sistem Anda:

* PHP 8.2
* Composer
* Node.js (versi yang direkomendasikan, misal: 21+)
* npm atau Yarn
* Git
* Database (misal: MySQL, PostgreSQL)

## Instalasi

Ikuti langkah-langkah di bawah ini untuk menginstal dan menjalankan proyek di lingkungan lokal Anda:

1.  **Kloning Repositori:**
    Buka terminal Anda dan jalankan perintah berikut untuk mengkloning repositori ini:

    ```bash
    git clone https://git.putrakuningan.com/daengdeni/template.git [nama_project]
    ```

2.  **Masuk ke Direktori Proyek:**
    Pindah ke direktori proyek yang baru dikloning:

    ```bash
    cd [nama_project]
    ```

3.  **Instal Dependensi PHP:**
    Instal semua dependensi PHP yang diperlukan menggunakan Composer:

    ```bash
    composer install && composer update
    ```

4.  **Konfigurasi Lingkungan:**
    Salin file `.env.example` ke `.env` dan sesuaikan pengaturan database Anda di dalamnya.

    ```bash
    cp .env.example .env
    ```
    Buka file `.env` dan atur koneksi database Anda (DB_CONNECTION, DB_HOST, DB_PORT, DB_DATABASE, DB_USERNAME, DB_PASSWORD).

5.  **Buat Kunci Aplikasi:**
    Buat kunci aplikasi untuk keamanan:

    ```bash
    php artisan key:generate
    ```

6.  **Tautkan Penyimpanan Publik:**
    Buat symlink dari penyimpanan publik ke direktori storage:

    ```bash
    php artisan storage:link
    ```

7.  **Migrasi Database dan Seed Awal:**
    Jalankan migrasi database dan masukkan data awal (seed) ke database Anda. Ini akan menghapus semua tabel yang ada jika sudah ada.

    ```bash
    php artisan migrate:fresh --seed
    ```

8.  **Instal Dependensi Node.js dan Build Aset:**
    Instal semua dependensi JavaScript/Node.js dan kompilasi aset frontend:

    ```bash
    npm install && npm update && npm run build
    ```
    *(Gunakan `yarn install && yarn upgrade && yarn run build` jika Anda menggunakan Yarn)*

## Seed Data Tambahan (Opsional)

Jika Anda perlu men-seed data tambahan untuk modul tertentu setelah seed awal, Anda dapat menggunakan perintah berikut:

* **Untuk modul lokasi:**

    ```bash
    php artisan module:seed location
    ```

* **Untuk data dasar (basicdata):**

    ```bash
    php artisan module:seed basicdata
    ```

* **Untuk manajemen pengguna (usermanagement):**

    ```bash
    php artisan module:seed usermanagement
    ```

## Menjalankan Aplikasi

Setelah semua langkah instalasi selesai, Anda dapat menjalankan aplikasi dengan:

```bash
php artisan serve