<?php
require '../config.php';
cekLogin();

if ($_SESSION['level'] !== 'admin') {
    header("Location: ../profil.php");
    exit;
}

$id = $_GET['id'];
$q  = mysqli_query($conn, "SELECT * FROM jabatan WHERE id=$id");
$data = mysqli_fetch_assoc($q);

if (isset($_POST['update'])) {
    $nama = mysqli_real_escape_string($conn, $_POST['nama_jabatan']);
    mysqli_query($conn, "UPDATE jabatan SET nama_jabatan='$nama' WHERE id=$id");
    header("Location: index.php");
    exit;
}
?>
<?php include '../templates/header.php'; ?>

<h1 class="text-xl font-bold mb-4">Edit Jabatan</h1>

<form method="post" class="bg-white p-4 rounded shadow max-w-md">
    <label class="block mb-2 text-sm">Nama Jabatan</label>
    <input type="text" name="nama_jabatan" class="border p-2 w-full mb-4" value="<?php echo $data['nama_jabatan']; ?>" required>
    <button name="update" class="bg-blue-600 text-white px-4 py-2 rounded">Update</button>
    <a href="index.php" class="ml-2 text-slate-600">Batal</a>
</form>

<?php include '../templates/footer.php'; ?>
