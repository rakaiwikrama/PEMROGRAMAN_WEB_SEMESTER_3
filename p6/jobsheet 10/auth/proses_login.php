<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require __DIR__ . '/../includes/koneksi.php';

$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';

if ($username === '' || $password === '') {
    $_SESSION['flash'] = ['type' => 'error', 'pesan' => 'Username dan password wajib diisi.'];
    header('Location: login.php');
    exit;
}

// 1. Inisialisasi pelacak percobaan login gagal di session per username
if (!isset($_SESSION['login_attempts'])) {
    $_SESSION['login_attempts'] = [];
}

$max_attempts = 3; // Batas maksimal 3 kali salah
$attempts = $_SESSION['login_attempts'][$username] ?? 0;

// 2. Cek apakah akun terkunci sementara
if ($attempts >= $max_attempts) {
    $_SESSION['flash'] = [
        'type' => 'error', 
        'pesan' => 'Terlalu banyak percobaan gagal. Akun dikunci sementara demi keamanan.'
    ];
    header('Location: login.php');
    exit;
}

// 3. Ambil data user dari database
$stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username");
$stmt->execute(['username' => $username]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// 4. Verifikasi password dengan password_verify()
if ($user && password_verify($password, $user['password'])) {
    // Jika berhasil, reset riwayat gagal
    unset($_SESSION['login_attempts'][$username]);

    // Set sesi login
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['nama'] = $user['nama'];
    $_SESSION['role'] = $user['role'];

    header('Location: ../index.php');
    exit;
}

// Jika gagal: tambahkan hitungan attempt
$_SESSION['login_attempts'][$username] = $attempts + 1;
$sisa_kesempatan = $max_attempts - $_SESSION['login_attempts'][$username];

if ($sisa_kesempatan > 0) {
    $pesan_error = "Username atau password salah. Sisa kesempatan: {$sisa_kesempatan} kali.";
} else {
    $pesan_error = "Anda telah gagal 3 kali. Akun terkunci sementara.";
}

$_SESSION['flash'] = ['type' => 'error', 'pesan' => $pesan_error];
header('Location: login.php');
exit;