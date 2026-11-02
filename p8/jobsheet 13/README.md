# Jobsheet 13 — Deployment & Dokumentasi (SIMPUS-Mini)

Sub-CPMK: Mendeploy dan mendokumentasikan aplikasi.

Ini adalah **snapshot akhir** proyek SIMPUS-Mini setelah 13 jobsheet (Jobsheet 1-12), siap didemokan pada UAS.

## Deskripsi Aplikasi

SIMPUS-Mini adalah aplikasi web sederhana untuk mengelola perpustakaan: data buku, anggota, serta transaksi peminjaman/pengembalian, dengan autentikasi petugas.

**Stack:** HTML5, CSS3, JavaScript, PHP native, PostgreSQL (PDO_PGSQL).

## ERD Final

```
buku            anggota           users              peminjaman
------          --------          ------             -----------
id (PK)         id (PK)           id (PK)             id (PK)
judul           nama              nama                buku_id (FK -> buku.id)
pengarang       no_anggota (UQ)   username (UQ)        anggota_id (FK -> anggota.id)
tahun           alamat            password (hash)      tanggal_pinjam
isbn            no_hp             role                 tanggal_kembali
stok                                                    status
kategori
```

## Fitur per Role

| Fitur | Tamu (tanpa login) | Petugas (login) |
|---|---|---|
| Lihat Beranda & statistik | Ya | Ya |
| Lihat Daftar Buku | Ya | Ya |
| Tambah/Edit/Hapus Buku | Tidak | Ya |
| Kelola Anggota (CRUD) | Tidak | Ya |
| Peminjaman Baru | Tidak | Ya |
| Pengembalian | Tidak | Ya |
| Riwayat Peminjaman | Tidak | Ya |

## Instalasi & Menjalankan

1. **Clone/salin folder ini** ke server (lokal atau hosting yang mendukung PHP + PostgreSQL).
2. **Buat database & impor skema** (urutan penting karena `peminjaman` mereferensikan `buku`/`anggota`):
   ```bash
   createdb simpus_mini
   psql -d simpus_mini -f sql/01_buku_anggota.sql
   psql -d simpus_mini -f sql/02_users.sql
   psql -d simpus_mini -f sql/03_peminjaman.sql
   ```
3. **Konfigurasi koneksi**: kredensial database dibaca dari `includes/config.php`, yang mengambil environment variable (`DB_HOST`, `DB_PORT`, `DB_NAME`, `DB_USER`, `DB_PASS`) dengan fallback ke nilai default lokal. Set environment variable sebelum menjalankan server bila kredensial produksi berbeda:
   ```bash
   DB_HOST=127.0.0.1 DB_NAME=simpus_mini DB_USER=produser DB_PASS=rahasia php -S localhost:8000
   ```
   Jangan mengubah nilai default di `includes/config.php` menjadi kredensial asli lalu meng-commit-nya ke repository publik.
4. **Jalankan** — path CSS/JS/link/redirect login dihitung relatif otomatis di `includes/header.php` & `includes/auth.php` berdasarkan kedalaman folder halaman, jadi tidak lagi terikat harus dijalankan dari root server:
   - **Opsi 1 — PHP built-in server**:
     ```bash
     php -S localhost:8000
     ```
   - **Opsi 2 — Laragon (Apache)**: lewat virtual host langsung ke folder `jobsheet-13/` (mis. `http://jobsheet13.test/`), atau bersarang di bawah domain proyek (mis. `http://dp2026.test/kode-praktikum/jobsheet-13/`) — dua-duanya jalan.
5. Buka `http://localhost:8000/index.php` (atau URL Laragon yang dipakai), registrasi akun petugas pertama lewat halaman `auth/register.php`.

## Struktur Folder

```
jobsheet-13/
├── index.php                  Beranda (statistik real-time dari DB)
├── includes/                  koneksi.php, config.php, header.php, footer.php, auth.php, csrf.php, helpers.php
├── assets/css, assets/js       styling & interaktivitas
├── buku/                       CRUD Buku
├── anggota/                    CRUD Anggota
├── auth/                       Register, Login, Logout
├── peminjaman/                 Peminjaman, Pengembalian, Riwayat
├── sql/                        skema database (01-03, jalankan berurutan)
└── docs/                       wireframe.md, security-checklist.md, manual-pengguna.md
```

## Dokumen Pendukung
- [`docs/wireframe.md`](docs/wireframe.md) — rancangan UX (Jobsheet 4)
- [`docs/security-checklist.md`](docs/security-checklist.md) — audit keamanan (Jobsheet 11)
- [`docs/manual-pengguna.md`](docs/manual-pengguna.md) — panduan penggunaan aplikasi
- [`../../Setup-Database-PostgreSQL-Laragon.md`](../../Setup-Database-PostgreSQL-Laragon.md) — cara menyiapkan PostgreSQL & database `simpus_mini` khusus di Laragon

## Catatan
- Seluruh berkas PHP telah dilolos-uji `php -l` (tanpa error sintaks). Sudah diverifikasi jalan end-to-end (Apache + PHP 8.3 + PostgreSQL 14.5 di Laragon), termasuk lewat virtual host langsung maupun bersarang di bawah domain proyek — lihat `Setup-Database-PostgreSQL-Laragon.md` di root repo untuk langkah setup database-nya.
- Untuk presentasi UAS, siapkan penjelasan alasan desain teknis: mengapa struktur tabel dan alur transaksi peminjaman dirancang seperti ini (lihat `README.md` Jobsheet 12 untuk detail transaksi stok).
