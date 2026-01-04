# 🗺️ Panduan Navigasi Dokumentasi & Project Structure

Dokumen ini berfungsi sebagai peta untuk memahami struktur folder proyek dan lokasi dokumentasi penting.

---

## 📂 Struktur Folder Proyek

Berikut adalah penjelasan mengenai folder-folder utama dalam repositori ini:

```bash
library_app/
├── app/                  # Logika Utama Aplikasi (Models, Controllers)
├── docker/               # Konfigurasi Docker (Jantung dari environment kita)
│   ├── common/           # Config yang dipakai Dev & Prod (misal: Dockerfile dasar)
│   ├── development/      # Config khusus Development (Tools, Debugging)
│   └── production/       # Config khusus Production (Performance, Security)
├── docs/                 # 📚 PUSAT DOKUMENTASI (Anda di sini)
├── public/               # File yang bisa diakses publik (CSS, JS hasil build)
├── resources/            # Source code Frontend (Blade, Raw CSS, JS)
├── routes/               # Definisi URL routing
├── storage/              # File upload, cache, dan logs aplikasi
└── tests/                # Testing automatis
```

---

## 📚 Indeks Dokumentasi (Docs Index)

Semua panduan teknis disimpan di folder `docs/`. Berikut fungsinya masing-masing:

| Nama File | Deskripsi & Kegunaan |
|-----------|----------------------|
| **[README.md](../README.md)** | **(Root)** Halaman depan repository. Berisi cara instalasi cepat (Quick Start) dan daftar perintah dasar. Mulai dari sini. |
| **[DOCKER.md](DOCKER.md)** | Panduan referensi lengkap tentang Docker. Penjelasan mendalam tentang setiap command dan cara kerja container. |
| **[ARCHITECTURE_REPORT.md](ARCHITECTURE_REPORT.md)** | **(Architecture Report)** Laporan teknis mengenai arsitektur sistem, keputusan desain, dan konfigurasi performa/tuning yang diterapkan. |
| **[ENVIRONMENT_GUIDE.md](ENVIRONMENT_GUIDE.md)** | Panduan khusus yang menjelaskan perbedaan Environment (Dev vs Prod) dan cara me-manage keduanya. |
| **[WSL_GUIDE.md](WSL_GUIDE.md)** | **(Khusus Windows)** Panduan wajib baca untuk pengguna Windows agar mendapatkan performa maksimal menggunakan WSL 2. |

---

## 🛠️ Cheat Sheet Perintah (Docker Helper)

Script `docker.ps1` (Windows) dan `docker.sh` (Linux/Mac) adalah alat utama Anda.

### Perintah Sehari-hari
| Tujuan | Perintah |
|--------|----------|
| Cek status, port, & URL | `.\docker.ps1 status` |
| Menyalakan aplikasi | `.\docker.ps1 dev up` |
| Mematikan aplikasi | `.\docker.ps1 dev down` |
| Coding Frontend (Live) | `.\docker.ps1 dev npm run dev` |
| Jalankan Migration | `.\docker.ps1 dev artisan migrate` |

### Troubleshooting
| Masalah | Solusi |
|---------|--------|
| **502 Bad Gateway** / Error Koneksi | Restart environment: `.\docker.ps1 dev restart` |
| **Permission Denied** (Linux) | Fix permission: `.\docker.ps1 dev fix-perms` (jika ada) |
| **Aplikasi Sangat Lambat** | Baca [WSL_GUIDE.md](WSL_GUIDE.md). Windows native filesystem memang lambat untuk Docker. |

---

## 💡 Tips untuk Developer Baru

1.  **Jangan edit file di dalam container!** Selalu edit file di folder proyek Windows/Host Anda. Perubahan akan otomatis tersinkronisasi.
2.  **Gunakan `workspace` container** untuk menjalankan perintah `npm` atau `composer` yang berat, agar tidak membebani container aplikasi utama.
3.  **Hati-hati dengan Production Mode.** Environment production (`prod`) menggunakan caching agresif. Perubahan kode TIDAK akan terlihat kecuali Anda rebuild image atau restart container. Gunakan `dev` untuk coding.
