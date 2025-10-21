<?php
session_start();
require "../config/database.php";
include "../layout/header.php";
include "../layout/sidebar.php";

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
  header("Location: ../auth/login.php");
  exit;
}

// Hitung data
$totalSiswa   = $pdo->query("SELECT COUNT(*) FROM siswa")->fetchColumn();
$totalPetugas = $pdo->query("SELECT COUNT(*) FROM users WHERE role='petugas'")->fetchColumn();
$totalSpp     = $pdo->query("SELECT COUNT(*) FROM spp")->fetchColumn();
$totalBayar   = $pdo->query("SELECT IFNULL(SUM(jumlah_bayar), 0) FROM pembayaran")->fetchColumn();

// Data pembayaran terbaru
$stmt = $pdo->query("SELECT p.id_pembayaran, s.nama AS nama_siswa, p.tgl_bayar, p.jumlah_bayar
                     FROM pembayaran p
                     JOIN siswa s ON p.id_siswa = s.id_siswa
                     ORDER BY p.id_pembayaran DESC
                     LIMIT 5");
$pembayaranTerbaru = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="main-content">
  <h2>Dashboard Admin</h2>

  <div class="dashboard-cards">
    <div class="card admin">
      <h3>Total Siswa</h3>
      <p><?= $totalSiswa ?></p>
    </div>
    <div class="card petugas">
      <h3>Total Petugas</h3>
      <p><?= $totalPetugas ?></p>
    </div>
    <div class="card spp">
      <h3>Data SPP</h3>
      <p><?= $totalSpp ?></p>
    </div>
    <div class="card siswa">
      <h3>Total Pembayaran</h3>
      <p>Rp<?= number_format($totalBayar, 0, ',', '.') ?></p>
    </div>
  </div>

  <div class="card mt-4">
    <div class="card-header bg-primary text-white">
      <h5 class="mb-0">Pembayaran Terbaru</h5>
    </div>
    <div class="card-body">
      <table class="table table-bordered table-striped">
        <thead class="table-dark">
          <tr>
            <th>No</th>
            <th>Nama Siswa</th>
            <th>Tanggal Bayar</th>
            <th>Jumlah Bayar</th>
          </tr>
        </thead>
        <tbody>
          <?php if ($pembayaranTerbaru): ?>
            <?php
            $no = 1;
            foreach ($pembayaranTerbaru as $row):
            ?>
              <tr>
                <td><?= $no++ ?></td>
                <td><?= htmlspecialchars($row['nama_siswa']) ?></td>
                <td><?= $row['tgl_bayar'] ?></td>
                <td>Rp<?= number_format($row['jumlah_bayar'], 0, ',', '.') ?></td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr>
              <td colspan="4" class="text-center">Belum ada pembayaran</td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<?php include "../layout/footer.php"; ?>