<?php
session_start();
require_once '../config/database.php';
$db = $pdo;

$error = "";

// LOGIN
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $username = trim($_POST['username']);
  $password = $_POST['password'];

  $stmt = $db->prepare("SELECT * FROM users WHERE username=? AND password=? LIMIT 1");
  $stmt->execute([$username, $password]);
  $user = $stmt->fetch(PDO::FETCH_ASSOC);

  if ($user) {
    if ($user['role'] === 'siswa') {
            $stmtSiswa = $db->prepare("SELECT id_siswa FROM siswa WHERE id_user=? LIMIT 1");
            $stmtSiswa->execute([$user['id_user']]);
            $siswa = $stmtSiswa->fetch(PDO::FETCH_ASSOC);

            if ($siswa) {
                $user['id_siswa'] = $siswa['id_siswa'];
            }
        }

     $_SESSION['user'] = [
            'id_user' => $user['id_user'],
            'username' => $user['username'],
            'nama' => $user['nama'],
            'role' => $user['role'],
            'id_siswa' => $user['id_siswa'] ?? null
        ];
    // Set pesan notifikasi login
    $_SESSION['flash'] = [
      'type' => 'success',
      'msg'  => "Halo {$user['nama']}, Anda berhasil login sebagai {$user['role']}!"
    ];

    // Redirect sesuai role
    if ($user['role'] === 'admin') {
      header("Location: ../admin/dashboard.php");
      exit;
    } elseif ($user['role'] === 'petugas') {
      header("Location: ../petugas/dashboard.php");
      exit;
    } elseif ($user['role'] === 'siswa') {
      header("Location: ../siswa/dashboard.php");
      exit;
    }
  } else {
    $error = "❌ Username atau Password salah!";
  }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <title>Login Aplikasi SPP</title>
  <link rel="stylesheet" href="../assets/style.css">
  <link rel="icon" type="image/png" href="../assets/favicon.png">
</head>
<body>
  <div class="login-box">
    <h2>Login Aplikasi Pembayaran SPP</h2>
    <?php if ($error): ?>
      <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <form method="post">
      <input type="text" name="username" placeholder="Username" required>
      <input type="password" name="password" placeholder="Password" required>
      <button type="submit">Login</button>
    </form>
  </div>
</body>
</html>
