<?php
session_start();
require "../config/database.php";
include "../layout/header.php";
include "../layout/sidebar.php";

$db = $pdo;

if (!isset($_SESSION['user']) || !in_array($_SESSION['user']['role'], ['admin', 'petugas'])) {
    header("Location: ../auth/login.php");
    exit;
}

// === Simpan pembayaran ===
if (isset($_POST['simpan'])) {
    $id_siswa     = $_POST['id_siswa'];
    $id_spp       = $_POST['id_spp'];
    $tgl_bayar    = $_POST['tgl_bayar'];
    $jumlah_bayar = $_POST['jumlah_bayar'];
    $id_petugas   = $_SESSION['user']['id_user'];

    $bulan_dibayar = date("F", strtotime($tgl_bayar));
    $tahun_dibayar = date("Y", strtotime($tgl_bayar));

    $stmt = $db->prepare("INSERT INTO pembayaran
        (id_petugas, id_siswa, id_spp, tgl_bayar, bulan_dibayar, tahun_dibayar, jumlah_bayar)
        VALUES (?,?,?,?,?,?,?)");
    $stmt->execute([$id_petugas, $id_siswa, $id_spp, $tgl_bayar, $bulan_dibayar, $tahun_dibayar, $jumlah_bayar]);

    $_SESSION['flash'] = ['type' => 'success', 'msg' => '✅ Pembayaran berhasil disimpan!'];
    header("Location: pembayaran.php");
    exit;
}
?>

<div class="main-content">
    <h2>Entry Pembayaran</h2>

    <!-- Form entry -->
    <form method="post" class="mb-4">
        <label>Siswa</label>
        <select name="id_siswa" class="form-control" required>
            <option value="">-- Pilih Siswa --</option>
            <?php
            $siswa = $db->query("SELECT * FROM siswa ORDER BY nama ASC")->fetchAll(PDO::FETCH_ASSOC);
            foreach ($siswa as $s) {
                echo "<option value='{$s['id_siswa']}'>" . htmlspecialchars($s['nama']) . "</option>";
            }
            ?>
        </select>

        <label>SPP</label>
        <select name="id_spp" class="form-control" required>
            <option value="">-- Pilih SPP --</option>
            <?php
            $spp = $db->query("SELECT * FROM spp ORDER BY tahun DESC")->fetchAll(PDO::FETCH_ASSOC);
            foreach ($spp as $sp) {
                echo "<option value='{$sp['id_spp']}'>{$sp['tahun']} - Rp" . number_format($sp['nominal'], 0, ',', '.') . "</option>";
            }
            ?>
        </select>

        <label>Tanggal Bayar</label>
        <input type="date" name="tgl_bayar" class="form-control" required>

        <label>Jumlah Bayar</label>
        <input type="number" name="jumlah_bayar" class="form-control" required>

        <button type="submit" name="simpan" class="btn btn-success mt-3">Simpan</button>
    </form>


    </script>