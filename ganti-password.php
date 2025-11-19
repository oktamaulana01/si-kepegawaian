<?php
require 'config.php';
cekLogin();

$username = $_SESSION['username'];
$level    = $_SESSION['level'];
$pesan    = '';

if (isset($_POST['simpan'])) {
    $pass1 = $_POST['password_baru'] ?? '';
    $pass2 = $_POST['konfirmasi_password'] ?? '';

    if ($pass1 === '' || $pass2 === '') {
        $pesan = 'Password tidak boleh kosong.';
    } elseif ($pass1 !== $pass2) {
        $pesan = 'Konfirmasi password tidak sama.';
    } else {
        // pakai md5 karena login kamu tadi juga pakai md5
        $passHash = md5($pass1);

        $sql = "UPDATE users SET password = '$passHash' WHERE username = '$username'";
        mysqli_query($conn, $sql);

        // ⬇️ ini yang bikin muncul di log
        tulisLog($conn, 'ganti-password', 'User ganti password: ' . $username);

        $pesan = 'Password berhasil diganti.';
    }
}

include 'templates/header.php';
?>

<h1 class="text-2xl font-bold mb-4">Ganti Password</h1>

<?php if ($pesan): ?>
    <div class="mb-4 bg-green-100 text-green-800 px-4 py-2 rounded">
        <?php echo htmlspecialchars($pesan); ?>
    </div>
<?php endif; ?>

<form method="post" class="bg-white p-4 rounded shadow max-w-lg">
    <label class="block mb-2 text-sm">Password Baru</label>
    <input type="password" name="password_baru" class="border p-2 w-full mb-3" required>

    <label class="block mb-2 text-sm">Konfirmasi Password</label>
    <input type="password" name="konfirmasi_password" class="border p-2 w-full mb-4" required>

    <button name="simpan" class="bg-blue-600 text-white px-4 py-2 rounded">Simpan</button>
</form>

<?php include 'templates/footer.php'; ?>
