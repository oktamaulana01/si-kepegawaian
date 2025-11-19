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

<!-- Header dengan Gradient -->
<div class="mb-8">
    <div class="bg-gradient-to-r from-blue-600 to-indigo-600 rounded-xl shadow-lg p-6 text-white">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold mb-2">Detail Pegawai</h1>
                <p class="text-blue-100">Informasi lengkap data pegawai</p>
            </div>
            <div class="hidden md:block">
                <svg class="w-16 h-16 text-blue-400 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
            </div>
        </div>
    </div>
</div>

<!-- Action Buttons -->
<div class="mb-6 flex flex-wrap gap-3">
    <a href="index.php" class="inline-flex items-center px-5 py-2.5 bg-white hover:bg-slate-50 border-2 border-slate-200 rounded-lg font-medium text-slate-700 transition-all duration-200 shadow-sm hover:shadow">
        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
        </svg>
        Kembali
    </a>

    <?php if ((int)$pegawai['aktif'] === 1): ?>
        <a href="nonaktifkan.php?id=<?php echo $pegawai['id']; ?>"
           onclick="return confirm('Nonaktifkan pegawai ini?')"
           class="inline-flex items-center px-5 py-2.5 bg-gradient-to-r from-orange-500 to-orange-600 hover:from-orange-600 hover:to-orange-700 rounded-lg font-medium text-white transition-all duration-200 shadow-md hover:shadow-lg transform hover:-translate-y-0.5">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
            </svg>
            Nonaktifkan
        </a>
    <?php else: ?>
        <a href="aktifkan.php?id=<?php echo $pegawai['id']; ?>"
           onclick="return confirm('Aktifkan kembali pegawai ini?')"
           class="inline-flex items-center px-5 py-2.5 bg-gradient-to-r from-emerald-500 to-emerald-600 hover:from-emerald-600 hover:to-emerald-700 rounded-lg font-medium text-white transition-all duration-200 shadow-md hover:shadow-lg transform hover:-translate-y-0.5">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            Aktifkan
        </a>
    <?php endif; ?>

    <a href="edit.php?id=<?php echo $pegawai['id']; ?>"
       class="inline-flex items-center px-5 py-2.5 bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 rounded-lg font-medium text-white transition-all duration-200 shadow-md hover:shadow-lg transform hover:-translate-y-0.5">
        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
        </svg>
        Edit
    </a>

    <a href="hapus.php?id=<?php echo $pegawai['id']; ?>"
       onclick="return confirm('Hapus PERMANEN pegawai ini? Data tidak bisa dikembalikan!')"
       class="inline-flex items-center px-5 py-2.5 bg-gradient-to-r from-red-500 to-red-600 hover:from-red-600 hover:to-red-700 rounded-lg font-medium text-white transition-all duration-200 shadow-md hover:shadow-lg transform hover:-translate-y-0.5">
        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
        </svg>
        Hapus
    </a>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Kartu Foto -->
    <div class="bg-white rounded-xl shadow-lg p-6 border border-slate-100 hover:shadow-xl transition-shadow duration-200">
        <div class="flex items-center mb-4">
            <div class="p-2 bg-indigo-100 rounded-lg">
                <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
            </div>
            <h2 class="font-bold text-lg ml-3 text-slate-800">Foto Pegawai</h2>
        </div>
        <div class="flex justify-center">
            <?php if (!empty($pegawai['foto'])): ?>
                <img src="../uploads/pegawai/<?php echo htmlspecialchars($pegawai['foto']); ?>"
                     class="w-48 h-48 object-cover rounded-xl border-4 border-slate-100 shadow-md" alt="Foto Pegawai">
            <?php else: ?>
                <div class="w-48 h-48 bg-gradient-to-br from-slate-100 to-slate-200 rounded-xl flex flex-col items-center justify-center text-slate-400 border-4 border-slate-100">
                    <svg class="w-16 h-16 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    <span class="text-sm font-medium">Tidak ada foto</span>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Kartu Data Pegawai -->
    <div class="lg:col-span-2 bg-white rounded-xl shadow-lg p-6 border border-slate-100 hover:shadow-xl transition-shadow duration-200">
        <div class="flex items-center justify-between mb-5">
            <div class="flex items-center">
                <div class="p-2 bg-blue-100 rounded-lg">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <h2 class="font-bold text-lg ml-3 text-slate-800">Data Pegawai</h2>
            </div>
            <?php if ((int)$pegawai['aktif'] === 1): ?>
                <span class="px-3 py-1 bg-emerald-100 text-emerald-700 rounded-full text-xs font-semibold flex items-center">
                    <span class="w-2 h-2 bg-emerald-500 rounded-full mr-2 animate-pulse"></span>
                    Aktif
                </span>
            <?php else: ?>
                <span class="px-3 py-1 bg-slate-100 text-slate-600 rounded-full text-xs font-semibold flex items-center">
                    <span class="w-2 h-2 bg-slate-400 rounded-full mr-2"></span>
                    Nonaktif
                </span>
            <?php endif; ?>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <div class="p-4 bg-gradient-to-br from-slate-50 to-slate-100 rounded-lg border border-slate-200">
                <p class="text-slate-500 text-xs font-semibold uppercase tracking-wide mb-1">NIP</p>
                <p class="font-bold text-slate-800 text-lg"><?php echo htmlspecialchars($pegawai['nip']); ?></p>
            </div>
            <div class="p-4 bg-gradient-to-br from-blue-50 to-indigo-50 rounded-lg border border-blue-200">
                <p class="text-blue-600 text-xs font-semibold uppercase tracking-wide mb-1">Nama Lengkap</p>
                <p class="font-bold text-slate-800 text-lg"><?php echo htmlspecialchars($pegawai['nama']); ?></p>
            </div>
            <div class="p-4 bg-gradient-to-br from-purple-50 to-purple-100 rounded-lg border border-purple-200">
                <p class="text-purple-600 text-xs font-semibold uppercase tracking-wide mb-1">Jabatan</p>
                <p class="font-bold text-slate-800"><?php echo htmlspecialchars($pegawai['jabatan']); ?></p>
            </div>
            <div class="p-4 bg-gradient-to-br from-amber-50 to-amber-100 rounded-lg border border-amber-200">
                <p class="text-amber-600 text-xs font-semibold uppercase tracking-wide mb-1">Unit Kerja</p>
                <p class="font-bold text-slate-800"><?php echo htmlspecialchars($pegawai['unit_kerja']); ?></p>
            </div>
            <div class="p-4 bg-gradient-to-br from-teal-50 to-teal-100 rounded-lg border border-teal-200">
                <p class="text-teal-600 text-xs font-semibold uppercase tracking-wide mb-1">Status Pegawai</p>
                <p class="font-bold text-slate-800"><?php echo htmlspecialchars($pegawai['status_pegawai']); ?></p>
            </div>
            <div class="p-4 bg-gradient-to-br from-rose-50 to-rose-100 rounded-lg border border-rose-200">
                <p class="text-rose-600 text-xs font-semibold uppercase tracking-wide mb-1">No HP</p>
                <p class="font-bold text-slate-800"><?php echo htmlspecialchars($pegawai['no_hp']); ?></p>
            </div>
        </div>
    </div>
</div>

<!-- Akun yang Terhubung -->
<div class="bg-white rounded-xl shadow-lg p-6 mt-6 border border-slate-100 hover:shadow-xl transition-shadow duration-200">
    <div class="flex items-center mb-5">
        <div class="p-2 bg-green-100 rounded-lg">
            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
            </svg>
        </div>
        <h2 class="font-bold text-lg ml-3 text-slate-800">Akun Aplikasi</h2>
    </div>
    <?php if ($akun): ?>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
            <div class="p-4 bg-gradient-to-br from-blue-50 to-blue-100 rounded-lg border border-blue-200">
                <p class="text-blue-600 text-xs font-semibold uppercase tracking-wide mb-2 flex items-center">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    Username
                </p>
                <p class="font-bold text-slate-800 text-lg"><?php echo htmlspecialchars($akun['username']); ?></p>
            </div>
            <div class="p-4 bg-gradient-to-br from-purple-50 to-purple-100 rounded-lg border border-purple-200">
                <p class="text-purple-600 text-xs font-semibold uppercase tracking-wide mb-2 flex items-center">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                    </svg>
                    Level
                </p>
                <p class="font-bold text-slate-800 text-lg capitalize"><?php echo htmlspecialchars($akun['level']); ?></p>
            </div>
            <div class="p-4 bg-gradient-to-br from-slate-50 to-slate-100 rounded-lg border border-slate-200">
                <p class="text-slate-600 text-xs font-semibold uppercase tracking-wide mb-2 flex items-center">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                    Password (hash)
                </p>
                <p class="font-mono text-xs text-slate-600 break-all bg-white p-2 rounded border border-slate-200">
                    <?php echo htmlspecialchars($akun['password_hash']); ?>
                </p>
            </div>

            <div class="md:col-span-3 p-5 bg-gradient-to-br from-amber-50 to-amber-100 rounded-lg border-2 border-amber-200">
                <p class="text-amber-700 text-xs font-semibold uppercase tracking-wide mb-2 flex items-center">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                    </svg>
                    Password Asli
                </p>
                <p class="font-bold text-slate-800 text-lg">
                    <?php
                    if ($hasPasswordRaw && isset($akun['password_raw']) && $akun['password_raw'] !== '') {
                        echo htmlspecialchars($akun['password_raw']);
                    } else {
                        echo '<span class="text-amber-600 text-base flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                </svg>
                                Tidak disimpan (demi keamanan)
                              </span>';
                    }
                    ?>
                </p>
            </div>
        </div>
    <?php else: ?>
        <div class="text-center py-8">
            <svg class="w-16 h-16 mx-auto text-slate-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636a9 9 0 010 12.728m0 0l-2.829-2.829m2.829 2.829L21 21M15.536 8.464a5 5 0 010 7.072m0 0l-2.829-2.829m-4.243 2.829a4.978 4.978 0 01-1.414-2.83m-1.414 5.658a9 9 0 01-2.167-9.238m7.824 2.167a1 1 0 111.414 1.414m-1.414-1.414L3 3m8.293 8.293l1.414 1.414"/>
            </svg>
            <p class="text-slate-500 font-medium">Belum ada akun yang terhubung ke pegawai ini</p>
            <p class="text-slate-400 text-sm mt-1">Silakan buat akun baru untuk pegawai ini</p>
        </div>
    <?php endif; ?>
</div>

<?php include '../templates/footer.php'; ?>