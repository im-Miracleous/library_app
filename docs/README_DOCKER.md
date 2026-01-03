# Panduan Menjalankan Aplikasi dengan Docker

File-file konfigurasi Docker telah ditambahkan ke project Anda untuk memudahkan setup development environment yang konsisten.

## Prasyarat
Pastikan Anda (dan rekan kerja Anda) telah menginstall:
- **Docker Desktop** (untuk Windows/Mac) atau **Docker Engine** (untuk Linux).

## Cara Menjalankan

1.  **Buka Terminal** di direktori project ini.
2.  **Jalankan Perintah**:
    ```bash
    docker compose up -d
    ```
    Perintah ini akan mendownload image yang diperlukan, membuild aplikasi, dan menjalankan container di background.

3.  **Akses Aplikasi**:
    - Web: [http://localhost:8000](http://localhost:8000)
    - PhpMyAdmin: [http://localhost:8080](http://localhost:8080)

## Catatan Penting
- **Database Host**: Konfigurasi `docker-compose.yml` telah diset otomatis agar aplikasi menggunakan `db` sebagai host database saat berjalan di dalam container. Anda **TIDAK PERLU** mengubah file `.env` lokal Anda (yang mungkin berisi `DB_HOST=127.0.0.1` untuk Laragon).
- **Environment Variables**: Jika Anda ingin mengubah password atau nama database, pastikan file `.env` Anda sudah sesuai, atau ubah default values di `docker-compose.yml`.

## Optimasi Performa (PENTING!)
Jika aplikasi terasa lambat di Windows:
1.  **Gunakan WSL2**: Pastikan project ini disimpan di dalam file sistem Linux (WSL2), misalnya `\\wsl$\Ubuntu\home\user\project`. Jangan jalankan Docker dari mount Windows (`/mnt/c/...`) karena I/O disk-nya sangat lambat.
2.  **Opcache**: Saya telah mengaktifkan PHP Opcache di konfigurasi Docker. Ini akan mempercepat eksekusi PHP secara signifikan.
3.  **Docker Build**: Gunakan build yang sudah dioptimalkan (extension installer) untuk mempercepat proses instalasi ulang.

## Perintah Berguna Lainnya
- **Matikan Container**: `docker compose down`
- **Lihat Logs**: `docker compose logs -f`
- **Masuk ke Container App**: `docker compose exec app bash` (disini Anda bisa menjalankan `php artisan`, `composer`, dll).
