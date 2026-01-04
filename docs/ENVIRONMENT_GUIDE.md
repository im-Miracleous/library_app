# 🔀 Development vs Production Environment - Panduan Lengkap

## 📊 Apakah Bisa Jalan Bersamaan?

**Jawaban: YA, tapi TIDAK DISARANKAN!**

Kedua environment (Development dan Production) **secara teknis bisa** berjalan bersamaan, tetapi akan menyebabkan **konflik port** dan **resource issues**.

---

## ⚠️ Masalah Jika Jalan Bersamaan

### 1. **Konflik Port Database**
Kedua environment mencoba bind ke port yang sama:

| Service | Dev Port | Prod Port | Konflik? |
|---------|----------|-----------|----------|
| MySQL | `3306` | Internal | ❌ Tidak (prod tidak expose) |
| App | `8000` | `80` | ✅ Aman (port berbeda) |
| phpMyAdmin | `8080` | - | ✅ Aman (prod tidak ada) |

**Catatan:** Jika Anda ubah `compose.prod.yaml` untuk expose database port, akan konflik!

### 2. **Resource Usage**
- 2x container PHP-FPM
- 2x container Nginx
- 2x container Database (jika keduanya expose port)
- 2x memory & CPU usage

### 3. **Confusion**
- Susah membedakan mana yang mana
- Bisa salah akses environment

---

## ✅ **REKOMENDASI: Jalankan Satu Environment Saja**

### Development (Untuk Coding)
```powershell
# Start development
.\docker.ps1 dev up

# Akses
# App: http://localhost:8000
# phpMyAdmin: http://localhost:8080
```

### Production (Untuk Testing Deploy)
```powershell
# Stop development dulu
.\docker.ps1 dev down

# Start production
.\docker.ps1 prod up

# Akses
# App: http://localhost
```

---

## 🔍 Cara Membedakan Environment

### 1. **Cek dengan Command Status**
```powershell
.\docker.ps1 status
```

**Output:**
```
📊 Checking running environments...

✅ DEVELOPMENT environment is RUNNING
   Access: http://localhost:8000
   phpMyAdmin: http://localhost:8080

NAMES                   STATUS              PORTS
library_web_dev         Up 5 minutes        0.0.0.0:8000->80/tcp
library_app_dev         Up 5 minutes        9000/tcp
library_db_dev          Up 5 minutes        0.0.0.0:3306->3306/tcp

⭕ Production environment is NOT running

💡 Tip: Kedua environment bisa jalan bersamaan, tapi akan konflik port!
   Sebaiknya hanya jalankan satu environment pada satu waktu.
```

### 2. **Cek Nama Container**
```powershell
docker ps
```

**Development containers** berakhiran `_dev`:
- `library_app_dev`
- `library_web_dev`
- `library_db_dev`
- `library_pma_dev`
- `library_workspace_dev`

**Production containers** berakhiran `_prod`:
- `library_app_prod`
- `library_web_prod`
- `library_db_prod`

### 3. **Cek Port yang Digunakan**

| Environment | App URL | phpMyAdmin | Database |
|-------------|---------|------------|----------|
| **Development** | http://localhost:8000 | http://localhost:8080 | localhost:3306 |
| **Production** | http://localhost | ❌ Tidak ada | Internal only |

### 4. **Cek di Browser**

**Development:**
- Buka http://localhost:8000
- Debug mode enabled (error messages detail)
- Laravel debug bar (jika installed)

**Production:**
- Buka http://localhost
- Debug mode disabled (error generic)
- No debug bar

---

## 🎯 Best Practices

### Workflow yang Disarankan

1. **Development (Sehari-hari)**
   ```powershell
   # Start dev
   .\docker.ps1 dev up
   
   # Coding...
   # Test di http://localhost:8000
   
   # Stop saat selesai
   .\docker.ps1 dev down
   ```

2. **Production Testing (Sebelum Deploy)**
   ```powershell
   # Pastikan dev sudah stop
   .\docker.ps1 dev down
   
   # Start prod
   .\docker.ps1 prod up
   
   # Test di http://localhost
   # Pastikan performa OK
   
   # Stop
   .\docker.ps1 prod down
   ```

3. **Switch Environment**
   ```powershell
   # Dari dev ke prod
   .\docker.ps1 dev down
   .\docker.ps1 prod up
   
   # Dari prod ke dev
   .\docker.ps1 prod down
   .\docker.ps1 dev up
   ```

---

## 🔧 Jika Tetap Ingin Jalan Bersamaan

Jika Anda **benar-benar perlu** menjalankan keduanya bersamaan, ubah port production:

### Edit `compose.prod.yaml`

```yaml
services:
  web:
    ports:
      - "8001:80"  # Ubah dari 80 ke 8001
```

**Akses:**
- Development: http://localhost:8000
- Production: http://localhost:8001

**Tapi tetap tidak disarankan karena:**
- Resource usage tinggi
- Bisa membingungkan
- Database tetap shared (jika expose port yang sama)

---

## 📋 Quick Reference

### Cek Status
```powershell
.\docker.ps1 status
```

### Start Development
```powershell
.\docker.ps1 dev up
```

### Start Production
```powershell
.\docker.ps1 prod up
```

### Stop Development
```powershell
.\docker.ps1 dev down
```

### Stop Production
```powershell
.\docker.ps1 prod down
```

### Stop Semua Container
```powershell
docker stop $(docker ps -q)
```

### Lihat Semua Container (Running & Stopped)
```powershell
docker ps -a
```

---

## 💡 Tips

1. **Gunakan `status` command** sebelum start environment baru
2. **Stop environment yang tidak dipakai** untuk hemat resource
3. **Development untuk coding**, Production untuk testing deploy
4. **Jangan edit database production** saat development running (jika share port)
5. **Commit code** sebelum switch environment

---

## ❓ FAQ

**Q: Kenapa production tidak ada phpMyAdmin?**  
A: Production environment minimal, hanya service yang diperlukan untuk production.

**Q: Apakah database terpisah?**  
A: Ya, masing-masing environment punya volume database sendiri (`dbdata_dev` dan `dbdata_prod`).

**Q: Bisa share database antara dev dan prod?**  
A: Bisa, tapi **sangat tidak disarankan**. Bisa corrupt data.

**Q: Bagaimana cara backup database?**  
A: Gunakan command:
```powershell
# Development
.\docker.ps1 dev artisan db:backup

# Production
.\docker.ps1 prod artisan db:backup
```

---

**Kesimpulan:** Sebaiknya **hanya jalankan satu environment** pada satu waktu untuk menghindari konflik dan confusion! 🎯
