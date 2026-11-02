Berarti field anggota yang sebenarnya adalah `nama`, `no_anggota`, `alamat`, dan `no_hp`. Contoh sebelumnya yang menggunakan email perlu diganti dengan field tersebut.

Kode kamu sudah memiliki flash message untuk kesalahan dan keberhasilan. Jadi, yang perlu ditambahkan adalah **validasi** untuk kasus yang belum diperiksa, bukan membuat mekanisme flash baru.

## 1. Pertahankan pemeriksaan nama dan nomor anggota

Pemeriksaan yang sudah ada tetap digunakan:

- Nama tidak boleh kosong.
- Nomor anggota tidak boleh kosong.

Untuk nama, tidak perlu membatasi input hanya pada huruf karena aturan seperti itu bisa menolak nama yang mengandung apostrof atau tanda hubung. Pada contoh ini, cukup pastikan nama diisi.

## 2. Periksa nomor anggota yang duplikat

Nomor anggota sebaiknya membedakan satu anggota dari anggota lainnya. Karena itu, sebelum menyimpan data, periksa apakah nomor tersebut sudah ada di `$_SESSION['anggota']`.

Contoh:

- Anggota pertama menggunakan `AGT001`.
- Anggota berikutnya menggunakan `AGT001`.
- Penyimpanan kedua ditolak dengan pesan: “No. Anggota sudah digunakan.”

Pada kode berikut, pemeriksaan tidak membedakan huruf besar dan kecil. Artinya, `AGT001` dan `agt001` dianggap nomor yang sama. Ini adalah aturan yang dipilih untuk contoh aplikasi ini.

Format nomor anggota belum dibatasi karena kamu belum memberikan aturan khusus, misalnya harus diawali `AGT` atau harus berisi angka saja.

## 3. Tambahkan aturan alamat dan nomor HP

Untuk latihan ini, alamat dibuat wajib diisi. Alasannya, data anggota yang disimpan diharapkan memiliki alamat. Jika pada aplikasi kamu alamat memang opsional, hapus pemeriksaan alamat kosong.

Nomor HP tetap opsional agar tidak mengubah kewajiban field yang belum diketahui. Namun, jika diisi, gunakan aturan:

- Berisi 10–15 digit.
- Boleh memiliki satu tanda `+` di awal.
- Tidak mengandung huruf, spasi, atau tanda hubung.

Contoh:

| Input nomor HP | Hasil |
|---|---|
| Kosong | Diterima karena opsional |
| `081234567890` | Diterima |
| `+6281234567890` | Diterima |
| `0812abc789` | Ditolak |
| `0812-3456-7890` | Ditolak |

Pola `preg_match()` yang digunakan adalah `/\A\+?[0-9]{10,15}\z/`. Pemeriksaan `!== 1` berarti input ditolak jika pola tidak cocok atau pemeriksaan gagal. `preg_match()` mengembalikan `1` untuk kecocokan, `0` untuk ketidakcocokan, dan `false` untuk kesalahan. [w3schools](https://www.w3schools.com/php/func_regex_preg_match.asp)

Batasan 10–15 digit di sini merupakan aturan sederhana untuk latihan, bukan pemeriksaan bahwa nomor tersebut benar-benar aktif.

## 4. Gunakan flash message yang sudah ada

Setiap kesalahan ditambahkan ke `$errors`. Setelah seluruh field diperiksa, pesan digabungkan menggunakan:

```php
implode(' ', $errors)
```

Misalnya, jika alamat kosong dan nomor HP berisi huruf, pesan yang ditampilkan menjadi:

```text
Alamat wajib diisi. No. HP harus berisi 10-15 digit, dengan tanda + opsional di awal.
```

Data tidak disimpan jika ada kesalahan. Pengguna diarahkan kembali ke `tambah.php`, yang harus menampilkan `$_SESSION['flash']` seperti pada halaman tambah buku.

## Kode lengkap `anggota/proses_tambah.php`

Kode ini mempertahankan nama field dan struktur penyimpanan dari kode kamu.

```php
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Proses penambahan hanya menerima pengiriman melalui POST.
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: tambah.php');
    exit;
}

$nama = trim($_POST['nama'] ?? '');
$noAnggota = trim($_POST['no_anggota'] ?? '');
$alamat = trim($_POST['alamat'] ?? '');
$noHp = trim($_POST['no_hp'] ?? '');

$errors = [];

// 1. Validasi nama.
if ($nama === '') {
    $errors[] = 'Nama wajib diisi.';
}

// 2. Validasi nomor anggota.
if ($noAnggota === '') {
    $errors[] = 'No. Anggota wajib diisi.';
} else {
    // Cek apakah nomor anggota sudah digunakan.
    foreach ($_SESSION['anggota'] ?? [] as $anggota) {
        $nomorTersimpan = $anggota['no_anggota'] ?? '';

        if (strcasecmp($nomorTersimpan, $noAnggota) === 0) {
            $errors[] = 'No. Anggota sudah digunakan.';
            break;
        }
    }
}

// 3. Validasi alamat.
// Hapus blok ini jika alamat boleh kosong.
if ($alamat === '') {
    $errors[] = 'Alamat wajib diisi.';
}

// 4. Validasi nomor HP.
// Nomor HP boleh kosong, tetapi formatnya diperiksa jika diisi.
if (
    $noHp !== '' &&
    preg_match('/\A\+?[0-9]{10,15}\z/', $noHp) !== 1
) {
    $errors[] = 'No. HP harus berisi 10-15 digit, dengan tanda + opsional di awal.';
}

// Jika ada kesalahan, simpan flash dan kembali ke form.
if (!empty($errors)) {
    $_SESSION['flash'] = [
        'type' => 'error',
        'pesan' => implode(' ', $errors),
    ];

    header('Location: tambah.php');
    exit;
}

// Siapkan tempat penyimpanan anggota jika belum tersedia.
if (!isset($_SESSION['anggota'])) {
    $_SESSION['anggota'] = [];
}

// Simpan data setelah seluruh validasi berhasil.
$_SESSION['anggota'][] = [
    'nama' => $nama,
    'no_anggota' => $noAnggota,
    'alamat' => $alamat,
    'no_hp' => $noHp,
];

// Simpan pesan keberhasilan.
$_SESSION['flash'] = [
    'type' => 'success',
    'pesan' => 'Anggota berhasil ditambahkan.',
];

header('Location: list.php');
exit;
```