<?php
require '../config.php';
cekLogin();

if ($_SESSION['level'] !== 'admin') {
    exit('Tidak diizinkan.');
}

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

$where = "";
if (count($whereParts) > 0) {
    $where = "WHERE " . implode(" AND ", $whereParts);
}

$q = mysqli_query($conn, "SELECT * FROM pegawai $where ORDER BY nama ASC");

header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=laporan-pegawai-" . date('Ymd') . ".xls");
header("Pragma: no-cache");
header("Expires: 0");
?>
<table border="1" cellpadding="4" cellspacing="0">
    <tr>
        <th colspan="6">
            Laporan Data Pegawai DKP3 Banjarbaru
            <?php if (!empty($judulTambahan)) echo ' - ' . implode(' | ', $judulTambahan); ?>
        </th>
    </tr>
    <tr>
        <th>No</th>
        <th>NIP</th>
        <th>Nama</th>
        <th>Jabatan</th>
        <th>Unit Kerja</th>
        <th>Status</th>
    </tr>
    <?php $no=1; while($row = mysqli_fetch_assoc($q)) : ?>
        <tr>
            <td><?php echo $no++; ?></td>
            <td><?php echo $row['nip']; ?></td>
            <td><?php echo $row['nama']; ?></td>
            <td><?php echo $row['jabatan']; ?></td>
            <td><?php echo $row['unit_kerja']; ?></td>
            <td><?php echo $row['status_pegawai']; ?></td>
        </tr>
    <?php endwhile; ?>
    <?php if ($no === 1): ?>
        <tr><td colspan="6">Tidak ada data.</td></tr>
    <?php endif; ?>
</table>
