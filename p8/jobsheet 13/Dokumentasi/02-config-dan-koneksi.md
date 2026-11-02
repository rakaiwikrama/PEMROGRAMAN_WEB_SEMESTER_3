# 2. `includes/config.php` & Environment Variable

## 2.1 Kode Lengkap `includes/config.php`

```php
<?php
// Pemisahan kredensial dari kode sumber (includes/koneksi.php).
// Di lingkungan produksi/hosting, isi nilai-nilai ini lewat environment
// variable (getenv) yang diatur di server, JANGAN di-commit ke repository
// bila nilai default di bawah sudah diganti dengan kredensial asli.
return [
    'db_host' => getenv('DB_HOST') ?: 'localhost',
    'db_port' => getenv('DB_PORT') ?: '5432',
    'db_name' => getenv('DB_NAME') ?: 'simpus_mini',
    'db_user' => getenv('DB_USER') ?: 'postgres',
    'db_pass' => getenv('DB_PASS') ?: 'postgres',
];
```

## 2.2 File PHP yang Langsung `return` Sebuah Array

Perhatikan file ini **tidak berisi fungsi** apa pun (beda dari
`includes/helpers.php` sejak
[dokumentasi jobsheet-11 §2.3](../../jobsheet-11/Dokumentasi/02-xss-dan-fungsi-e.md#23-fungsi-e-solusinya) —
seluruh isinya hanyalah **satu pernyataan `return`** yang langsung
mengembalikan sebuah array asosiatif. Ini pola PHP yang umum untuk
file konfigurasi: file ini **tidak dimaksudkan** untuk dijalankan
sendiri atau di-`include`/`require` demi efek sampingnya (seperti
`header.php` yang mencetak HTML) — ia murni "dipanggil" untuk
**nilai kembaliannya**, dipakai dengan cara khusus yang dibahas di
[§2.4](#24-cara-memakainya-configphp--pdo-di-koneksiphp).

## 2.3 `getenv('DB_HOST') ?: 'localhost'`

- **`getenv('DB_HOST')`** — fungsi PHP bawaan yang membaca nilai
  environment variable bernama `DB_HOST` (ingat konsep environment
  variable dari
  [bab 1 §1.3](01-konsep-dasar-deployment-config.md#13-solusi-environment-variable)).
  Kalau environment variable ini **tidak diset** di server, `getenv()`
  mengembalikan `false`.
- **`?:`** — ini **bukan** operator null coalescing `??` yang sudah
  kamu kenal sejak
  [dokumentasi jobsheet-07 §1.6](../../jobsheet-07/Dokumentasi/01-konsep-dasar-php.md#16-operator--null-coalescing) —
  ini disebut **operator Elvis** (`?:`, dinamai karena bentuknya mirip
  gaya rambut Elvis Presley kalau dimiringkan!). Bedanya: `??` hanya
  memeriksa apakah nilai di kiri `null`/tidak-ada, sedangkan `?:`
  memeriksa apakah nilai di kiri **"falsy"** — yaitu `false`, `0`,
  string kosong `""`, atau `null`. Karena `getenv()` mengembalikan
  **`false`** (bukan `null`) saat environment variable tidak ada, `?:`
  yang cocok dipakai di sini, bukan `??`.
- Jadi `getenv('DB_HOST') ?: 'localhost'` berarti: **"pakai nilai
  environment variable `DB_HOST` kalau ada isinya; kalau tidak (kosong/
  `false`), pakai `'localhost'` sebagai cadangan."**

## 2.4 Cara Memakainya: `config.php` → PDO di `koneksi.php`

```php
// includes/koneksi.php
$config = require __DIR__ . '/config.php';

try {
    $pdo = new PDO(
        "pgsql:host={$config['db_host']};port={$config['db_port']};dbname={$config['db_name']}",
        $config['db_user'],
        $config['db_pass']
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Koneksi database gagal: " . $e->getMessage());
}
```

- **`$config = require __DIR__ . '/config.php';`** — ini pemakaian
  `require` yang **berbeda** dari semua contoh sebelumnya (ingat
  `require`/`include` dari
  [dokumentasi jobsheet-07 §2.2](../../jobsheet-07/Dokumentasi/02-includes-header-footer.md#22-memanggil-include)):
  sebelumnya, `require`/`include` selalu dipakai untuk **efek samping**
  (menyisipkan HTML dari `header.php`). Di sini, `require` dipakai
  untuk **mengambil nilai kembalian** — karena `config.php` berisi
  `return [...]` ([§2.2](#22-file-php-yang-langsung-return-sebuah-array)),
  hasil `require`-nya adalah **array itu sendiri**, langsung disimpan
  ke variabel `$config`.
- **`"pgsql:host={$config['db_host']};..."`** — perhatikan sintaks
  **`{$config['db_host']}`** dengan kurung kurawal — ini diperlukan
  karena PHP butuh bantuan tambahan untuk menyisipkan **elemen array**
  (bukan sekadar variabel biasa seperti `$host` di
  [dokumentasi jobsheet-08 §4.3](../../jobsheet-08/Dokumentasi/04-koneksi-pdo.md#43-membuat-koneksi-new-pdo))
  ke dalam string interpolation — tanpa kurung kurawal, PHP bisa salah
  menafsirkan di mana nama variabel berakhir dan teks biasa dimulai.
- Sisa kode (`try`/`catch`, `PDO::ATTR_ERRMODE`, `die(...)`) **identik
  persis** dengan yang sudah kamu bedah di
  [dokumentasi jobsheet-08 §4.4](../../jobsheet-08/Dokumentasi/04-koneksi-pdo.md#44-menangani-kegagalan-koneksi) —
  hanya sumber nilainya yang berubah, dari variabel tertulis langsung
  menjadi array `$config` yang dibaca dari file terpisah.

## 2.5 Cara Mengatur Environment Variable Saat Deployment

Sesuai [README.md](../README.md) jobsheet ini:

```bash
DB_HOST=127.0.0.1 DB_NAME=simpus_mini DB_USER=produser DB_PASS=rahasia php -S localhost:8000
```

Menulis `NAMA_VARIABEL=nilai` **sebelum** sebuah perintah di terminal
(command line) adalah cara mengatur environment variable **khusus
untuk proses itu saja** — dalam contoh ini, server `php -S` yang
dijalankan akan "melihat" `DB_HOST`, `DB_NAME`, `DB_USER`, `DB_PASS`
dengan nilai yang ditentukan, dan `getenv()` di `config.php` akan
membaca nilai-nilai itu, **menimpa** nilai cadangan (`'localhost'`,
`'postgres'`, dst.). Di hosting/server produksi sungguhan, environment
variable seperti ini biasanya diatur lewat panel kontrol
hosting/layanan cloud, bukan diketik manual di terminal — tapi
**konsepnya sama persis**: nilai rahasia disimpan **di luar** kode
sumber.

## 2.6 Peringatan Penting

Sesuai [README.md](../README.md) jobsheet ini:

> Jangan mengubah nilai default di `includes/config.php` menjadi
> kredensial asli lalu meng-commit-nya ke repository publik.

Ini menegaskan kembali **inti masalah** yang sudah dibahas di
[bab 1 §1.2](01-konsep-dasar-deployment-config.md#12-masalahnya-kredensial-yang-menempel-di-kode):
memindahkan kredensial ke `config.php` **tidak otomatis** membuatnya
aman kalau kamu tetap **menuliskan** kredensial asli di file itu dan
meng-commit-nya ke Git. Keamanan sesungguhnya baru didapat kalau
kredensial asli **hanya** diatur lewat environment variable di server
produksi (seperti [§2.5](#25-cara-mengatur-environment-variable-saat-deployment)) —
`config.php` yang di-commit ke repository **tetap** hanya berisi nilai
cadangan yang aman untuk pengembangan lokal.

Lanjut ke: [Dokumentasi Proyek: README, ERD, & Manual Pengguna](03-dokumentasi-proyek-final.md)
