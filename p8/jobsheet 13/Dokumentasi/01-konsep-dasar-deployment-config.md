# 1. Konsep Dasar: Deployment & Pemisahan Konfigurasi

## 1.1 Apa itu Deployment?

**Deployment** adalah proses memindahkan/menjalankan aplikasi yang
sudah selesai dikembangkan di komputermu sendiri (disebut *lingkungan
pengembangan/development*) ke tempat lain — server produksi, hosting,
atau komputer lab untuk demo — yang disebut *lingkungan produksi
(production)*. Sepanjang jobsheet-01 sampai jobsheet-12, kamu selalu
menjalankan SIMPUS-Mini di komputer sendiri lewat `php -S localhost:8000`.
Jobsheet ini mempersiapkan aplikasi supaya bisa dijalankan di **tempat
lain** yang mungkin punya konfigurasi berbeda (nama database, kredensial
login PostgreSQL yang berbeda, dst.).

## 1.2 Masalahnya: Kredensial yang "Menempel" di Kode

Ingat `includes/koneksi.php` sejak
[dokumentasi jobsheet-08 §4.2](../../jobsheet-08/Dokumentasi/04-koneksi-pdo.md#42-lima-variabel-konfigurasi):

```php
$host = "localhost";
$port = "5432";
$db   = "simpus_mini";
$user = "postgres";
$pass = "postgres";
```

Nilai-nilai ini ditulis **langsung** di dalam kode PHP. Ini bekerja
baik untuk belajar di komputer sendiri, tapi punya 2 masalah nyata
begitu aplikasi perlu **dipindahkan** ke tempat lain:

1. **Kredensial produksi berbeda dengan lokal** — server sungguhan
   biasanya punya username/password PostgreSQL yang **berbeda** dari
   `postgres`/`postgres` yang dipakai untuk belajar. Kalau kredensial
   ditulis langsung di kode, kamu harus **mengedit file kode sumber**
   setiap kali berpindah lingkungan — merepotkan dan rawan salah edit.
2. **Risiko keamanan kalau kode di-commit ke repository publik** — kalau
   kredensial produksi (password database sungguhan) ditulis langsung
   di kode, dan kode itu di-commit ke Git lalu di-push ke repository
   publik (seperti GitHub), **siapa pun** yang bisa melihat repository
   itu akan tahu password database produksi milikmu.

## 1.3 Solusi: Environment Variable

**Environment variable** (variabel lingkungan) adalah nilai yang
disimpan **di luar** kode sumber — di tingkat sistem operasi/server
tempat aplikasi dijalankan, bukan di dalam file `.php` mana pun.
Setiap server (lokal, produksi, hosting) bisa punya nilai environment
variable yang **berbeda-beda**, tanpa perlu mengubah satu baris kode
pun. Detail cara PHP membaca environment variable (`getenv()`)
dibahas di [bab 2](02-config-dan-koneksi.md).

## 1.4 Kenapa Solusinya Bukan Sekadar "Hapus Nilai Defaultnya"?

Perhatikan `includes/config.php` **tetap** menyediakan nilai cadangan
(`'localhost'`, `'simpus_mini'`, `'postgres'`, dst. — dibahas detail di
[bab 2 §2.3](02-config-dan-koneksi.md#23-getenvdb_host--localhost)) —
bukan **mengharuskan** environment variable selalu diisi. Ini keputusan
desain yang disengaja: untuk **belajar/pengembangan lokal**, nilai
cadangan yang sama seperti sejak jobsheet-08 tetap membuat aplikasi
langsung bisa dijalankan tanpa konfigurasi tambahan apa pun — hanya
saat **benar-benar di-deploy ke produksi** (dengan kredensial
sungguhan yang berbeda), environment variable perlu diisi eksplisit
untuk **menimpa** nilai cadangan tersebut.

## 1.5 Kenapa Ini Dipisah ke File Baru (`config.php`), Bukan Ditambah Langsung di `koneksi.php`?

Ingat prinsip pemisahan tanggung jawab (*separation of concerns*) yang
sudah dibahas sejak
[dokumentasi jobsheet-02 §2.2](../../jobsheet-02/Dokumentasi/02-perubahan-file-html.md#22-kenapa-struktur-html-sengaja-tidak-diubah)
(HTML vs CSS) dan
[dokumentasi jobsheet-06 §2.4](../../jobsheet-06/Dokumentasi/02-perubahan-file-html.md#24-kenapa-bukujs-dan-anggotajs-dipisah-bukan-digabung-ke-appjs)
(pemisahan file JS per halaman). Prinsip yang sama berlaku di sini:
`config.php` **hanya** bertanggung jawab menentukan **nilai apa** yang
dipakai (dari environment atau cadangan), sementara `koneksi.php`
**hanya** bertanggung jawab **menggunakan** nilai itu untuk membuat
koneksi PDO. Pemisahan ini membuat kedua tanggung jawab lebih mudah
dipahami terpisah, dan `config.php` bisa **dipakai ulang** kalau
suatu saat ada bagian lain aplikasi yang juga butuh nilai konfigurasi
yang sama.

Lanjut ke: [`includes/config.php` & Environment Variable](02-config-dan-koneksi.md)
