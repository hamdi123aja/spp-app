<?php
require "../config/database.php";
include "../layout/header.php";
include "../layout/sidebar.php";

// ambil semua data petugas
$stmt = $pdo->query("SELECT * FROM users WHERE role='petugas'");
$petugas = $stmt->fetchAll(PDO::FETCH_ASSOC);

// hapus data
if (isset($_GET['hapus'])) {
  $id = $_GET['hapus'];
  $stmt = $pdo->prepare("DELETE FROM users WHERE id_user=? AND role='petugas'");
  $stmt->execute([$id]);
  header("Location: petugas.php");
  exit;
}
?>

<div class="main-content">
  <h1>Kelola Petugas</h1>
  <a href="petugas_edit.php" class="btn btn-success mb-3">+ Tambah Petugas</a>

  <table class="table table-bordered">
    <tr>
      <th>Username</th>
      <th>Nama</th>
      <th>Aksi</th>
    </tr>
    <?php foreach ($petugas as $p): ?>
      <tr>
        <td><?= htmlspecialchars($p['username']) ?></td>
        <td><?= htmlspecialchars($p['nama']) ?></td>
        <td>
          <a href="petugas_edit.php?id=<?= $p['id_user'] ?>" class="btn btn-sm btn-warning">Edit</a>
          <a href="petugas.php?hapus=<?= $p['id_user'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Yakin hapus data ini?')">Hapus</a>
        </td>
      </tr>
    <?php endforeach; ?>
  </table>
</div>

<?php include "../layout/footer.php"; ?>