<?php
session_start();
require "../config/database.php";
include "../layout/header.php";
include "../layout/sidebar.php";

$db = $pdo;

// Logika untuk menghapus data pembayaran
if (isset($_GET['hapus'])) {
    $id_pembayaran = $_GET['hapus'];
    try {
        // Hapus data pembayaran dari database
        $stmt_hapus = $db->prepare("DELETE FROM pembayaran WHERE id_pembayaran = ?");
        $stmt_hapus->execute([$id_pembayaran]);

        // Set notifikasi sukses
        $_SESSION['success'] = "Data pembayaran berhasil dihapus!";
    } catch (PDOException $e) {
        // Set notifikasi error jika gagal
        $_SESSION['error'] = "Gagal menghapus data: " . $e->getMessage();
    }
    // Redirect kembali ke halaman ini untuk mencegah resubmit
    header("Location: history.php");
    exit;
}

// Notifikasi (misalnya dari halaman lain atau setelah hapus)
$message = "";
if (isset($_SESSION['success'])) {
    $message = "<div class='alert alert-success'>" . $_SESSION['success'] . "</div>";
    unset($_SESSION['success']);
}
if (isset($_SESSION['error'])) {
    $message = "<div class='alert alert-danger'>" . $_SESSION['error'] . "</div>";
    unset($_SESSION['error']);
}

// Ambil semua data riwayat pembayaran dari database
$stmt = $db->query("SELECT p.*, s.nama AS nama_siswa, u.nama AS nama_petugas
                    FROM pembayaran p
                    JOIN siswa s ON p.id_siswa=s.id_siswa
                    JOIN users u ON p.id_petugas=u.id_user
                    ORDER BY p.id_pembayaran DESC");

?>

<div class="main-content">
    <?= $message ?>
    <h3>Riwayat Pembayaran</h3>
    <div class="table-responsive">
        <table class="table table-bordered" id="historyTable">
            <thead class="table-dark">
                <tr>
                    <th>No</th>
                    <th>Nama Siswa</th>
                    <th>Tanggal Bayar</th>
                    <th>Bulan</th>
                    <th>Tahun</th>
                    <th>Jumlah</th>
                    <th>Petugas</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $no = 1;
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    echo "<tr>
                            <td>" . $no++ . "</td>
                            <td>" . htmlspecialchars($row['nama_siswa']) . "</td>
                            <td>" . htmlspecialchars($row['tgl_bayar']) . "</td>
                            <td>" . htmlspecialchars($row['bulan_dibayar']) . "</td>
                            <td>" . htmlspecialchars($row['tahun_dibayar']) . "</td>
                            <td>Rp" . number_format($row['jumlah_bayar'], 0, ',', '.') . "</td>
                            <td>" . htmlspecialchars($row['nama_petugas']) . "</td>
                            <td>
                                <a href='detail_pembayaran.php?id=" . $row['id_pembayaran'] . "' class='btn btn-sm btn-info'>Detail</a>
                                <a href='history.php?hapus=" . $row['id_pembayaran'] . "' class='btn btn-sm btn-danger' onclick='return confirm(\"Yakin ingin menghapus data ini?\")'>Hapus</a>
                            </td>
                          </tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script>
    $(document).ready(function() {
        $('#historyTable').DataTable({
            "pageLength": 5, // default 5 baris
            "lengthMenu": [5, 10, 25, 50], // pilihan show entries
            "ordering": true,
            "responsive": true,
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.13.6/i18n/id.json"
            }
        });
    });
</script>

<?php include "../layout/footer.php"; ?>