<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>
<br/>

# 📚 Library App - Sistem Manajemen Perpustakaan Digital

Aplikasi manajemen perpustakaan berbasis web menggunakan **Laravel 12** dengan **Docker** untuk environment yang konsisten dan mudah di-deploy.

---

## 🚀 Quick Start dengan Docker (Recommended)

### Prasyarat
- **Docker Desktop** ([Download](https://www.docker.com/products/docker-desktop))
- **Git** ([Download](https://git-scm.com/downloads))

### Instalasi

1. **Clone Repository**
   ```bash
   git clone [URL_REPOSITORY_ANDA]
   cd library_app
   ```

2. **Setup Environment**
   ```bash
   # Copy file .env.example ke .env
   cp .env.example .env
   ```

3. **Start Development Environment**
   
   **Windows (PowerShell):**
   ```powershell
   .\docker.ps1 dev up
   ```
   
   **Linux/Mac/WSL:**
   > **Penting:** Pastikan file script memiliki izin eksekusi.
   ```bash
   # Beri izin eksekusi (Hanya perlu sekali di awal)
   chmod +x docker.sh
   chmod +x docker/development/php-fpm/entrypoint.sh
   
   # Jalankan environment
   ./docker.sh dev up
   ```

4. **Install & Run Frontend Assets**
   Agar tampilan web normal (tidak berantakan), Anda harus menginstal dependensi frontend.
   
   **Windows:**
   ```powershell
   .\docker.ps1 dev npm install
   .\docker.ps1 dev npm run dev
   ```
   
   **Linux/Mac/WSL:**
   ```bash
   ./docker.sh dev npm install
   ./docker.sh dev npm run dev
   ```

5. **Akses Aplikasi**
   - **Aplikasi**: http://localhost:8000
   - **phpMyAdmin**: http://localhost:8080

### 🔑 Contoh Akun Login Dummy

- **Admin**: `super@admin.library.com`
- **Petugas**: `budi@petugas.library.com`
- **Anggota**: *(Dibuat acak saat seeding database)*
- **Password**: `password123`

---

## 🐳 Alur Kerja & Environment (Workflow)

Project ini menggunakan pendekatan **Dual-Environment** dengan Docker:

### 1. Development Environment (`dev`) 🛠️
Digunakan untuk **sehari-hari saat coding**.
- **Fitur**: Hot Reload (Vite), Debug Mode On, phpMyAdmin.
- **URL**: `http://localhost:8000`
- **Cara Pakai**:
  ```powershell
  # Start
  .\docker.ps1 dev up
  
  # Coding Frontend (Hot Reload) - Biarkan terminal terbuka
  .\docker.ps1 dev npm run dev
  ```

### 2. Production Simulation (`prod`) 🚀
Digunakan untuk **mengetes hasil akhir** sebelum deploy ke server asli.
- **Fitur**: Performa tinggi (Opcache/JIT), Security Headers, Caching Agresif, Gzip.
- **URL**: `http://localhost` (Port 80)
- **Cara Pakai**:
  ```powershell
  # Pastikan dev dimatikan dulu agar port tidak bentrok
  .\docker.ps1 dev down
  
  # Start Production
  .\docker.ps1 prod up
  
  # Reset Total (Jika ada error/cache membandel)
  .\docker.ps1 prod fresh
  ```

---

## 🛠️ Command Helper (Cheatsheet)

Script `docker.ps1` (Windows) dan `docker.sh` (Linux/Mac) adalah teman terbaik Anda.

### 🔍 Cek Status
```powershell
.\docker.ps1 status
# Menampilkan environment mana yang aktif dan port-nya.
```

### 🔍 Bantuan (Help)
Jika Anda lupa command apa saja yang tersedia, gunakan:
```powershell
.\docker.ps1 help
# atau
.\docker.ps1 list
```

### 🧹 Maintenance & Reset (Penting!)
Script ini dilengkapi fitur maintenance canggih:

```powershell
# 1. Prune System (Hapus Image Sampah)
# Membersihkan stopped containers, unused networks, dan unused images.
# GUNAKAN DENGAN HATI-HATI!
.\docker.ps1 prune

# 2. Reset Development (Soft Reset)
# Hapus container & volume dev, lalu build ulang & seed database.
.\docker.ps1 dev fresh

# 3. Reset Production (Soft Reset)
# Rebuild image prod + clear cache + fresh DB.
.\docker.ps1 prod fresh

# 4. NUCLEAR RESET (Hard Reset Project) 🧨
# Menghapus SEMUA container, image, dan volume KHUSUS project ini.
# Database akan hilang total. Gunakan jika ingin start benar-benar dar 0.
.\docker.ps1 reset
```

### 🔨 Manual Build
Gunakan perintah ini jika Anda melakukan perubahan pada `Dockerfile` atau konfigurasi image:
```powershell
# Rebuild image Development
.\docker.ps1 dev build

# Rebuild image Production
.\docker.ps1 prod build
```

### 🎨 Frontend
```powershell
# Build untuk Production (Wajib dijalankan jika ingin update asset di Prod)
.\docker.ps1 dev npm run build
```

### 🗄️ Database & Artisan
```powershell
# Masuk ke shell container app
.\docker.ps1 dev bash

# Management Database
.\docker.ps1 dev artisan migrate                   # Update struktur tabel
.\docker.ps1 dev artisan db:seed                   # Isi data dummy/awal
.\docker.ps1 dev artisan migrate:fresh --seed      # Reset Total (Hapus semua & ulang dari awal)

# Artisan command lainnya (jalankan di app container)
.\docker.ps1 dev artisan route:list
```

---

## 🔧 Troubleshooting (Masalah Umum)

### 1. Error "502 Bad Gateway" di Production
- **Penyebab**: Container `webapp` (Nginx) sudah jalan, tapi `app` (PHP-FPM) belum selesai startup atau crash.
- **Solusi**:
  1. Tunggu 10-30 detik (migrasi database di Windows agak lama).
  2. Cek logs: `.\docker.ps1 prod logs`.
  3. Jika stuck, gunakan jurus andalan: `.\docker.ps1 prod fresh`.

### 2. Aplikasi Terasa Lambat
- **Penyebab**: Docker di Windows menggunakan filesystem NTFS yang lambat untuk banyak file kecil (seperti `vendor/` dan `node_modules/`).
- **Solusi**:
  - Gunakan **WSL 2** (Ubuntu) untuk menyimpan project code.
  - Development environment sudah di-tuning dengan Opcache, tapi Production environment akan jauh lebih ngebut.

### 3. "View path not found" atau Error Cache
- Melakukan `.\docker.ps1 prod fresh` biasanya menyelesaikan masalah ini karena perintah tersebut akan membuat ulang struktur direktori cache yang dibutuhkan.

---

## � Instalasi Manual (Tanpa Docker)

Jika Anda lebih memilih menggunakan XAMPP atau Laragon:

### 1. Persiapan Software
Pastikan sudah terinstal:
1. **Git** - [Download](https://git-scm.com/downloads)
2. **Laragon** atau **XAMPP** - PHP 8.2+
3. **Composer** - [Download](https://getcomposer.org/download/)
4. **Node.js** (LTS) - [Download](https://nodejs.org/)

### 2. Clone Project
```bash
git clone [URL_REPOSITORY_ANDA]
cd library_app
```

### 3. Install Dependencies
```bash
# Backend
composer install

# Frontend
npm install
```

### 4. Setup Environment
```bash
# Copy .env
cp .env.example .env

# Generate key
php artisan key:generate

# Edit .env sesuaikan username/password database Anda
```

### 5. Setup Database
1. Buat database baru bernama `library_db` di phpMyAdmin/HeidiSQL.
2. Jalankan migrasi:
   ```bash
   php artisan migrate:fresh --seed
   ```

### 6. Jalankan Aplikasi
Buka 2 terminal berbeda:

**Terminal 1 (PHP Server):**
```bash
php artisan serve
```

**Terminal 2 (Vite/Tailwind):**
```bash
npm run dev
```

**Akses:** http://127.0.0.1:8000

---

## �📖 Dokumentasi Lengkap

- **[Peta Dokumentasi](docs/DOCUMENTATION_MAP.md)** - Mulai dari sini untuk melihat struktur folder
- **[Laporan Arsitektur](docs/ARCHITECTURE_REPORT.md)** - Detail teknis konfigurasi Production vs Development
- **[Panduan Environment](docs/ENVIRONMENT_GUIDE.md)** - Panduan lengkap development vs production dan troubleshooting

---

## 💡 Tips untuk Developer Baru

1. **Jangan jalankan `dev` dan `prod` bersamaan** kecuali Anda paham mapping port-nya, karena keduanya menggunakan resource database yang berbeda (terisolasi).
2. **Setup Awal**: Cukup jalankan `cp .env.example .env` lalu `.\docker.ps1 dev fresh`. Anda langsung siap coding!
3. **Login App**:
   - **Admin**: `admin@library.com` (Pass: `password123`)
   - **Petugas**: `budi@library.com` (Pass: `password123`)

---

**Dibuat dengan ❤️ menggunakan Laravel 12 & Docker**