<?php
require "../config/database.php";
include "../layout/header.php";
include "../layout/sidebar.php";

// ambil semua data spp
$stmt = $pdo->query("SELECT * FROM spp");
$spp = $stmt->fetchAll(PDO::FETCH_ASSOC);

// hapus data
if (isset($_GET['hapus'])) {
  $id = $_GET['hapus'];
  $stmt = $pdo->prepare("DELETE FROM spp WHERE id_spp=?");
  $stmt->execute([$id]);
  header("Location: spp.php");
  exit;
}
?>

<div class="main-content">
  <h1>Kelola SPP</h1>
  <a href="spp_edit.php" class="btn btn-success mb-3">+ Tambah SPP</a>

  <table class="table table-bordered">
    <tr>
      <th>Tahun</th>
      <th>Nominal</th>
      <th>Aksi</th>
    </tr>
    <?php foreach ($spp as $sp): ?>
      <tr>
        <td><?= htmlspecialchars($sp['tahun']) ?></td>
        <td>Rp<?= number_format($sp['nominal'], 0, ',', '.') ?></td>
        <td>
          <a href="spp_edit.php?id=<?= $sp['id_spp'] ?>" class="btn btn-sm btn-warning">Edit</a>
          <a href="spp.php?hapus=<?= $sp['id_spp'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Yakin hapus data ini?')">Hapus</a>
        </td>
      </tr>
    <?php endforeach; ?>
  </table>
</div>

<?php include "../layout/footer.php"; ?>