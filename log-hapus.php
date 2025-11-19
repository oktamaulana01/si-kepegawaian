<?php
require 'config.php';
cekLogin();

// cuma admin
if ($_SESSION['level'] !== 'admin') {
    header("Location: profil.php");
    exit;
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id > 0) {
    // ambil dulu untuk keterangan log
    $q = mysqli_query($conn, "SELECT * FROM log_aktivitas WHERE id = $id");
    $row = mysqli_fetch_assoc($q);

    mysqli_query($conn, "DELETE FROM log_aktivitas WHERE id = $id");

    // catat di log juga kalau mau
    tulisLog($conn, 'hapus', 'Hapus log aktivitas: ' . ($row['keterangan'] ?? 'ID ' . $id));
}

header("Location: log.php");
exit;
