<?php
require_once __DIR__ . '/../config.php';
cekLogin();

// hanya admin
if ($_SESSION['level'] !== 'admin') {
    header("Location: ../profil.php");
    exit;
}

// cek parameter konfirmasi biar gak ke-klik sembarangan
$konfirmasi = $_GET['konfirmasi'] ?? '';

if ($konfirmasi !== 'YA') {
    // kalau belum konfirmasi, balik saja
    header("Location: index.php");
    exit;
}

// SOFT DELETE SEMUA PEGAWAI
mysqli_query($conn, "UPDATE pegawai SET aktif = 0");

// catat log
tulisLog($conn, 'nonaktif-massal', 'Menonaktifkan semua data pegawai');

// kalau mau HARD DELETE (hapus barisnya), ganti baris di atas jadi:
// mysqli_query($conn, "TRUNCATE TABLE pegawai");

header("Location: index.php");
exit;
