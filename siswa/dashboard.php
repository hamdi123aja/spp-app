<?php
session_start();
require "../config/database.php";
include "../layout/header.php";
include "../layout/sidebar.php";

$db = $pdo;

// hanya role siswa yang bisa akses
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'siswa') {
  header("Location: ../auth/login.php");
  exit;
}

// ambil id_user dari session
$id_user = $_SESSION['user']['id_user'];

// cari id_siswa berdasarkan id_user
$stmt = $db->prepare("SELECT id_siswa, nama FROM siswa WHERE id_user=? LIMIT 1");
$stmt->execute([$id_user]);
$siswa = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$siswa) {
  die("Data siswa tidak ditemukan. Hubungi admin!");
}

$id_siswa = $siswa['id_siswa'];

// ambil riwayat pembayaran untuk siswa ini
$stmt = $db->prepare("SELECT p.*, s.nama AS nama_siswa
                      FROM pembayaran p
                      JOIN siswa s ON p.id_siswa = s.id_siswa
                      WHERE p.id_siswa = ?
                      ORDER BY p.id_pembayaran DESC");
$stmt->execute([$id_siswa]);
$riwayat_pembayaran = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="main-content">
  <h2 class="mb-4">Dashboard Siswa</h2>

  <div class="card shadow mb-4">
    <div class="card-header bg-primary text-white py-3">
      <h6 class="m-0 font-weight-bold">Riwayat Pembayaran</h6>
    </div>
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-bordered table-striped table-hover" id="riwayatTable" width="100%">
          <thead class="table-dark text-center">
            <tr>
              <th>No</th>
              <th>Nama Siswa</th>
              <th>Tanggal Bayar</th>
              <th>Bulan</th>
              <th>Tahun</th>
              <th>Jumlah</th>
            </tr>
          </thead>
          <tbody>
            <?php if ($riwayat_pembayaran): ?>
              <?php $no = 1; ?>
              <?php foreach ($riwayat_pembayaran as $row): ?>
                <tr>
                  <td class="text-center"><?= $no++ ?></td>
                  <td><?= htmlspecialchars($row['nama_siswa']) ?></td>
                  <td><?= htmlspecialchars($row['tgl_bayar']) ?></td>
                  <td><?= htmlspecialchars($row['bulan_dibayar']) ?></td>
                  <td><?= htmlspecialchars($row['tahun_dibayar']) ?></td>
                  <td>Rp<?= number_format($row['jumlah_bayar'], 0, ',', '.') ?></td>
                </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr>
                <td colspan="6" class="text-center text-danger">Belum ada riwayat pembayaran untuk siswa ini.</td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<!-- DataTables -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script>
  $(document).ready(function() {
    $('#riwayatTable').DataTable({
      "pageLength": 5,
      "lengthMenu": [5, 10, 25, 50],
      "ordering": false,
      "responsive": true,
      "language": {
        "url": "//cdn.datatables.net/plug-ins/1.13.6/i18n/id.json"
      }
    });
  });
</script>

<?php include "../layout/footer.php"; ?>