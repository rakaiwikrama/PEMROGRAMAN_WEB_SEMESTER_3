# Dokumentasi Jobsheet 13 — Deployment & Dokumentasi

Dokumentasi ini melanjutkan
[dokumentasi jobsheet-12](../../jobsheet-12/Dokumentasi/README.md)
(Integrasi Modul Peminjaman). Jobsheet-13 adalah **jobsheet penutup**
seluruh rangkaian SIMPUS-Mini — sesuai judulnya, "Deployment &
Dokumentasi": bukan menambah fitur baru, melainkan **menyiapkan**
aplikasi yang sudah selesai dibangun (Jobsheet 1-12) supaya siap
**dijalankan di tempat lain** (server produksi/hosting) dan **dipahami
orang lain** (dokumentasi proyek).

## Kabar Baik: Hampir Semua Kode Aplikasi Tidak Berubah

Diperiksa langsung: **satu-satunya** perbedaan kode dari jobsheet-12
ada di 3 file — `includes/koneksi.php`, file baru `includes/config.php`,
dan `includes/footer.php` (hanya label tahun). Seluruh logika CRUD
Buku/Anggota, autentikasi, CSRF, dan modul Peminjaman **identik
persis** dengan jobsheet-12 — sudah dibahas tuntas di
[dokumentasi jobsheet-07](../../jobsheet-07/Dokumentasi/README.md)
sampai
[dokumentasi jobsheet-12](../../jobsheet-12/Dokumentasi/README.md).

## Apa yang Baru di Jobsheet 13?

Sesuai [README.md](../README.md) jobsheet ini:

1. **`includes/config.php`** — file baru yang memisahkan **kredensial**
   database dari kode sumber, membaca dari *environment variable*
   dengan nilai cadangan (fallback) untuk pengembangan lokal.
2. **`includes/koneksi.php`** diubah untuk membaca konfigurasi dari
   `config.php`, bukan lagi menuliskan kredensial langsung di kodenya.
3. **`docs/manual-pengguna.md`** — panduan penggunaan aplikasi untuk
   pengguna akhir (bukan dokumentasi kode untuk developer).
4. **`README.md`** (di root proyek) menjadi **snapshot akhir** proyek:
   ERD final, matriks fitur per role, instruksi instalasi lengkap.

## Daftar Isi

1. [Konsep Dasar: Deployment & Pemisahan Konfigurasi](01-konsep-dasar-deployment-config.md)
2. [`includes/config.php` & Environment Variable](02-config-dan-koneksi.md)
3. [Dokumentasi Proyek: README, ERD, & Manual Pengguna](03-dokumentasi-proyek-final.md)
4. [Rangkuman & Latihan Lanjutan](04-rangkuman-latihan.md)

## Struktur Folder

```
jobsheet-13/
├── index.php, buku/, anggota/, auth/, peminjaman/   # Identik dengan jobsheet-12
├── includes/
│   ├── config.php                # BARU — kredensial dari environment variable
│   ├── koneksi.php                # Diubah — membaca config.php
│   └── ...                         # header.php, footer.php, auth.php, csrf.php, helpers.php — tidak berubah (footer hanya label tahun)
├── sql/                             # Identik dengan jobsheet-12
├── docs/
│   ├── wireframe.md                 # Identik dengan jobsheet-12 (asal dari Jobsheet 4)
│   ├── security-checklist.md        # Identik dengan jobsheet-12 (asal dari Jobsheet 11)
│   └── manual-pengguna.md           # BARU — panduan pengguna akhir
├── README.md                         # Ditulis ulang sebagai snapshot akhir proyek
└── Dokumentasi/                      # Folder dokumentasi ini
```

**Catatan penting** dari [README.md](../README.md) jobsheet ini:
seluruh berkas PHP sudah lolos uji `php -l` (tanpa error sintaks), tapi
koneksi database **belum sempat diuji langsung** di lingkungan
penyusunan dokumen ini (tidak ada instance PostgreSQL aktif) — perlu
diuji ulang di lab sebelum demo UAS.
