<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: tambah.php');
    exit;
}

$judul = trim($_POST['judul'] ?? '');
$pengarang = trim($_POST['pengarang'] ?? '');
$tahunInput = $_POST['tahun'] ?? '';
$isbn = trim($_POST['isbn'] ?? '');
$stokInput = $_POST['stok'] ?? '';
$kategori = trim($_POST['kategori'] ?? '');

$tahun = filter_var($tahunInput, FILTER_VALIDATE_INT);
$stok = filter_var($stokInput, FILTER_VALIDATE_INT);

$errors = [];

if ($judul === '') {
    $errors[] = 'Judul wajib diisi.';
}

if ($pengarang === '') {
    $errors[] = 'Pengarang wajib diisi.';
}

if ($tahun === false || $tahun < 1900 || $tahun > 2026) {
    $errors[] = 'Tahun harus berupa bilangan bulat antara 1900-2026.';
}

if ($stok === false || $stok < 0) {
    $errors[] = 'Stok harus berupa bilangan bulat dan tidak boleh negatif.';
}

if ($isbn !== '' && preg_match('/\A[0-9-]+\z/', $isbn) !== 1) {
    $errors[] = 'ISBN hanya boleh berisi angka dan tanda hubung.';
}

$kategoriValid = ['fiksi', 'non-fiksi', 'referensi'];

if (!in_array($kategori, $kategoriValid, true)) {
    $errors[] = 'Kategori buku tidak valid.';
}

if (!empty($errors)) {
    $_SESSION['flash'] = [
        'type' => 'error',
        'pesan' => implode(' ', $errors),
    ];

    header('Location: tambah.php');
    exit;
}

if (!isset($_SESSION['buku'])) {
    $_SESSION['buku'] = [];
}

$_SESSION['buku'][] = [
    'judul' => $judul,
    'pengarang' => $pengarang,
    'tahun' => $tahun,
    'isbn' => $isbn,
    'stok' => $stok,
    'kategori' => $kategori,
];

$_SESSION['flash'] = [
    'type' => 'success',
    'pesan' => 'Buku berhasil ditambahkan.',
];

header('Location: list.php');
exit;