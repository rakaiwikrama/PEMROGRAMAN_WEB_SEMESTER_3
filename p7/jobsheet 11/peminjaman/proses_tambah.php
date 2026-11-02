<?php
require __DIR__ . '/../includes/auth.php';
require __DIR__ . '/../includes/csrf.php';
require __DIR__ . '/../includes/koneksi.php';

csrf_verify();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: tambah.php');
    exit;
}

$anggota_id = $_POST['anggota_id'] ?? null;
$buku_id = $_POST['buku_id'] ?? null;

// Validasi input dasar
$errors = [];
if (empty($anggota_id)) {
    $errors[] = "Anggota wajib dipilih.";
}
if (empty($buku_id)) {
    $errors[] = "Buku wajib dipilih.";
}

if (!empty($errors)) {
    $_SESSION['flash'] = ['type' => 'error', 'pesan' => implode(' ', $errors)];
    header('Location: tambah.php');
    exit;
}

try {
    // Mulai Database Transaction
    $pdo->beginTransaction();

    // 1. VALIDASI BISNIS: Cek keterlambatan > 14 hari pada transaksi aktif (status = 'dipinjam')
    $stmtCekTerlambat = $pdo->prepare("
        SELECT COUNT(*) FROM peminjaman 
        WHERE anggota_id = :anggota_id 
          AND status = 'dipinjam' 
          AND (CURRENT_DATE - tanggal_pinjam) > 14
    ");
    $stmtCekTerlambat->execute(['anggota_id' => $anggota_id]);
    $terlambatCount = $stmtCekTerlambat->fetchColumn();

    if ($terlambatCount > 0) {
        throw new Exception("Peminjaman ditolak! Anggota ini memiliki buku terlambat dikembalikan lebih dari 14 hari.");
    }

    // 2. CEK & KUNCI STOK BUKU (SELECT ... FOR UPDATE mencegah race condition stok ganda)
    $stmtBuku = $pdo->prepare("SELECT stok FROM buku WHERE id = :id FOR UPDATE");
    $stmtBuku->execute(['id' => $buku_id]);
    $buku = $stmtBuku->fetch(PDO::FETCH_ASSOC);

    if (!$buku) {
        throw new Exception("Buku tidak ditemukan di database.");
    }

    if ($buku['stok'] <= 0) {
        throw new Exception("Stok buku sudah habis, tidak dapat dipinjam.");
    }

    // 3. INSERT TRANSAKSI PEMINJAMAN BARU
    $stmtInsert = $pdo->prepare("
        INSERT INTO peminjaman (anggota_id, buku_id, tanggal_pinjam, status) 
        VALUES (:anggota_id, :buku_id, CURRENT_DATE, 'dipinjam')
    ");
    $stmtInsert->execute([
        'anggota_id' => $anggota_id,
        'buku_id' => $buku_id
    ]);

    // 4. KURANGI STOK BUKU
    $stmtUpdateStok = $pdo->prepare("
        UPDATE buku SET stok = stok - 1 WHERE id = :id
    ");
    $stmtUpdateStok->execute(['id' => $buku_id]);

    // Jika seluruh proses sukses, simpan permanen (Commit)
    $pdo->commit();

    $_SESSION['flash'] = ['type' => 'success', 'pesan' => 'Peminjaman buku berhasil dicatat.'];
    header('Location: riwayat.php'); // Sesuaikan dengan halaman daftar/riwayat peminjaman Anda
    exit;

} catch (Exception $e) {
    // Jika ada error atau aturan bisnis yang dilanggar, batalkan semua perubahan (RollBack)
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    $_SESSION['flash'] = ['type' => 'error', 'pesan' => $e->getMessage()];
    header('Location: tambah.php');
    exit;
}