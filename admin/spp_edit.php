<?php
require "../config/database.php";
include "../layout/header.php";
include "../layout/sidebar.php";
$db = $pdo;

// Cek user login atau ambil dari URL
if (isset($_SESSION['user_id'])) {
    $id_user = $_SESSION['user_id'];
} elseif (isset($_GET['id_user'])) {
    $id_user = $_GET['id_user'];
} else {
    // Jika tidak ada keduanya, alihkan ke login
    header("Location: ../auth/login.php");
    exit;
}


$id = $_GET['id'] ?? null;
$tahun = $nominal = "";
if ($id) {
    $stmt = $pdo->prepare("SELECT * FROM spp WHERE id_spp=?");
    $stmt->execute([$id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($row) {
        $tahun   = $row['tahun'];
        $nominal = $row['nominal'];
    }
}

// simpan data
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tahun   = $_POST['tahun'];
    $nominal = $_POST['nominal'];

    if ($id) {
        $stmt = $pdo->prepare("UPDATE spp SET tahun=?, nominal=? WHERE id_spp=?");
        $stmt->execute([$tahun, $nominal, $id]);
    } else {
        $stmt = $pdo->prepare("INSERT INTO spp (tahun,nominal) VALUES (?,?)");
        $stmt->execute([$tahun, $nominal]);
    }

    header("Location: spp.php");
    exit;
}
?>

<div class="main-content">
    <h1><?= $id ? "Edit SPP" : "Tambah SPP" ?></h1>

    <form method="post" class="form-box">
        <label>Tahun</label>
        <input type="number" name="tahun" value="<?= htmlspecialchars($tahun) ?>" required>

        <label>Nominal</label>
        <input type="number" name="nominal" value="<?= htmlspecialchars($nominal) ?>" required>

        <button type="submit" class="btn btn-success">Simpan</button>
        <a href="spp.php" class="btn btn-danger">Batal</a>
    </form>
</div>

<?php include "../layout/footer.php"; ?>