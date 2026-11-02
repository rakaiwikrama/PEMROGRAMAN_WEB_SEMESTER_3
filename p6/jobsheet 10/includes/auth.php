<?php
// Guard clause: di-include di baris paling atas setiap halaman yang
// membutuhkan login (sebelum header.php mengeluarkan output apa pun),
// agar header('Location: ...') masih bisa dipanggil.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id']) && isset($_COOKIE['remember_user'])) {
    // Validasi token/ID dari cookie ke database, lalu "hidupkan" kembali sesi
    $_SESSION['user_id'] = $_COOKIE['remember_user'];
    // Ambil juga data role dari database untuk diset ke $_SESSION['role']
}

