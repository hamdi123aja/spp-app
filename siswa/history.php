<?php
session_start();
require "../config/database.php";
include "../layout/header.php";
include "../layout/sidebar.php";

$id_siswa = $_SESSION['user']['id_user']; // ambil dari session login siswa

$stmt = $pdo->prepare("SELECT p.*, u.nama AS nama_petugas, s.nama AS nama_siswa
                       FROM pembayaran p
                       JOIN users u ON p.id_petugas=u.id_user
                       JOIN siswa s ON p.id_siswa=s.id_siswa
                       WHERE p.id_siswa=? 
                       ORDER BY p.tgl_bayar DESC LIMIT 5");
$stmt->execute([$id_siswa]);
$history = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="main-content">
  <h2>History Pembayaran</h2>
  <table class="table table-bordered">
    <thead>
      <tr>
        <th>No</th>
        <th>Tanggal</th>
        <th>Bulan</th>
        <th>Tahun</th>
        <th>Jumlah</th>
        <th>Petugas</th>
      </tr>
    </thead>
    <tbody>
      <?php $no = 1;
      foreach ($history as $row): ?>
        <tr>
          <td><?= $no++ ?></td>
          <td><?= htmlspecialchars($row['tgl_bayar']) ?></td>
          <td><?= htmlspecialchars($row['bulan_dibayar']) ?></td>
          <td><?= htmlspecialchars($row['tahun_dibayar']) ?></td>
          <td>Rp<?= number_format($row['jumlah_bayar'], 0, ',', '.') ?></td>
          <td><?= htmlspecialchars($row['nama_petugas']) ?></td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
  <a href="history_detail.php" class="btn btn-info">Lihat Semua</a>
</div>

<?php include "../layout/footer.php"; ?>