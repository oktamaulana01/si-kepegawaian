<?php
require_once __DIR__ . '/../config.php';
cekLogin();
if ($_SESSION['level'] !== 'admin') { 
    header('Location: ../profil.php'); 
    exit; 
}

$id = (int)($_GET['id'] ?? 0);
if ($id < 1) { 
    header('Location: index.php'); 
    exit; 
}

// ambil data pegawai (untuk nama & foto)
$peg = mysqli_fetch_assoc(mysqli_query($conn, "SELECT nama,foto FROM pegawai WHERE id=$id"));

// hapus file foto jika ada
if (!empty($peg['foto'])) {
    $path = __DIR__ . '/../uploads/pegawai/' . $peg['foto'];
    if (is_file($path)) {
        @unlink($path);
    }
}

/* ===========================
   HAPUS USER YANG TERKAIT
   =========================== */
mysqli_query($conn, "DELETE FROM users WHERE pegawai_id = $id");

// hapus data pegawai
mysqli_query($conn, "DELETE FROM pegawai WHERE id=$id");

tulisLog($conn, 'hapus', 'Hapus pegawai: '.($peg['nama'] ?? ('ID '.$id)));
header('Location: index.php?msg=hapus-ok');
exit;
