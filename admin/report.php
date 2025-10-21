<?php
require "../config/database.php";
include "../layout/header.php";
include "../layout/sidebar.php";

$db = $pdo;
$bulan = $_POST['bulan'] ?? date('m');
$tahun = $_POST['tahun'] ?? date('Y');

$laporan = [];

// Query utama: data terbaru di atas
$stmt = $db->prepare("SELECT p.*, s.nama AS nama_siswa, s.nisn, k.nama_kelas, 
                             u.nama AS nama_petugas, sp.nominal 
        FROM pembayaran p
        JOIN siswa s ON p.id_siswa=s.id_siswa
        JOIN kelas k ON s.id_kelas=k.id_kelas
        JOIN spp sp ON p.id_spp=sp.id_spp
        JOIN users u ON p.id_petugas=u.id_user
        WHERE MONTH(p.tgl_bayar)=? AND YEAR(p.tgl_bayar)=?
        ORDER BY p.id_pembayaran DESC");
$stmt->execute([$bulan, $tahun]);
$laporan = $stmt->fetchAll(PDO::FETCH_ASSOC);

// === Export CSV ===
if (isset($_POST['export_csv'])) {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment;filename="laporan_' . $bulan . '_' . $tahun . '.csv"');
    $output = fopen("php://output", "w");
    fputcsv($output, ['No', 'Tanggal', 'NISN', 'Nama Siswa', 'Kelas', 'Petugas', 'SPP', 'Jumlah Bayar', 'Bulan Dibayar', 'Tahun Dibayar']);
    $no = 1;
    foreach ($laporan as $row) {
        fputcsv($output, [
            $no++,
            $row['tgl_bayar'],
            $row['nisn'],
            $row['nama_siswa'],
            $row['nama_kelas'],
            $row['nama_petugas'],
            $row['nominal'],
            $row['jumlah_bayar'],
            $row['bulan_dibayar'],
            $row['tahun_dibayar']
        ]);
    }
    fclose($output);
    exit;
}

// === Export Word ===
if (isset($_POST['export_word'])) {
    header("Content-type: application/vnd.ms-word");
    header("Content-Disposition: attachment;Filename=laporan_$bulan_$tahun.doc");
    echo "<h2>Laporan Pembayaran</h2>";
    echo "<table border='1' cellspacing='0' cellpadding='5'>";
    echo "<tr><th>No</th><th>Tanggal</th><th>NISN</th><th>Nama</th><th>Kelas</th><th>Petugas</th><th>SPP</th><th>Jumlah</th><th>Bulan</th><th>Tahun</th></tr>";
    $no = 1;
    foreach ($laporan as $row) {
        echo "<tr>
                <td>{$no}</td>
                <td>{$row['tgl_bayar']}</td>
                <td>{$row['nisn']}</td>
                <td>{$row['nama_siswa']}</td>
                <td>{$row['nama_kelas']}</td>
                <td>{$row['nama_petugas']}</td>
                <td>" . number_format($row['nominal'], 0, ',', '.') . "</td>
                <td>" . number_format($row['jumlah_bayar'], 0, ',', '.') . "</td>
                <td>{$row['bulan_dibayar']}</td>
                <td>{$row['tahun_dibayar']}</td>
              </tr>";
        $no++;
    }
    echo "</table>";
    exit;
}

// === Export PDF (pakai FPDF) ===
if (isset($_POST['export_pdf'])) {

    $pdf->AddPage();
    $pdf->SetFont('Arial', 'B', 14);
    $pdf->Cell(190, 10, "Laporan Pembayaran", 0, 1, 'C');
    $pdf->SetFont('Arial', '', 10);

    // Header tabel
    $pdf->Cell(10, 8, "No", 1);
    $pdf->Cell(25, 8, "Tanggal", 1);
    $pdf->Cell(25, 8, "NISN", 1);
    $pdf->Cell(35, 8, "Nama", 1);
    $pdf->Cell(25, 8, "Kelas", 1);
    $pdf->Cell(30, 8, "Petugas", 1);
    $pdf->Cell(20, 8, "Jumlah", 1);
    $pdf->Ln();

    $no = 1;
    foreach ($laporan as $row) {
        $pdf->Cell(10, 8, $no++, 1);
        $pdf->Cell(25, 8, $row['tgl_bayar'], 1);
        $pdf->Cell(25, 8, $row['nisn'], 1);
        $pdf->Cell(35, 8, $row['nama_siswa'], 1);
        $pdf->Cell(25, 8, $row['nama_kelas'], 1);
        $pdf->Cell(30, 8, $row['nama_petugas'], 1);
        $pdf->Cell(20, 8, $row['jumlah_bayar'], 1);
        $pdf->Ln();
    }
    $pdf->Output();
    exit;
}
?>

<div class="main-content">
    <h1 class="h3 mb-4 text-gray-800">Laporan Pembayaran</h1>

    <!-- Search & Export Card -->
    <div class="card shadow mb-4">
        <div class="card-body">
            <form method="post" class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label for="bulan">Bulan</label>
                    <select name="bulan" id="bulan" class="form-control">
                        <?php
                        $bulan_array = [
                            1 => 'Januari',
                            2 => 'Februari',
                            3 => 'Maret',
                            4 => 'April',
                            5 => 'Mei',
                            6 => 'Juni',
                            7 => 'Juli',
                            8 => 'Agustus',
                            9 => 'September',
                            10 => 'Oktober',
                            11 => 'November',
                            12 => 'Desember'
                        ];
                        foreach ($bulan_array as $key => $value) {
                            $selected = ($key == $bulan) ? 'selected' : '';
                            echo "<option value='{$key}' {$selected}>{$value}</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="tahun">Tahun</label>
                    <input type="number" name="tahun" id="tahun" class="form-control" value="<?= htmlspecialchars($tahun) ?>">
                </div>
                <div class="col-md-6">
                    <button type="submit" name="filter" class="btn btn-primary">Search</button>
                    <button type="submit" name="export_csv" class="btn btn-success">Export CSV</button>
                    <button type="submit" name="export_pdf" class="btn btn-danger">Export PDF</button>
                    <button type="submit" name="export_word" class="btn btn-info">Export Word</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Table -->
    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="laporanTable">
                    <thead class="table-dark">
                        <tr>
                            <th>No</th>
                            <th>Tanggal</th>
                            <th>NISN</th>
                            <th>Nama</th>
                            <th>Kelas</th>
                            <th>Petugas</th>
                            <th>SPP</th>
                            <th>Jumlah</th>
                            <th>Bulan</th>
                            <th>Tahun</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1;
                        foreach ($laporan as $row) : ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><?= htmlspecialchars($row['tgl_bayar']) ?></td>
                                <td><?= htmlspecialchars($row['nisn']) ?></td>
                                <td><?= htmlspecialchars($row['nama_siswa']) ?></td>
                                <td><?= htmlspecialchars($row['nama_kelas']) ?></td>
                                <td><?= htmlspecialchars($row['nama_petugas']) ?></td>
                                <td><?= number_format($row['nominal'], 0, ',', '.') ?></td>
                                <td><?= number_format($row['jumlah_bayar'], 0, ',', '.') ?></td>
                                <td><?= htmlspecialchars($row['bulan_dibayar']) ?></td>
                                <td><?= htmlspecialchars($row['tahun_dibayar']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- DataTables -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script>
    $(document).ready(function() {
        $('#laporanTable').DataTable({
            "pageLength": 5, // tampil 5 data
            "lengthMenu": [5, 10, 25, 50],
            "ordering": false, // urutan pakai query DESC
            "responsive": true,
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.13.6/i18n/id.json"
            }
        });
    });
</script>

<?php include "../layout/footer.php"; ?>