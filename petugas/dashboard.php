<?php
session_start();
include "../layout/header.php";
include "../layout/sidebar.php";
require_once "../config/database.php";

// Cek role
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'petugas') {
  header("Location: ../auth/login.php");
  exit;
}

// Ambil data ringkasan
$totalSiswa   = $pdo->query("SELECT COUNT(*) FROM siswa")->fetchColumn();
$totalSpp     = $pdo->query("SELECT COUNT(*) FROM spp")->fetchColumn();
$totalBayar   = $pdo->query("SELECT IFNULL(SUM(jumlah_bayar), 0) FROM pembayaran")->fetchColumn();

// Ambil 5 pembayaran terakhir
$latestPayments = $pdo->query("
    SELECT p.tgl_bayar, s.nama AS nama_siswa, p.jumlah_bayar, u.nama AS petugas
    FROM pembayaran p
    JOIN siswa s ON p.id_siswa = s.id_siswa
    JOIN users u ON p.id_petugas = u.id_user
    ORDER BY p.id_pembayaran DESC
    LIMIT 5
")->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="main-content">
  <h2>Dashboard Petugas SPP</h2>

  <div class="dashboard-cards">
    <div class="card siswa">
      <h3>Total Siswa</h3>
      <p><?= $totalSiswa ?></p>
    </div>
    <div class="card spp">
      <h3>Jenis SPP</h3>
      <p><?= $totalSpp ?></p>
    </div>
    <div class="card admin">
      <h3>Total Pembayaran</h3>
      <p>Rp<?= number_format($totalBayar, 0, ',', '.') ?></p>
    </div>
  </div>

  <div class="card shadow mt-4">
    <div class="card-header bg-primary text-white">
      Pembayaran Terbaru
    </div>
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-bordered">
          <thead class="table-dark">
            <tr>
              <th>No</th>
              <th>Tanggal</th>
              <th>Nama Siswa</th>
              <th>Jumlah Bayar</th>
              <th>Petugas</th>
            </tr>
          </thead>
          <tbody>
            <?php if ($latestPayments): ?>
              <?php $no = 1; ?>
              <?php foreach ($latestPayments as $row): ?>
                <tr>
                  <td><?= $no++ ?></td>
                  <td><?= htmlspecialchars($row['tgl_bayar']) ?></td>
                  <td><?= htmlspecialchars($row['nama_siswa']) ?></td>
                  <td>Rp<?= number_format($row['jumlah_bayar'], 0, ',', '.') ?></td>
                  <td><?= htmlspecialchars($row['petugas']) ?></td>
                </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr>
                <td colspan="5" class="text-center">Belum ada data pembayaran.</td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<?php include "../layout/footer.php"; ?>