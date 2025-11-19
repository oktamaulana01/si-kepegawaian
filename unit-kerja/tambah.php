<?php
require '../config.php';
cekLogin();

if ($_SESSION['level'] !== 'admin') {
    header("Location: ../profil.php");
    exit;
}

if (isset($_POST['simpan'])) {
    $nama = mysqli_real_escape_string($conn, $_POST['nama_unit']);
    mysqli_query($conn, "INSERT INTO unit_kerja (nama_unit) VALUES ('$nama')");
    header("Location: index.php");
    exit;
}
?>
<?php include '../templates/header.php'; ?>

<h1 class="text-xl font-bold mb-4">Tambah Unit Kerja</h1>

<form method="post" class="bg-white p-4 rounded shadow max-w-md">
    <label class="block mb-2 text-sm">Nama Unit Kerja</label>
    <input type="text" name="nama_unit" class="border p-2 w-full mb-4" required>
    <button name="simpan" class="bg-blue-600 text-white px-4 py-2 rounded">Simpan</button>
    <a href="index.php" class="ml-2 text-slate-600">Batal</a>
</form>

<?php include '../templates/footer.php'; ?>
