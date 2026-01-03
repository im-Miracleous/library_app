# Panduan Migrasi ke WSL 2 (Untuk Performa Maksimal)

Karena Anda menggunakan Windows, menjalankan Docker files langsung dari drive `C:` (Windows Filesystem) memiliki performa I/O yang lambat.
Solusinya adalah memindahkan kode project ke dalam **Linux Filesystem (WSL 2)**.

## Langkah-langkah Migrasi

### 1. Buka Terminal Ubuntu
Buka aplikasi **Ubuntu** dari Start Menu, atau jalankan perintah ini di PowerShell Anda:
```powershell
wsl
```
Anda akan masuk ke terminal Linux (tanda `$` atau `#`).

### 2. Pindahkan Project
Di dalam terminal Ubuntu, jalankan perintah berikut untuk menyalin folder project dari Windows ke Home Directory Linux Anda:

```bash
# 1. Pastikan rsync terinstall
sudo apt update && sudo apt install -y rsync

# 2. Salin project (Tanpa node_modules & vendor agar cepat)
# Perintah ini akan menyalin folder 'library_app' ke home directory
rsync -av --progress --exclude='node_modules' --exclude='vendor' /mnt/c/laragon/www/library_app ~/

# 3. Masuk ke folder baru
cd ~/library_app

# 4. Jalankan Docker
docker compose up -d

# 5. Install Dependencies (PENTING!)
# Karena folder vendor/node_modules tidak ikut dicopy, kita harus install ulang:
docker compose exec app composer install
docker compose exec app npm install
docker compose exec app npm run build
```
*Docker akan membuild ulang container, tapi kali ini file-file berada di filesystem Linux yang "native", sehingga kecepatannya akan meningkat drastis.*

### 4. Mengedit Code (VS Code)
Untuk mengedit kodingan yang ada di dalam WSL:
1.  Pastikan Anda berada di folder project di terminal Ubuntu: `cd ~/library_app`
2.  Ketik perintah:
    ```bash
    code .
    ```
    *VS Code akan terbuka dalam mode "WSL: Ubuntu".*

---

## Catatan
-   IP Address aplikasi tetap sama: `localhost:8000`.

### Akses dari Browser Windows
Meskipun aplikasi berjalan di dalam Linux (WSL), Anda **tetap bisa** mengaksesnya dari Browser Windows (Chrome/Edge) menggunakan alamat **`http://localhost:8000`**.
WSL 2 secara otomatis memforward port tersebut ke Windows.

-   Anda tidak perlu lagi menjalankan Laragon/Apache di Windows.
-   Jangan lupa copy file `.env` jika belum ikut tersalin (biasanya ikut jika menggunakan `cp -r`).
