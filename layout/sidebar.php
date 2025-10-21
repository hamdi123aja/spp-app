<?php
if (session_status() === PHP_SESSION_NONE) session_start();
$current_page = basename($_SERVER['PHP_SELF']);
$role = $_SESSION['user']['role'] ?? '';
$basePath = "../" . $role;
?>

<div class="sidebar bg-dark text-white position-fixed" style="width:240px; height:100vh; top:0; left:0; padding-top:60px;">
  <h4 class="text-center mb-4">SPP APP</h4>
  <ul class="nav flex-column">

    <!-- Dashboard -->
    <li class="nav-item">
      <a href="<?= $basePath ?>/dashboard.php"
        class="nav-link text-white <?= $current_page == 'dashboard.php' ? 'active bg-primary rounded' : '' ?>">
        🏠 Dashboard
      </a>
    </li>

    <?php if ($role === 'admin'): ?>
      <!-- Menu khusus admin -->
      <li class="nav-item">
        <a href="<?= $basePath ?>/siswa.php"
          class="nav-link text-white <?= $current_page == 'siswa.php' ? 'active bg-primary rounded' : '' ?>">
          🎓 Kelola Siswa
        </a>
      </li>
      <li class="nav-item">
        <a href="<?= $basePath ?>/petugas.php"
          class="nav-link text-white <?= $current_page == 'petugas.php' ? 'active bg-primary rounded' : '' ?>">
          👩‍💼 Kelola Petugas
        </a>
      </li>
      <li class="nav-item">
        <a href="<?= $basePath ?>/kelas.php"
          class="nav-link text-white <?= $current_page == 'kelas.php' ? 'active bg-primary rounded' : '' ?>">
          🏫 Kelola Kelas
        </a>
      </li>
      <li class="nav-item">
        <a href="<?= $basePath ?>/spp.php"
          class="nav-link text-white <?= $current_page == 'spp.php' ? 'active bg-primary rounded' : '' ?>">
          💰 Kelola SPP
        </a>
      </li>
      <li class="nav-item">
        <a href="<?= $basePath ?>/pembayaran.php"
          class="nav-link text-white <?= $current_page == 'pembayaran.php' ? 'active bg-primary rounded' : '' ?>">
          ➕ Entry Pembayaran
        </a>
      </li>
      <li class="nav-item">
        <a href="<?= $basePath ?>/history.php"
          class="nav-link text-white <?= $current_page == 'history.php' ? 'active bg-primary rounded' : '' ?>">
          📑 Riwayat Pembayaran
        </a>
      </li>
      <li class="nav-item">
        <a href="<?= $basePath ?>/report.php"
          class="nav-link text-white <?= $current_page == 'report.php' ? 'active bg-primary rounded' : '' ?>">
          📒 Laporan
        </a>
      </li>
    <?php endif; ?>

    <?php if ($role === 'petugas'): ?>
      <!-- Admin & Petugas -->
      <li class="nav-item">
        <a href="<?= $basePath ?>/payment_entry.php"
          class="nav-link text-white <?= $current_page == 'payment_entry.php' ? 'active bg-primary rounded' : '' ?>">
          ➕ Entry Pembayaran
        </a>
      </li>
      <li class="nav-item">
        <a href="<?= $basePath ?>/history.php"
          class="nav-link text-white <?= $current_page == 'history.php' ? 'active bg-primary rounded' : '' ?>">
          📑 Riwayat Pembayaran
        </a>
      </li>
    <?php endif; ?>

    <?php if ($role === 'siswa'): ?>

    <?php endif; ?>

    <!-- Logout -->
    <li class="nav-item mt-3">
      <a href="../auth/logout.php" class="nav-link text-danger">🚪 Logout</a>
    </li>
  </ul>
</div>