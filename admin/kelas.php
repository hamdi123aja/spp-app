<?php
require "../config/database.php";
include "../layout/header.php";
include "../layout/sidebar.php";

$db = $pdo;

// Hapus data
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    $stmt = $db->prepare("DELETE FROM kelas WHERE id_kelas=?");
    $stmt->execute([$id]);
    header("Location: kelas.php");
    exit;
}

// Ambil semua kelas dan urutkan berdasarkan ID secara descending (terbaru)
// Menggunakan 'id_kelas' sebagai patokan data terbaru
$stmt = $db->query("SELECT * FROM kelas ORDER BY id_kelas DESC");
$kelas = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<div class="main-content">
    <h1>Kelola Kelas</h1>
    <a href="kelas_edit.php" class="btn btn-primary mb-3">Tambah Kelas</a>
    <table class="table table-bordered">
        <tr>
            <th>No</th>
            <th>Nama Kelas</th>
            <th>Kompetensi Keahlian</th>
            <th>Aksi</th>
        </tr>

        <?php
        // Inisialisasi variabel $no sebelum loop dimulai
        $no = 1;
        ?>
        <?php foreach ($kelas as $k): ?>
            <tr>
                <td><?= $no++ ?></td>
                <td><?= htmlspecialchars($k['nama_kelas']) ?></td>
                <td><?= htmlspecialchars($k['kompetensi_keahlian']) ?></td>
                <td>
                    <a href="kelas_edit.php?id=<?= $k['id_kelas'] ?>" class="btn btn-sm btn-warning">Edit</a>
                    <a href="kelas.php?hapus=<?= $k['id_kelas'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Yakin hapus?')">Hapus</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
</div>
<?php include "../layout/footer.php"; ?>