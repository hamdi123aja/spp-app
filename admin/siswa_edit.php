<?php
session_start();
require "../config/database.php";
include "../layout/header.php";
include "../layout/sidebar.php";

$db = $pdo;

// Cek login
if (!isset($_SESSION['user'])) {
    header("Location: ../auth/login.php");
    exit;
}
$id_user = $_SESSION['user']['id_user']; // Ambil id_user dari session login

// Ambil data siswa jika mode edit
$id = $_GET['id'] ?? null;
$siswa = null;
if ($id) {
    $stmt = $db->prepare("SELECT * FROM siswa WHERE id_siswa=?");
    $stmt->execute([$id]);
    $siswa = $stmt->fetch(PDO::FETCH_ASSOC);
}

// Ambil data kelas & spp
$kelas = $db->query("SELECT * FROM kelas")->fetchAll(PDO::FETCH_ASSOC);
$spp   = $db->query("SELECT * FROM spp")->fetchAll(PDO::FETCH_ASSOC);

$error = "";

// Simpan data
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nisn   = trim($_POST['nisn']);
    $nis    = trim($_POST['nis']);
    $nama   = trim($_POST['nama']);
    $id_kelas = $_POST['id_kelas'];
    $id_spp   = $_POST['id_spp'];
    $alamat = trim($_POST['alamat']);
    $no_telp = trim($_POST['no_telp']);
    $id_siswa = $_POST['id_siswa'] ?? null;

    if (empty($nisn) || empty($nis) || empty($nama) || empty($id_kelas) || empty($id_spp)) {
        $error = "⚠️ NISN, NIS, Nama, Kelas, dan SPP wajib diisi!";
    } else {
        try {
            if ($id_siswa) {
                // Update
                $sql = "UPDATE siswa 
                        SET nisn=?, nis=?, nama=?, id_kelas=?, id_spp=?, alamat=?, no_telp=?, id_user=?
                        WHERE id_siswa=?";
                $stmt = $db->prepare($sql);
                $stmt->execute([$nisn, $nis, $nama, $id_kelas, $id_spp, $alamat, $no_telp, $id_user, $id_siswa]);
                $_SESSION['success'] = "Data siswa berhasil diperbarui!";
            } else {
                // Insert
                $sql = "INSERT INTO siswa (nisn, nis, nama, id_kelas, id_spp, alamat, no_telp, id_user) 
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
                $stmt = $db->prepare($sql);
                $stmt->execute([$nisn, $nis, $nama, $id_kelas, $id_spp, $alamat, $no_telp, $id_user]);
                $_SESSION['success'] = "Data siswa berhasil ditambahkan!";
            }

            header("Location: siswa.php");
            exit;
        } catch (PDOException $e) {
            $error = "Gagal menyimpan data: " . $e->getMessage();
        }
    }
}
?>

<div class="main-content">
    <h1><?= $id ? "Edit Siswa" : "Tambah Siswa" ?></h1>
    <?php if ($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="post" class="form-box">
        <input type="hidden" name="id_siswa" value="<?= htmlspecialchars($siswa['id_siswa'] ?? '') ?>">

        <label for="nisn">NISN</label>
        <input type="text" name="nisn" id="nisn" class="form-control"
            value="<?= htmlspecialchars($siswa['nisn'] ?? '') ?>" required>

        <label for="nis">NIS</label>
        <input type="text" name="nis" id="nis" class="form-control"
            value="<?= htmlspecialchars($siswa['nis'] ?? '') ?>" required>

        <label for="nama">Nama</label>
        <input type="text" name="nama" id="nama" class="form-control"
            value="<?= htmlspecialchars($siswa['nama'] ?? '') ?>" required>

        <label for="id_kelas">Kelas</label>
        <select name="id_kelas" id="id_kelas" class="form-control" required>
            <option value="">-- Pilih Kelas --</option>
            <?php foreach ($kelas as $k): ?>
                <option value="<?= $k['id_kelas'] ?>" <?= ($siswa && $siswa['id_kelas'] == $k['id_kelas']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($k['nama_kelas']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label for="id_spp">SPP</label>
        <select name="id_spp" id="id_spp" class="form-control" required>
            <option value="">-- Pilih SPP --</option>
            <?php foreach ($spp as $sp): ?>
                <option value="<?= $sp['id_spp'] ?>" <?= ($siswa && $siswa['id_spp'] == $sp['id_spp']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($sp['tahun']) ?> - Rp<?= number_format($sp['nominal'], 0, ',', '.') ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label for="alamat">Alamat</label>
        <input type="text" name="alamat" id="alamat" class="form-control"
            value="<?= htmlspecialchars($siswa['alamat'] ?? '') ?>">

        <label for="no_telp">No. Telepon</label>
        <input type="text" name="no_telp" id="no_telp" class="form-control"
            value="<?= htmlspecialchars($siswa['no_telp'] ?? '') ?>">

        <button type="submit" class="btn btn-success">Simpan</button>
        <a href="siswa.php" class="btn btn-secondary">Batal</a>
    </form>
</div>

<?php include "../layout/footer.php"; ?>