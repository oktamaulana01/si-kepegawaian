<?php
require_once __DIR__ . '/../config.php';
cekLogin();

if ($_SESSION['level'] !== 'admin') {
    header("Location: ../profil.php");
    exit;
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$q  = mysqli_query($conn, "SELECT * FROM pegawai WHERE id = $id LIMIT 1");
$data = mysqli_fetch_assoc($q);
if (!$data) {
    die("Data pegawai tidak ditemukan");
}

// ambil referensi
$jabatan = mysqli_query($conn, "SELECT * FROM jabatan ORDER BY nama_jabatan ASC");
$unit    = mysqli_query($conn, "SELECT * FROM unit_kerja ORDER BY nama_unit ASC");

if (isset($_POST['update'])) {
    $nip    = mysqli_real_escape_string($conn, $_POST['nip']);
    $nama   = mysqli_real_escape_string($conn, $_POST['nama']);
    $jab    = mysqli_real_escape_string($conn, $_POST['jabatan']);
    $unitk  = mysqli_real_escape_string($conn, $_POST['unit_kerja']);
    $status = mysqli_real_escape_string($conn, $_POST['status_pegawai']);
    $nohp   = mysqli_real_escape_string($conn, $_POST['no_hp']);

    $namaFileBaru = $data['foto']; // pakai foto lama

    // jika upload foto baru
    if (!empty($_FILES['foto']['name'])) {
        $allowed = ['jpg','jpeg','png'];
        $maxSize = 2 * 1024 * 1024;

        $origName = $_FILES['foto']['name'];
        $size     = $_FILES['foto']['size'];
        $tmp      = $_FILES['foto']['tmp_name'];
        $ext      = strtolower(pathinfo($origName, PATHINFO_EXTENSION));

        if (!in_array($ext, $allowed)) {
            die("Format foto harus JPG/PNG");
        }
        if ($size > $maxSize) {
            die("Ukuran foto maksimal 2MB");
        }

        $namaFileBaru = 'pegawai-' . time() . '.' . $ext;
        $tujuan = __DIR__ . '/../uploads/pegawai/' . $namaFileBaru;
        move_uploaded_file($tmp, $tujuan);

        // hapus foto lama
        if (!empty($data['foto'])) {
            $lama = __DIR__ . '/../uploads/pegawai/' . $data['foto'];
            if (file_exists($lama)) {
                unlink($lama);
            }
        }
    }

    $sql_update = "UPDATE pegawai SET
                    nip = '$nip',
                    nama = '$nama',
                    jabatan = '$jab',
                    unit_kerja = '$unitk',
                    status_pegawai = '$status',
                    no_hp = '$nohp',
                    foto = '$namaFileBaru'
                   WHERE id = $id";
    mysqli_query($conn, $sql_update);

    tulisLog($conn, 'edit', "Edit pegawai: $nama (ID: $id)");

    header("Location: index.php");
    exit;
}

include '../templates/header.php';
?>

<h1 class="text-xl font-bold mb-4">Edit Pegawai</h1>

<form method="post" enctype="multipart/form-data" class="bg-white p-4 rounded shadow max-w-lg">
    <label class="block mb-2 text-sm">NIP</label>
    <input type="text" name="nip" class="border p-2 w-full mb-3" value="<?php echo $data['nip']; ?>">

    <label class="block mb-2 text-sm">Nama</label>
    <input type="text" name="nama" class="border p-2 w-full mb-3" value="<?php echo $data['nama']; ?>" required>

    <label class="block mb-2 text-sm">Jabatan</label>
    <select name="jabatan" class="border p-2 w-full mb-3">
        <option value="">-- Pilih --</option>
        <?php while($j = mysqli_fetch_assoc($jabatan)) : ?>
            <option value="<?php echo $j['nama_jabatan']; ?>" <?php if ($j['nama_jabatan'] == $data['jabatan']) echo 'selected'; ?>>
                <?php echo $j['nama_jabatan']; ?>
            </option>
        <?php endwhile; ?>
    </select>

    <label class="block mb-2 text-sm">Unit Kerja</label>
    <select name="unit_kerja" class="border p-2 w-full mb-3">
        <option value="">-- Pilih --</option>
        <?php while($u = mysqli_fetch_assoc($unit)) : ?>
            <option value="<?php echo $u['nama_unit']; ?>" <?php if ($u['nama_unit'] == $data['unit_kerja']) echo 'selected'; ?>>
                <?php echo $u['nama_unit']; ?>
            </option>
        <?php endwhile; ?>
    </select>

    <label class="block mb-2 text-sm">Status Pegawai</label>
    <input type="text" name="status_pegawai" class="border p-2 w-full mb-3" value="<?php echo $data['status_pegawai']; ?>">

    <label class="block mb-2 text-sm">No HP</label>
    <input type="text" name="no_hp" class="border p-2 w-full mb-3" value="<?php echo $data['no_hp']; ?>">

    <label class="block mb-2 text-sm">Foto (kosongkan jika tidak diganti)</label>
    <input type="file" name="foto" class="border p-2 w-full mb-4" accept="image/*">
    <?php if (!empty($data['foto'])): ?>
        <img src="../uploads/pegawai/<?php echo $data['foto']; ?>" class="h-20 mb-3 rounded" alt="">
    <?php endif; ?>

    <button name="update" class="bg-blue-600 text-white px-4 py-2 rounded">Update</button>
    <a href="index.php" class="ml-2 text-slate-500">Batal</a>
</form>

<?php include '../templates/footer.php'; ?>
