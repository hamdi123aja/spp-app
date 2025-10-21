<?php
session_start();

// Simpan pesan notifikasi logout
$_SESSION['flash'] = [
    'type' => 'success',
    'msg'  => 'Anda berhasil logout.',
];

// Hancurkan semua data sesi
session_destroy();

// Alihkan pengguna ke halaman login
header("Location: login.php");
exit;
