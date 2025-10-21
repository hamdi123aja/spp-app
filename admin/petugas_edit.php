<?php
require "../config/database.php";
include "../layout/header.php";
include "../layout/sidebar.php";

$id   = $_GET['id'] ?? null;
$username = $nama = "";
if ($id) {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id_user=? AND role='petugas'");
    $stmt->execute([$id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($row) {
        $username = $row['username'];
        $nama     = $row['nama'];
    }
}

// simpan data
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $nama     = $_POST['nama'];
    $password = $_POST['password'];

    if ($id) {
        // update
        if ($password) {
            $stmt = $pdo->prepare("UPDATE users SET username=?, nama=?, password=? WHERE id_user=? AND role='petugas'");
            $stmt->execute([$username, $nama, $password, $id]);
        } else {
            $stmt = $pdo->prepare("UPDATE users SET username=?, nama=? WHERE id_user=? AND role='petugas'");
            $stmt->execute([$username, $nama, $id]);
        }
    } else {
        // insert baru
        $stmt = $pdo->prepare("INSERT INTO users (username,password,nama,role) VALUES (?,?,?,?)");
        $stmt->execute([$username, $password ?: '123', $nama, 'petugas']);
    }

    header("Location: petugas.php");
    exit;
}
?>

<div class="main-content">
    <h1><?= $id ? "Edit Petugas" : "Tambah Petugas" ?></h1>

    <form method="post" class="form-box">
        <label>Username</label>
        <input type="text" name="username" value="<?= htmlspecialchars($username) ?>" required>

        <label>Nama</label>
        <input type="text" name="nama" value="<?= htmlspecialchars($nama) ?>" required>

        <label>Password (kosongkan jika tidak diubah)</label>
        <input type="password" name="password">

        <button type="submit" class="btn btn-success">Simpan</button>
        <a href="petugas.php" class="btn btn-danger">Batal</a>
    </form>
</div>

<?php include "../layout/footer.php"; ?>