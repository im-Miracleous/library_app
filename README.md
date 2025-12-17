<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>
<br/>

# Panduan Instalasi Project Library App (Laravel 12 + Vite)

Halo semua! 👋 Berikut adalah panduan lengkap untuk menjalankan project ini di komputer kalian masing-masing. Ikuti langkah-langkah ini secara berurutan agar tidak terjadi error.

## 1. Persiapan Software (Prerequisites)

Pastikan komputer kalian sudah terinstal aplikasi berikut:

1.  **Git**: [Download di sini](https://www.google.com/search?q=https://git-scm.com/downloads "null") (Untuk download kodingan).
    
2.  **Laragon** (Rekomendasi) atau **XAMPP**: (Untuk Database MySQL dan PHP).
    
    -   _Pastikan PHP versi 8.2 ke atas._
        
3.  **Composer**: [Download di sini](https://getcomposer.org/download/ "null") (Untuk install library PHP/Laravel).
    
4.  **Node.js** (Versi LTS terbaru): [Download di sini](https://nodejs.org/ "null") (Wajib untuk menjalankan Tailwind CSS/Vite).
    

## 2. Mengambil Project (Clone)

1.  Buka folder `www` (jika pakai Laragon) atau `htdocs` (jika pakai XAMPP).
    
2.  Klik kanan di ruang kosong > Pilih **Git Bash Here** (atau buka Terminal).
    
3.  Ketik perintah ini (ganti URL dengan link repo Github kita):
    
    ```
    git clone [GANTI_INI_DENGAN_URL_GITHUB_ANDA]
    ```
    
4.  Masuk ke folder project:
    
    ```
    cd library_app
    ```
    

## 3. Instalasi Library (PENTING!)

Project ini butuh dua jenis "bumbu" (library) agar bisa jalan. Jalankan perintah ini di terminal **di dalam folder library_app**:

**A. Install Backend (PHP/Laravel):**

```
composer install
```

_(Tunggu sampai selesai. Ini akan mendownload folder `vendor`)_.

**B. Install Frontend (Tailwind/Vite):**

```
npm install
```

_(Tunggu sampai selesai. Ini akan mendownload folder `node_modules`)_.

## 4. Konfigurasi Environment (.env)

Laravel butuh file pengaturan rahasia bernama `.env`. Kalian harus membuatnya sendiri.

1.  Duplikat file contoh `.env.example` dan ubah namanya menjadi `.env`.
    
2.  Generate Kunci Rahasia Aplikasi: Ketik di terminal:
    
    ```
    php artisan key:generate
    ```
    
3.  Edit file `.env` (Buka pakai Notepad/VS Code): Sesuaikan konfigurasi database kalian.
    
    **Jika pakai XAMPP (MySQL versi lama/MariaDB):** Tambahkan baris ini di bawah password agar tidak error (Collation Mismatch):
    
    ```
    DB_COLLATION=utf8mb4_unicode_ci
    ```
    
    ⚠️ Setelah menyimpan perubahan pada file .env, wajib jalankan perintah ini di terminal agar konfigurasi terbaca:
    
    ```
    php artisan config:clear
    ```
    

## 5. Siapkan Database

1.  Buka aplikasi Database Client kalian (HeidiSQL di Laragon, atau phpMyAdmin di XAMPP). _(Pastikan MySQL/MariaDB sudah berjalan!)_
    
2.  Buat database baru dengan nama: **`library_db`**.
    
3.  Kembali ke terminal, jalankan perintah ini untuk membuat tabel otomatis & isi data dummy (Migration & Seeder):
    
    ```
    php artisan migrate:fresh --seed
    ```
    
    _(Jika muncul tulisan hijau "DONE", berarti sukses!)_.
    

## 6. Menjalankan Aplikasi (Run) 🚀

Kalian harus menjalankan **DUA TERMINAL** sekaligus.

**Terminal 1 (Untuk menjalankan Server PHP):**

```
php artisan serve
```

_(Biarkan terminal ini terbuka. Jangan diclose)_.

**Terminal 2 (Untuk menjalankan CSS/Tailwind):** Buka tab terminal baru, lalu ketik:

```
npm run dev
```

_(Wajib jalan terus agar tampilan website tidak hancur/putih)_.

## 7. Buka di Browser

Akses alamat ini di browser kalian: 👉 **http://127.0.0.1:8000**

**Akun Login (Data Dummy):**

-   **Admin:** `admin@library.com`
    
-   **Petugas:** `budi@library.com`
    
-   **Password:** `Library@2025!` (atau `password123` jika belum diubah).
    

## Troubleshooting (Jika Error)

-   **Error "Vite manifest not found":** Artinya kalian lupa menjalankan `npm run dev`.
    
-   **Error Database "Connection Refused":** Cek apakah Laragon/XAMPP (MySQL) sudah di-start?
    
-   **Tampilan Berantakan:** Pastikan terminal `npm run dev` sedang berjalan.