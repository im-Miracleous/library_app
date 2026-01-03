# 🏗️ System Architecture & Configuration Report

Dokumen ini menjelaskan arsitektur teknis dari environment Docker yang digunakan dalam proyek Library App, termasuk alasan pemilihan teknologi dan konfigurasi performa yang diterapkan.

---

## 🎯 Keputusan Arsitektur (Architectural Decisions)

### 1. Dual Environment Strategy
Kami memisahkan environment menjadi **Development** dan **Production** secara fisik (file config terpisah) untuk mencapai tujuan yang berbeda:

| Feature | Development (`compose.dev.yaml`) | Production (`compose.prod.yaml`) |
|---------|--------------------------------|--------------------------------|
| **Prioritas** | Kecepatan Coding (Developer Experience) | Kecepatan Eksekusi & Keamanan |
| **Volumes** | Bind Mounts (Live Edit) | Read-Only (Immutable Code) |
| **Caching** | Disabled / Relaxed (Immediate updates) | Aggressive (Opcache + JIT) |
| **Image** | PHP 8.4 + Dev Tools | PHP 8.4 Slim (No Dev Tools) |
| **Asset Build**| Hot Module Replacement (Vite HMR) | Pre-compiled Assets |

### 2. Container Roles
- **App Service (`library_app`)**:
  - **OS:** Debian Bookworm (Stable & Compatible).
  - **PHP:** 8.4 (FPM).
  - **Fungsi:** Menangani request dinamis dari Nginx & menjalankan Artisan commands.
  
- **Workspace Service (`library_workspace`)**:
  - **OS:** Alpine Linux (Lightweight).
  - **Stack:** Node.js 20 + PHP CLI 8.3/8.4 + Composer.
  - **Fungsi:** "Mesin Tukang" untuk menjalankan `npm install`, `composer install`, dan task cron. Tidak melayani traffic web.

- **Web Server (`library_web`)**:
  - **Software:** Nginx (Alpine).
  - **Fungsi:** Reverse Proxy untuk PHP-FPM dan menyajikan Static Asset (CSS/JS/Images) dengan efisien.

---

## ⚡ Performance Tuning (Optimasi)

Berikut adalah detail optimasi "under-the-hood" yang diterapkan untuk menangani limitasi performa (terutama I/O pada Windows Host).

### 1. PHP-FPM Configuration (`www.conf`)
- **Process Manager (`pm = dynamic`)**: 
  Kami beralih dari `ondemand` ke `dynamic` dengan `min_spare_servers` yang disiapkan. Ini mencegah overhead pembuatan proses baru setiap kali ada request request.
- **Listen Address**: Bind ke `0.0.0.0:9000` untuk memastikan koneksi stabil, bukan Unix Socket yang kadang problematik di filesystem cross-platform.

### 2. Opcache Strategy (`php.ini`)
Kami menggunakan konfigurasi Opcache khusus untuk Windows Development:

*   **`opcache.memory_consumption=128`**: RAM yang dialokasikan untuk menyimpan kode terkompilasi.
*   **`opcache.revalidate_freq=2`**: File dicek perubahannya setiap 2 detik. Ini kompromi terbaik antara "Real-time" dan "Tidak membebani disk".
*   **`opcache.fast_shutdown=0`**: Dimatikan untuk stabilitas (mencegah crash saat restart process).

### 3. Nginx Tuning (`nginx.conf`)
Untuk mencegah error `502 Bad Gateway` yang disebabkan oleh lambatnya respons PHP di Windows:

```nginx
fastcgi_read_timeout 1800;    # Timeout 30 menit (Nginx akan sabar menunggu PHP)
fastcgi_buffer_size 128k;     # Buffer besar untuk menampung response JSON panjang tanpa disk buffering
client_max_body_size 100M;    # Mengizinkan upload file besar
```

### 4. Vite HMR (Hot Module Replacement)
Pada mode development (`npm run dev`), server Vite dikonfigurasi untuk:
- Menggunakan polling (`usePolling: true`) agar perubahan file di Windows terdeteksi di Linux.
- Menggunakan port eksplisit `5173` agar tidak bentrok atau salah alamat.

---

## 🔒 Security Measures (Production Ready)

Meskipun saat ini di tahap Development, setup Production sudah disiapkan dengan standar keamanan:

1.  **Read-Only Filesystem**: Container aplikasi production di-mount sebagai Read-Only. Jika ada hacker yang masuk, mereka tidak bisa menanam backdoor atau memodifikasi kode PHP.
2.  **Security Headers**: Nginx secara otomatis menambahkan header seperti `X-Frame-Options` dan `X-XSS-Protection`.
3.  **Hidden Environment**: File `.env` tidak pernah disertakan dalam image build.

---

## 📄 Peta Konfigurasi (Configuration Map)

Jika Anda perlu mengubah setting, inilah lokasinya:

- **PHP Settings (Memory, Upload Limit)**: 
  - Dev: `docker/development/php-fpm/php.ini`
  - Prod: `docker/production/php-fpm/php.ini`
  
- **PHP Worker Pool (Max Children, Process)**:
  - Dev: `docker/development/php-fpm/www.conf`
  - Prod: Menggunakan default/auto-calculated.

- **Nginx Server Block**:
  - Dev: `docker/development/nginx/nginx.conf`
  - Prod: `docker/production/nginx/nginx.conf`

- **Database Credentials**:
  - Selalu ada di file `.env` root project.
