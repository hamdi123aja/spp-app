<?php
session_start();
if (isset($_SESSION['user'])) {
  if ($_SESSION['user']['role'] === 'admin') header("Location: admin/dashboard.php");
  elseif ($_SESSION['user']['role'] === 'petugas') header("Location: petugas/dashboard.php");
  elseif ($_SESSION['user']['role'] === 'siswa') header("Location: siswa/dashboard.php");
  exit;
}
header("Location: auth/login.php");
exit;
