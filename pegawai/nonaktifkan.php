<?php
require_once __DIR__ . '/../config.php';
cekLogin();
if ($_SESSION['level'] !== 'admin') { header('Location: ../profil.php'); exit; }

$id = (int)($_GET['id'] ?? 0);
if ($id < 1) { header('Location: index.php'); exit; }

// ambil nama utk log (opsional)
$peg = mysqli_fetch_assoc(mysqli_query($conn, "SELECT nama FROM pegawai WHERE id=$id"));
mysqli_query($conn, "UPDATE pegawai SET aktif=0 WHERE id=$id");

tulisLog($conn, 'nonaktifkan', 'Nonaktifkan pegawai: '.($peg['nama'] ?? ('ID '.$id)));
header('Location: index.php?msg=nonaktif-ok');
exit;
