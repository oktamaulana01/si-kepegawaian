<?php
require 'config.php';
cekLogin();

$username    = $_SESSION['username'];
$level       = $_SESSION['level'];
$namaLengkap = $_SESSION['nama_lengkap'] ?? $username;
$pegawaiId   = $_SESSION['pegawai_id'] ?? 0;

// ambil data pegawai yang terhubung
$pegawai = null;
if ($pegawaiId) {
    $q = mysqli_query($conn, "SELECT * FROM pegawai WHERE id = $pegawaiId LIMIT 1");
    $pegawai = mysqli_fetch_assoc($q);
}

include 'templates/header.php';
?>

<!-- Header Section -->
<div class="mb-8">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-slate-800 mb-2 flex items-center">
                <svg class="w-9 h-9 mr-3 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
                Profil Saya
            </h1>
            <p class="text-slate-600">Informasi akun dan data pegawai</p>
        </div>
        <?php if ($level === 'admin'): ?>
        <a href="index.php" class="inline-flex items-center px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg transition-colors duration-200">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
            </svg>
            Dashboard
        </a>
        <?php endif; ?>
    </div>
</div>

<!-- Info Akun Card -->
<div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl shadow-lg overflow-hidden mb-6">
    <div class="relative">
        <div class="absolute top-0 right-0 -mt-4 -mr-4 w-32 h-32 bg-white opacity-10 rounded-full"></div>
        <div class="absolute bottom-0 left-0 -mb-8 -ml-8 w-40 h-40 bg-white opacity-10 rounded-full"></div>
        
        <div class="relative p-6 text-white">
            <div class="flex items-center mb-6">
                <div class="w-16 h-16 bg-white bg-opacity-20 rounded-xl flex items-center justify-center backdrop-blur-sm mr-4">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <h2 class="text-2xl font-bold"><?php echo htmlspecialchars($namaLengkap); ?></h2>
                    <p class="text-blue-100 text-sm">Informasi Akun</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-white bg-opacity-10 backdrop-blur-sm rounded-lg p-4">
                    <div class="flex items-center mb-2">
                        <svg class="w-5 h-5 mr-2 text-blue-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        <span class="text-blue-100 text-sm font-medium">Username</span>
                    </div>
                    <p class="text-lg font-semibold"><?php echo htmlspecialchars($username); ?></p>
                </div>

                <div class="bg-white bg-opacity-10 backdrop-blur-sm rounded-lg p-4">
                    <div class="flex items-center mb-2">
                        <svg class="w-5 h-5 mr-2 text-blue-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        <span class="text-blue-100 text-sm font-medium">Nama Lengkap</span>
                    </div>
                    <p class="text-lg font-semibold"><?php echo htmlspecialchars($namaLengkap); ?></p>
                </div>

                <div class="bg-white bg-opacity-10 backdrop-blur-sm rounded-lg p-4">
                    <div class="flex items-center mb-2">
                        <svg class="w-5 h-5 mr-2 text-blue-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                        <span class="text-blue-100 text-sm font-medium">Level Akses</span>
                    </div>
                    <p class="text-lg font-semibold capitalize"><?php echo htmlspecialchars($level); ?></p>
                </div>
            </div>

            <div class="mt-4 flex items-center bg-blue-400 bg-opacity-30 backdrop-blur-sm rounded-lg p-3">
                <svg class="w-5 h-5 mr-2 text-blue-100" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <p class="text-blue-100 text-sm">Untuk mengganti password, silakan gunakan menu "Ganti Password" di navigasi atas</p>
            </div>
        </div>
    </div>
</div>

<!-- Data Pegawai Card -->
<div class="bg-white rounded-xl shadow-lg overflow-hidden">
    <div class="bg-gradient-to-r from-slate-50 to-slate-100 px-6 py-4 border-b border-slate-200">
        <h2 class="font-bold text-slate-800 flex items-center">
            <svg class="w-6 h-6 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            Data Kepegawaian
        </h2>
    </div>

    <?php if ($pegawai): ?>
        <div class="p-6">
            <div class="flex flex-col lg:flex-row gap-6">
                <!-- Foto Section -->
                <div class="flex-shrink-0">
                    <?php if (!empty($pegawai['foto'])): ?>
                        <div class="relative group">
                            <img src="uploads/pegawai/<?php echo $pegawai['foto']; ?>" 
                                 class="w-40 h-40 object-cover rounded-xl shadow-lg ring-4 ring-slate-100" 
                                 alt="Foto Pegawai">
                            <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-10 transition-all duration-200 rounded-xl"></div>
                        </div>
                    <?php else: ?>
                        <div class="w-40 h-40 bg-gradient-to-br from-slate-200 to-slate-300 rounded-xl shadow-lg flex items-center justify-center ring-4 ring-slate-100">
                            <svg class="w-20 h-20 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                        </div>
                        <p class="text-center text-xs text-slate-500 mt-2">Tidak ada foto</p>
                    <?php endif; ?>
                </div>

                <!-- Info Section -->
                <div class="flex-1">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- NIP -->
                        <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-lg p-4 border border-blue-200">
                            <div class="flex items-center mb-2">
                                <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"/>
                                </svg>
                                <span class="text-xs font-semibold text-blue-700 uppercase tracking-wide">NIP</span>
                            </div>
                            <p class="text-lg font-bold text-blue-900"><?php echo $pegawai['nip']; ?></p>
                        </div>

                        <!-- Nama -->
                        <div class="bg-gradient-to-br from-purple-50 to-purple-100 rounded-lg p-4 border border-purple-200">
                            <div class="flex items-center mb-2">
                                <svg class="w-5 h-5 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                                <span class="text-xs font-semibold text-purple-700 uppercase tracking-wide">Nama</span>
                            </div>
                            <p class="text-lg font-bold text-purple-900"><?php echo $pegawai['nama']; ?></p>
                        </div>

                        <!-- Jabatan -->
                        <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-lg p-4 border border-green-200">
                            <div class="flex items-center mb-2">
                                <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                </svg>
                                <span class="text-xs font-semibold text-green-700 uppercase tracking-wide">Jabatan</span>
                            </div>
                            <p class="text-lg font-bold text-green-900"><?php echo $pegawai['jabatan']; ?></p>
                        </div>

                        <!-- Unit Kerja -->
                        <div class="bg-gradient-to-br from-orange-50 to-orange-100 rounded-lg p-4 border border-orange-200">
                            <div class="flex items-center mb-2">
                                <svg class="w-5 h-5 mr-2 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                </svg>
                                <span class="text-xs font-semibold text-orange-700 uppercase tracking-wide">Unit Kerja</span>
                            </div>
                            <p class="text-lg font-bold text-orange-900"><?php echo $pegawai['unit_kerja']; ?></p>
                        </div>

                        <!-- Status Pegawai -->
                        <div class="bg-gradient-to-br from-indigo-50 to-indigo-100 rounded-lg p-4 border border-indigo-200">
                            <div class="flex items-center mb-2">
                                <svg class="w-5 h-5 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                </svg>
                                <span class="text-xs font-semibold text-indigo-700 uppercase tracking-wide">Status Pegawai</span>
                            </div>
                            <p class="text-lg font-bold text-indigo-900"><?php echo $pegawai['status_pegawai']; ?></p>
                        </div>

                        <!-- Status Data -->
                        <div class="bg-gradient-to-br from-teal-50 to-teal-100 rounded-lg p-4 border border-teal-200">
                            <div class="flex items-center mb-2">
                                <svg class="w-5 h-5 mr-2 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <span class="text-xs font-semibold text-teal-700 uppercase tracking-wide">Status Data</span>
                            </div>
                            <p class="text-lg font-bold text-teal-900">
                                <?php echo (isset($pegawai['aktif']) && $pegawai['aktif'] == 1) ? 'Aktif' : 'Nonaktif'; ?>
                            </p>
                        </div>

                        <!-- No HP -->
                        <div class="bg-gradient-to-br from-pink-50 to-pink-100 rounded-lg p-4 border border-pink-200 md:col-span-2">
                            <div class="flex items-center mb-2">
                                <svg class="w-5 h-5 mr-2 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                </svg>
                                <span class="text-xs font-semibold text-pink-700 uppercase tracking-wide">No HP</span>
                            </div>
                            <p class="text-lg font-bold text-pink-900"><?php echo $pegawai['no_hp']; ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php else: ?>
        <div class="p-12 text-center">
            <svg class="w-20 h-20 mx-auto text-slate-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
            </svg>
            <p class="text-slate-500 text-lg font-medium">Data pegawai tidak terhubung dengan akun ini</p>
            <p class="text-slate-400 text-sm mt-2">Silakan hubungi administrator untuk menghubungkan data pegawai Anda</p>
        </div>
    <?php endif; ?>
</div>

<?php include 'templates/footer.php'; ?>