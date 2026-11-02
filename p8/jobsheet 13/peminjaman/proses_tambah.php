<?php
require __DIR__ . '/../includes/auth.php';
require __DIR__ . '/../includes/csrf.php';
require __DIR__ . '/../includes/koneksi.php';

csrf_verify();

$anggotaId = $_POST['anggota_id'] ?? '';
$bukuId = $_POST['buku_id'] ?? '';

if ($anggotaId === '' || $bukuId === '') {
    $_SESSION['flash'] = ['type' => 'error', 'pesan' => 'Anggota dan buku wajib dipilih.'];
    header('Location: tambah.php');
    exit;
}

try {
    $pdo->beginTransaction();

    // 1. VALIDASI BISNIS: Cek apakah anggota memiliki peminjaman aktif yang terlambat > 14 hari
    // Di PostgreSQL, (CURRENT_DATE - tanggal_pinjam) menghasilkan selisih hari.
    $stmtCekTerlambat = $pdo->prepare("
        SELECT COUNT(*) FROM peminjaman 
        WHERE anggota_id = :anggota_id 
          AND status = 'dipinjam' 
          AND (CURRENT_DATE - tanggal_pinjam) > 14
    ");
    $stmtCekTerlambat->execute(['anggota_id' => $anggotaId]);
    $jumlahTerlambat = $stmtCekTerlambat->fetchColumn();

    if ($jumlahTerlambat > 0) {
        throw new Exception('Peminjaman ditolak! Anggota ini memiliki buku yang terlambat dikembalikan lebih dari 14 hari.');
    }

    // 2. Kunci baris buku (FOR UPDATE) agar stok tidak berubah oleh transaksi lain
    // di tengah proses ini — mencegah stok menjadi negatif akibat race condition.
    $cek = $pdo->prepare("SELECT stok FROM buku WHERE id = :id FOR UPDATE");
    $cek->execute(['id' => $bukuId]);
    $buku = $cek->fetch(PDO::FETCH_ASSOC);

    if (!$buku || $buku['stok'] < 1) {
        throw new Exception('Stok buku tidak tersedia.');
    }

    // 3. Catat transaksi peminjaman baru
    $insert = $pdo->prepare(
        "INSERT INTO peminjaman (buku_id, anggota_id, tanggal_pinjam, status)
         VALUES (:buku_id, :anggota_id, CURRENT_DATE, 'dipinjam')"
    );
    $insert->execute(['buku_id' => $bukuId, 'anggota_id' => $anggotaId]);

    // 4. Kurangi stok buku
    $update = $pdo->prepare("UPDATE buku SET stok = stok - 1 WHERE id = :id");
    $update->execute(['id' => $bukuId]);

    $pdo->commit();

    $_SESSION['flash'] = ['type' => 'success', 'pesan' => 'Peminjaman berhasil dicatat.'];
    header('Location: ../index.php');
    exit;
} catch (Exception $e) {
    $pdo->rollBack();
    $_SESSION['flash'] = ['type' => 'error', 'pesan' => 'Gagal mencatat peminjaman: ' . $e->getMessage()];
    header('Location: tambah.php');
    exit;
}