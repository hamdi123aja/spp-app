<?php
session_start();
require "../config/database.php";
include "../layout/header.php";
include "../layout/sidebar.php";

$db = $pdo;

// Cek apakah parameter 'id' ada di URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo "<div class='main-content'><h3>Data tidak ditemukan. Kembali ke <a href='riwayat.php'>Riwayat Pembayaran</a>.</h3></div>";
    include "../layout/footer.php";
    exit;
}

$id_pembayaran = $_GET['id'];

// Ambil data pembayaran, siswa, petugas, dan kelas
$stmt = $db->prepare("SELECT p.*, s.nama AS nama_siswa, s.nisn, k.nama_kelas, u.nama AS nama_petugas
                    FROM pembayaran p
                    JOIN siswa s ON p.id_siswa = s.id_siswa
                    JOIN users u ON p.id_petugas = u.id_user
                    JOIN kelas k ON s.id_kelas = k.id_kelas
                    WHERE p.id_pembayaran = ?");
$stmt->execute([$id_pembayaran]);
$pembayaran = $stmt->fetch(PDO::FETCH_ASSOC);

// Jika data tidak ditemukan
if (!$pembayaran) {
    echo "<div class='main-content'><h3>Data pembayaran tidak ditemukan. Kembali ke <a href='riwayat.php'>Riwayat Pembayaran</a>.</h3></div>";
    include "../layout/footer.php";
    exit;
}
?>

<div class="main-content">
    <div class="container-fluid">
        <h3 class="mb-4">Detail Pembayaran</h3>

        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                Informasi Pembayaran
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>ID Pembayaran:</strong> <?= htmlspecialchars($pembayaran['id_pembayaran']) ?></p>
                        <p><strong>Tanggal Bayar:</strong> <?= htmlspecialchars($pembayaran['tgl_bayar']) ?></p>
                        <p><strong>Bulan Dibayar:</strong> <?= htmlspecialchars($pembayaran['bulan_dibayar']) ?></p>
                        <p><strong>Tahun Dibayar:</strong> <?= htmlspecialchars($pembayaran['tahun_dibayar']) ?></p>
                        <p><strong>Jumlah Bayar:</strong> Rp<?= number_format($pembayaran['jumlah_bayar'], 0, ',', '.') ?></p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Nama Petugas:</strong> <?= htmlspecialchars($pembayaran['nama_petugas']) ?></p>
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow-sm mt-4">
            <div class="card-header bg-info text-white">
                Informasi Siswa
            </div>
            <div class="card-body">
                <p><strong>Nama Siswa:</strong> <?= htmlspecialchars($pembayaran['nama_siswa']) ?></p>
                <p><strong>NISN:</strong> <?= htmlspecialchars($pembayaran['nisn']) ?></p>
                <p><strong>Kelas:</strong> <?= htmlspecialchars($pembayaran['nama_kelas']) ?></p>
            </div>
        </div>

        <div class="mt-4">
            <a href="history.php" class="btn btn-secondary">Kembali</a>
        </div>
    </div>
</div>

<?php include "../layout/footer.php"; ?>