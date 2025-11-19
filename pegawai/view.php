<?php
// pegawai/view.php
require '../config.php';
cekLogin();

// Batasi admin saja (kalau mau pegawai juga boleh, tinggal ubah)
if ($_SESSION['level'] !== 'admin') {
    header("Location: ../profil.php");
    exit;
}

// Ambil ID dengan aman
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    // id tidak valid -> balik ke daftar pegawai
    header("Location: index.php?msg=invalid-id");
    exit;
}

// Ambil data pegawai
$pegawai = null;
$stmt = mysqli_prepare($conn, "SELECT * FROM pegawai WHERE id = ? LIMIT 1");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);
$pegawai = mysqli_fetch_assoc($res);
mysqli_stmt_close($stmt);

// Jika tidak ada datanya, redirect balik
if (!$pegawai) {
    header("Location: index.php?msg=not-found");
    exit;
}

// Cek apakah tabel users punya kolom password_raw
$hasPasswordRaw = false;
$cek = mysqli_query($conn, "SHOW COLUMNS FROM users LIKE 'password_raw'");
if ($cek && mysqli_num_rows($cek) > 0) {
    $hasPasswordRaw = true;
}

// Ambil akun yang terhubung ke pegawai (jika ada)
$akun = null;
if ($hasPasswordRaw) {
    $sqlUser = "SELECT id, username, level, password AS password_hash, password_raw FROM users WHERE pegawai_id = ? LIMIT 1";
} else {
    $sqlUser = "SELECT id, username, level, password AS password_hash FROM users WHERE pegawai_id = ? LIMIT 1";
}
$stmt2 = mysqli_prepare($conn, $sqlUser);
mysqli_stmt_bind_param($stmt2, "i", $id);
mysqli_stmt_execute($stmt2);
$res2 = mysqli_stmt_get_result($stmt2);
$akun = mysqli_fetch_assoc($res2);
mysqli_stmt_close($stmt2);

include '../templates/header.php';
?>

<div class="mb-6 flex items-center justify-between">
    <h1 class="text-2xl font-bold">Detail Pegawai</h1>
    <div class="flex gap-2">
        <a href="index.php" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 rounded">Kembali</a>

        <?php if ((int)$pegawai['aktif'] === 1): ?>
            <a href="nonaktifkan.php?id=<?php echo $pegawai['id']; ?>"
               onclick="return confirm('Nonaktifkan pegawai ini?')"
               class="px-4 py-2 bg-orange-500 hover:bg-orange-600 text-white rounded">Nonaktifkan</a>
        <?php else: ?>
            <a href="aktifkan.php?id=<?php echo $pegawai['id']; ?>"
               onclick="return confirm('Aktifkan kembali pegawai ini?')"
               class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded">Aktifkan</a>
        <?php endif; ?>

        <a href="edit.php?id=<?php echo $pegawai['id']; ?>"
           class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded">Edit</a>

        <a href="hapus.php?id=<?php echo $pegawai['id']; ?>"
           onclick="return confirm('Hapus PERMANEN pegawai ini? Data tidak bisa dikembalikan!')"
           class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded">Hapus</a>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    <!-- Kartu Foto -->
    <div class="bg-white rounded shadow p-4">
        <h2 class="font-semibold mb-3">Foto</h2>
        <?php if (!empty($pegawai['foto'])): ?>
            <img src="../uploads/pegawai/<?php echo htmlspecialchars($pegawai['foto']); ?>"
                 class="w-40 h-40 object-cover rounded border" alt="Foto Pegawai">
        <?php else: ?>
            <div class="w-40 h-40 bg-slate-200 rounded flex items-center justify-center text-slate-500">
                Tidak ada foto
            </div>
        <?php endif; ?>
    </div>

    <!-- Kartu Data Pegawai -->
    <div class="bg-white rounded shadow p-4 md:col-span-2">
        <h2 class="font-semibold mb-3">Data Pegawai</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
            <div>
                <p class="text-slate-500 text-sm">NIP</p>
                <p class="font-medium"><?php echo htmlspecialchars($pegawai['nip']); ?></p>
            </div>
            <div>
                <p class="text-slate-500 text-sm">Nama</p>
                <p class="font-medium"><?php echo htmlspecialchars($pegawai['nama']); ?></p>
            </div>
            <div>
                <p class="text-slate-500 text-sm">Jabatan</p>
                <p class="font-medium"><?php echo htmlspecialchars($pegawai['jabatan']); ?></p>
            </div>
            <div>
                <p class="text-slate-500 text-sm">Unit Kerja</p>
                <p class="font-medium"><?php echo htmlspecialchars($pegawai['unit_kerja']); ?></p>
            </div>
            <div>
                <p class="text-slate-500 text-sm">Status Pegawai</p>
                <p class="font-medium"><?php echo htmlspecialchars($pegawai['status_pegawai']); ?></p>
            </div>
            <div>
                <p class="text-slate-500 text-sm">Status Data</p>
                <p class="font-medium">
                    <?php echo ((int)$pegawai['aktif'] === 1) ? 'Aktif' : 'Nonaktif'; ?>
                </p>
            </div>
            <div>
                <p class="text-slate-500 text-sm">No HP</p>
                <p class="font-medium"><?php echo htmlspecialchars($pegawai['no_hp']); ?></p>
            </div>
        </div>
    </div>
</div>

<!-- Akun yang Terhubung -->
<div class="bg-white rounded shadow p-4 mt-6">
    <h2 class="font-semibold mb-3">Akun Aplikasi</h2>
    <?php if ($akun): ?>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
            <div>
                <p class="text-slate-500 text-sm">Username</p>
                <p class="font-medium"><?php echo htmlspecialchars($akun['username']); ?></p>
            </div>
            <div>
                <p class="text-slate-500 text-sm">Level</p>
                <p class="font-medium capitalize"><?php echo htmlspecialchars($akun['level']); ?></p>
            </div>
            <div>
                <p class="text-slate-500 text-sm">Password (hash)</p>
                <p class="font-medium break-all">
                    <?php echo htmlspecialchars($akun['password_hash']); ?>
                </p>
            </div>

            <div class="md:col-span-3">
                <p class="text-slate-500 text-sm">Password Asli</p>
                <p class="font-medium">
                    <?php
                    if ($hasPasswordRaw && isset($akun['password_raw']) && $akun['password_raw'] !== '') {
                        echo htmlspecialchars($akun['password_raw']);
                    } else {
                        echo '<span class="text-slate-500">Tidak disimpan (demi keamanan)</span>';
                    }
                    ?>
                </p>
            </div>
        </div>
    <?php else: ?>
        <p class="text-slate-500 text-sm">Belum ada akun yang terhubung ke pegawai ini.</p>
    <?php endif; ?>
</div>

<?php include '../templates/footer.php'; ?>
