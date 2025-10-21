<?php
session_start();
require "../config/database.php";
include "../layout/header.php";
include "../layout/sidebar.php";

$db = $pdo;

// --- LOGIKA HAPUS ---
if (isset($_GET['hapus'])) {
  $id = $_GET['hapus'];
  try {
    $stmt_hapus = $db->prepare("DELETE FROM siswa WHERE id_siswa = ?");
    $stmt_hapus->execute([$id]);
    $_SESSION['success'] = "Data siswa berhasil dihapus!";
  } catch (PDOException $e) {
    $_SESSION['error'] = "Gagal menghapus data: " . $e->getMessage();
  }
  header("Location: siswa.php");
  exit;
}

// Ambil data siswa
$stmt = $db->query("SELECT s.*, k.nama_kelas
                    FROM siswa s
                    JOIN kelas k ON s.id_kelas = k.id_kelas
                    ORDER BY s.id_siswa DESC");
$siswa = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Pesan notifikasi
$message = "";
if (isset($_SESSION['success'])) {
  $message = "<div class='alert alert-success'>" . $_SESSION['success'] . "</div>";
  unset($_SESSION['success']);
}
if (isset($_SESSION['error'])) {
  $message = "<div class='alert alert-danger'>" . $_SESSION['error'] . "</div>";
  unset($_SESSION['error']);
}
?>

<div class="main-content">
  <h1>Kelola Siswa</h1>
  <?= $message ?>
  <a href="siswa_edit.php" class="btn btn-primary mb-3">Tambah Siswa</a>
  <table class="table table-bordered">
    <thead class="table-light">
      <tr>
        <th>NISN</th>
        <th>Nama</th>
        <th>Kelas</th>
        <th>Aksi</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($siswa as $s): ?>
        <tr>
          <td><?= htmlspecialchars($s['nisn']) ?></td>
          <td><?= htmlspecialchars($s['nama']) ?></td>
          <td><?= htmlspecialchars($s['nama_kelas']) ?></td>
          <td>
            <a href="siswa_edit.php?id=<?= $s['id_siswa'] ?>" class="btn btn-sm btn-warning">Edit</a>
            <a href="siswa.php?hapus=<?= $s['id_siswa'] ?>"
              onclick="return confirm('Yakin ingin menghapus data ini?')"
              class="btn btn-sm btn-danger">Hapus</a>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>

<?php include "../layout/footer.php"; ?>