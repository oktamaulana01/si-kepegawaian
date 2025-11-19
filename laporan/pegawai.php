<?php
require '../config.php';
require __DIR__ . '/../vendor/autoload.php';

use Dompdf\Dompdf;
use Dompdf\Options;

// siapkan filter
$whereParts = [];
$judulTambahan = [];

// filter unit
if (!empty($_GET['unit'])) {
    $unit = mysqli_real_escape_string($conn, $_GET['unit']);
    $whereParts[] = "unit_kerja = '$unit'";
    $judulTambahan[] = "Unit: " . $_GET['unit'];
}

// filter aktif
if (isset($_GET['aktif']) && $_GET['aktif'] === '1') {
    $whereParts[] = "aktif = 1";
    $judulTambahan[] = "Pegawai Aktif";
}

// gabung where
$where = "";
if (count($whereParts) > 0) {
    $where = "WHERE " . implode(" AND ", $whereParts);
}

$q = mysqli_query($conn, "SELECT * FROM pegawai $where ORDER BY nama ASC");

// generate html
ob_start();
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Laporan Data Pegawai</title>
    <style>
        body { font-family: sans-serif; font-size: 11px; }
        .judul { text-align: center; }
        .judul h2 { margin: 0; }
        .judul h3 { margin: 3px 0 10px 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #000; padding: 4px; }
        th { background: #eee; }
        .ttd { width: 200px; float: right; text-align: center; margin-top: 30px; }
    </style>
</head>
<body>
    <div class="judul">
        <h2>DINAS KETAHANAN PANGAN, PERTANIAN DAN PERIKANAN</h2>
        <h3>KOTA BANJARBARU</h3>
        <span>Laporan Data Pegawai</span>
        <?php if (!empty($judulTambahan)): ?>
            <div><small><?php echo implode(' | ', $judulTambahan); ?></small></div>
        <?php endif; ?>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width:30px;">No</th>
                <th style="width:110px;">NIP</th>
                <th>Nama</th>
                <th>Jabatan</th>
                <th>Unit Kerja</th>
                <th style="width:70px;">Status</th>
            </tr>
        </thead>
        <tbody>
            <?php $no=1; while($row = mysqli_fetch_assoc($q)) : ?>
            <tr>
                <td style="text-align:center;"><?php echo $no++; ?></td>
                <td><?php echo $row['nip']; ?></td>
                <td><?php echo $row['nama']; ?></td>
                <td><?php echo $row['jabatan']; ?></td>
                <td><?php echo $row['unit_kerja']; ?></td>
                <td><?php echo $row['status_pegawai']; ?></td>
            </tr>
            <?php endwhile; ?>
            <?php if ($no === 1): ?>
            <tr>
                <td colspan="6" style="text-align:center;">Tidak ada data.</td>
            </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <div class="ttd">
        Banjarbaru, <?php echo date('d-m-Y'); ?><br>
        Bagian Umum dan Kepegawaian<br><br><br><br>
        _____________________
    </div>
</body>
</html>
<?php
$html = ob_get_clean();

// render pdf
$options = new Options();
$options->set('isRemoteEnabled', true);
$dompdf = new Dompdf($options);
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();
$dompdf->stream("laporan-pegawai.pdf", ["Attachment" => false]);
