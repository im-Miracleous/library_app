# 📚 Dokumentasi Library App

Selamat datang di dokumentasi lengkap Library App! Berikut adalah daftar dokumen yang tersedia:

---

## 🐳 Docker Documentation

### [DOCKER.md](DOCKER.md)
**Panduan Lengkap Docker Setup**

Berisi:
- Struktur direktori Docker
- Cara menggunakan development & production environment
- Command-command Docker Compose
- Troubleshooting Docker
- Tips optimasi performa Windows

**Baca ini jika:** Anda ingin memahami setup Docker secara mendalam atau mengalami masalah.

---

### [ARCHITECTURE_REPORT.md](ARCHITECTURE_REPORT.md)
**System Architecture & Performance Configuration**

Berisi:
- Keputusan arsitektur (Dev vs Prod strategy)
- Detail container tech stack (Debian/Alpine, PHP 8.4)
- Strategi performa (Opcache, JIT, Nginx buffering)
- Spesifikasi keamanan

**Baca ini jika:** Anda ingin memahami cara kerja "jeroan" sistem atau ingin melakukan tuning performa lebih lanjut.

---

### [WSL_GUIDE.md](WSL_GUIDE.md)
**Panduan WSL 2 untuk Windows**

Berisi:
- Cara install & setup WSL 2
- Integrasi dengan Docker Desktop
- Tips performa maksimal
- Troubleshooting WSL

**Baca ini jika:** Anda menggunakan Windows dan ingin performa Docker yang optimal (10-100x lebih cepat).

---

### [ENVIRONMENT_GUIDE.md](ENVIRONMENT_GUIDE.md)
**Panduan Development vs Production Environment**

Berisi:
- Apakah bisa jalan bersamaan?
- Cara membedakan environment
- Best practices workflow
- Tips & troubleshooting

**Baca ini jika:** Anda bingung membedakan development dan production, atau ingin tahu apakah bisa jalan bersamaan.

---

### [README_DOCKER.md](README_DOCKER.md)
**Docker Quick Reference**

Berisi:
- Quick start commands
- Common use cases
- Cheat sheet Docker commands

**Baca ini jika:** Anda sudah familiar dengan Docker dan hanya butuh referensi cepat.

---

## 📖 Cara Menggunakan Dokumentasi

1. **Pemula?** Mulai dari [README.md](../README.md) di root project
2. **Ingin setup Docker?** Baca [DOCKER.md](DOCKER.md)
3. **Pakai Windows?** Baca [WSL_GUIDE.md](WSL_GUIDE.md) untuk performa terbaik
4. **Troubleshooting?** Cek bagian Troubleshooting di [DOCKER.md](DOCKER.md)

---

## 🔗 Quick Links

- [Kembali ke README Utama](../README.md)
- [Docker Compose Dev](../compose.dev.yaml)
- [Docker Compose Prod](../compose.prod.yaml)
- [Helper Script (PowerShell)](../docker.ps1)
- [Helper Script (Bash)](../docker.sh)

---

**Catatan:** Semua file markdown project telah dipindahkan ke folder `docs/` untuk organisasi yang lebih baik, kecuali `README.md` yang tetap di root sebagai entry point utama.
