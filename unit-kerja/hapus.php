<?php
require '../config.php';
cekLogin();

if ($_SESSION['level'] !== 'admin') {
    header("Location: ../profil.php");
    exit;
}

$id = $_GET['id'];
mysqli_query($conn, "DELETE FROM unit_kerja WHERE id=$id");
header("Location: index.php");
exit;
?>
