<?php
require "../config/database.php";
include "../layout/header.php";
include "../layout/sidebar.php";

$db = $pdo;

// Ambil data untuk edit
$id = $_GET['id'] ?? null;
$kelas = null;
if ($id) {
    $stmt = $db->prepare("SELECT * FROM kelas WHERE id_kelas=?");
    $stmt->execute([$id]);
    $kelas = $stmt->fetch(PDO::FETCH_ASSOC);
}

// Simpan data
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_kelas = $_POST['nama_kelas'];
    $kompetensi = $_POST['kompetensi_keahlian'];

    if ($id) {
        // Update
        $stmt = $db->prepare("UPDATE kelas SET nama_kelas=?, kompetensi_keahlian=? WHERE id_kelas=?");
        $stmt->execute([$nama_kelas, $kompetensi, $id]);
    } else {
        // Insert
        $stmt = $db->prepare("INSERT INTO kelas (nama_kelas, kompetensi_keahlian) VALUES (?, ?)");
        $stmt->execute([$nama_kelas, $kompetensi]);
    }

    header("Location: kelas.php");
    exit;
}
?>
<div class="main-content">
    <h1><?= $id ? "Edit Kelas" : "Tambah Kelas" ?></h1>

    <form method="post" class="form-box">
        <label for="nama_kelas">Nama Kelas</label>
        <input type="text" name="nama_kelas" id="nama_kelas" class="form-control"
            value="<?= htmlspecialchars($kelas['nama_kelas'] ?? '') ?>" required>

        <label for="kompetensi_keahlian">Kompetensi Keahlian</label>
        <input type="text" name="kompetensi_keahlian" id="kompetensi_keahlian" class="form-control"
            value="<?= htmlspecialchars($kelas['kompetensi_keahlian'] ?? '') ?>" required>

        <button type="submit" class="btn btn-success mt-3">Simpan</button>
        <a href="kelas.php" class="btn btn-danger mt-3">Batal</a>
    </form>
</div>
<?php include "../layout/footer.php"; ?>