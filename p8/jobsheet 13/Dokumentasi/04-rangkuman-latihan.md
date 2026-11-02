# 4. Rangkuman & Latihan Lanjutan

## 4.1 Rangkuman Keseluruhan Jobsheet 13

| Bagian | Konsep yang Dipelajari |
|---|---|
| [Konsep Dasar Deployment](01-konsep-dasar-deployment-config.md) | Apa itu deployment, kenapa kredensial harus dipisah dari kode |
| [`config.php` & Environment Variable](02-config-dan-koneksi.md) | `getenv()`, operator Elvis `?:`, `require` untuk mengambil nilai kembalian |
| [Dokumentasi Proyek Final](03-dokumentasi-proyek-final.md) | ERD, matriks fitur per role, manual pengguna vs dokumentasi developer |

## 4.2 Konsep Inti yang Perlu Diingat

1. **Kredensial produksi tidak boleh menempel di kode sumber** —
   environment variable memisahkan **konfigurasi** dari **kode**,
   memungkinkan kode yang sama dijalankan di lingkungan berbeda tanpa
   diedit
   ([bab 1](01-konsep-dasar-deployment-config.md)).
2. **Operator Elvis (`?:`) berbeda dari null coalescing (`??`)** —
   `?:` memeriksa nilai "falsy" (termasuk `false` yang dikembalikan
   `getenv()`), `??` hanya memeriksa `null`
   ([bab 2 §2.3](02-config-dan-koneksi.md#23-getenvdb_host--localhost)).
3. **`require`/`include` bisa dipakai untuk mengambil nilai
   kembalian**, tidak melulu untuk efek samping seperti mencetak HTML
   ([bab 2 §2.4](02-config-dan-koneksi.md#24-cara-memakainya-configphp--pdo-di-koneksiphp)).
4. **Dokumentasi punya banyak bentuk untuk pembaca berbeda** — README
   untuk gambaran teknis keseluruhan, manual pengguna untuk pemakai
   akhir, dokumentasi jobsheet (seperti yang kamu baca ini) untuk
   pembelajar yang ingin memahami kode
   ([bab 3 §3.5](03-dokumentasi-proyek-final.md#35-docsmanual-penggunamd-dokumentasi-untuk-pengguna-akhir-bukan-developer)).

## 4.3 Melihat Kembali: Perjalanan 13 Jobsheet

Ini titik yang baik untuk menengok kembali seberapa jauh SIMPUS-Mini
sudah berkembang, jobsheet demi jobsheet:

| Jobsheet | Yang Ditambahkan |
|---|---|
| [01](../../jobsheet-01/Dokumentasi/README.md) | HTML5 semantic — kerangka halaman statis |
| [02](../../jobsheet-02/Dokumentasi/README.md) | CSS3 — Flexbox, Grid, styling |
| [03](../../jobsheet-03/Dokumentasi/README.md) | Responsive design — media query, hamburger menu |
| [04](../../jobsheet-04/Dokumentasi/README.md) | Rancangan UI/UX — wireframe, user flow, aktor |
| [05](../../jobsheet-05/Dokumentasi/README.md) | JavaScript DOM & Event — interaktivitas di browser |
| [06](../../jobsheet-06/Dokumentasi/README.md) | Fetch API & JSON — data dinamis dari file |
| [07](../../jobsheet-07/Dokumentasi/README.md) | PHP dasar — server-side, `$_SESSION`, form handling |
| [08](../../jobsheet-08/Dokumentasi/README.md) | PostgreSQL — data yang benar-benar persisten |
| [09](../../jobsheet-09/Dokumentasi/README.md) | CRUD penuh — Update, Delete, pagination |
| [10](../../jobsheet-10/Dokumentasi/README.md) | Autentikasi — Login, Register, otorisasi |
| [11](../../jobsheet-11/Dokumentasi/README.md) | Keamanan — XSS, CSRF, session fixation |
| [12](../../jobsheet-12/Dokumentasi/README.md) | Integrasi Peminjaman — transaction, relasi tabel |
| **13** | **Deployment & dokumentasi — siap dipakai orang lain** |

Perhatikan pola besarnya: setiap jobsheet **membangun di atas**
fondasi yang sudah ada, jarang menulis ulang dari nol. Tag semantic
dari jobsheet-01 masih dipakai sampai jobsheet-13; pola `<label>` +
`<input>` yang dipelajari di jobsheet-01 tetap dipakai di form Login
jobsheet-10; validasi server-side dari jobsheet-07 masih relevan
persis sampai form Peminjaman jobsheet-12.

## 4.4 Cara Mencoba Sendiri

1. Ikuti seluruh langkah **Instalasi & Menjalankan** di
   [README.md](../README.md) jobsheet ini dari awal, seolah-olah ini
   pertama kalinya kamu melihat proyek ini.
2. Coba jalankan server **dengan** environment variable kustom (ingat
   [bab 2 §2.5](02-config-dan-koneksi.md#25-cara-mengatur-environment-variable-saat-deployment)),
   memakai nama database yang **berbeda** dari `simpus_mini` — buat
   database baru dengan nama lain, jalankan skema ke sana, lalu
   jalankan server dengan `DB_NAME=nama_lain php -S localhost:8000` —
   buktikan aplikasi tetap berfungsi tanpa mengedit kode sama sekali.
3. Buka [`docs/manual-pengguna.md`](../docs/manual-pengguna.md) dan
   ikuti setiap langkahnya **sebagai pengguna**, bukan sebagai
   developer — apakah instruksinya cukup jelas diikuti tanpa perlu
   membaca kode sama sekali?
4. Jalankan `php -l` (linter sintaks PHP) pada salah satu file, sesuai
   catatan di [README.md](../README.md) jobsheet ini:
   ```bash
   php -l buku/list.php
   ```
   Amati output "No syntax errors detected" — cara cepat memeriksa
   kesalahan penulisan kode PHP tanpa perlu benar-benar menjalankan
   aplikasinya.

## 4.5 Ide Latihan Tambahan (Opsional)

1. **Tambah `.env.example`** — buat file baru bernama `.env.example`
   berisi daftar nama environment variable yang dibutuhkan (`DB_HOST`,
   `DB_PORT`, dst.) **tanpa** nilai sungguhan, sebagai contoh/template
   untuk siapa pun yang men-deploy aplikasi ini — pola yang sangat
   umum dipakai di proyek sungguhan (cari tahu sendiri lewat referensi
   umum tentang file `.env` di proyek web).
2. **Lengkapi validasi bisnis yang masih tertunda** — ingat dari
   [dokumentasi jobsheet-12 §6.4](../../jobsheet-12/Dokumentasi/06-rangkuman-latihan.md#64-ide-latihan-tambahan-opsional),
   aturan "anggota terlambat >14 hari tidak boleh meminjam" masih
   belum diterapkan — coba selesaikan sebagai latihan akhir yang
   menggabungkan hampir semua konsep yang sudah kamu pelajari (query
   SQL, transaction, validasi, flash message).
3. **Sisipkan screenshot sungguhan** ke `manual-pengguna.md` — jalankan
   aplikasi di lab dengan PostgreSQL aktif, ambil tangkapan layar
   nyata di setiap langkah bertanda `[Screenshot]`, lengkapi dokumen
   itu sesuai catatannya sendiri.
4. **Tulis "lessons learned"-mu sendiri** — coba tulis 1 halaman
   ringkasan (terpisah dari dokumentasi ini) tentang konsep pemrograman
   web apa yang menurutmu paling menantang dari seluruh 13 jobsheet
   ini, dan kenapa — latihan reflektif yang bagus sebelum presentasi
   UAS.

Selamat — kamu sudah menyelesaikan seluruh dokumentasi perjalanan
SIMPUS-Mini, dari kerangka HTML statis di jobsheet-01 sampai aplikasi
web lengkap yang siap di-deploy di jobsheet-13. Kalau ada bagian dari
jobsheet mana pun yang masih terasa kurang jelas, dokumentasi tiap
jobsheet saling terhubung lewat tautan — jangan ragu menelusuri
kembali ke jobsheet sebelumnya untuk menyegarkan konsep dasarnya.
