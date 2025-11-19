<?php
require_once __DIR__ . '/../config.php';
cekLogin();

// batasi hanya admin
if ($_SESSION['level'] !== 'admin') {
    header("Location: ../profil.php");
    exit;
}

/* =========================
   PAGINATION & PENCARIAN
   ========================= */
$limit            = 10; // jumlah data per halaman
$halaman_sekarang = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($halaman_sekarang < 1) $halaman_sekarang = 1;
$offset = ($halaman_sekarang - 1) * $limit;

// keyword cari
$cari  = isset($_GET['cari']) ? trim($_GET['cari']) : "";
$where = "";
if ($cari !== "") {
    $cari_safe = mysqli_real_escape_string($conn, $cari);
    $where     = "WHERE nama LIKE '%$cari_safe%' OR nip LIKE '%$cari_safe%'";
}

// hitung total data
$sql_count     = "SELECT COUNT(*) AS total FROM pegawai $where";
$res_count     = mysqli_query($conn, $sql_count);
$row_count     = mysqli_fetch_assoc($res_count);
$total_data    = $row_count['total'];
$total_halaman = ceil($total_data / $limit);

// ambil data pegawai sesuai halaman
$sql_data = "SELECT * FROM pegawai $where ORDER BY id DESC LIMIT $limit OFFSET $offset";
$result   = mysqli_query($conn, $sql_data);

// ambil daftar unit kerja untuk dropdown (PDF & Excel)
$unit_list  = mysqli_query($conn, "SELECT DISTINCT unit_kerja FROM pegawai WHERE unit_kerja <> '' ORDER BY unit_kerja ASC");
$unit_list2 = mysqli_query($conn, "SELECT DISTINCT unit_kerja FROM pegawai WHERE unit_kerja <> '' ORDER BY unit_kerja ASC");
?>
<?php include __DIR__ . '/../templates/header.php'; ?>

<!-- HEADER SECTION -->
<div class="mb-6">
    <div class="flex items-center justify-between mb-4">
        <div>
            <h1 class="text-3xl font-bold text-slate-800 flex items-center">
                <svg class="w-8 h-8 mr-3 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
                Data Pegawai
            </h1>
            <p class="text-slate-600 mt-1">Kelola data pegawai instansi</p>
        </div>
        <a href="../index.php" class="inline-flex items-center px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg transition-colors duration-200">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
            </svg>
            Dashboard
        </a>
    </div>
</div>

<!-- STATS CARDS -->
<?php
$totPns = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS jml FROM pegawai WHERE status_pegawai='PNS'"))['jml'];
$totHonorer = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS jml FROM pegawai WHERE status_pegawai='Honorer'"))['jml'];
$totAktif = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS jml FROM pegawai WHERE aktif=1"))['jml'];
$totNonaktif = $total_data - $totAktif;
?>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <!-- Total Pegawai Card -->
    <div class="relative overflow-hidden bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
        <div class="absolute top-0 right-0 -mt-4 -mr-4 w-24 h-24 bg-white opacity-10 rounded-full"></div>
        <div class="absolute bottom-0 left-0 -mb-8 -ml-8 w-32 h-32 bg-white opacity-10 rounded-full"></div>
        <div class="relative p-5 text-white">
            <div class="flex items-center justify-between mb-3">
                <div class="w-12 h-12 bg-white bg-opacity-20 rounded-lg flex items-center justify-center backdrop-blur-sm">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                </div>
            </div>
            <p class="text-blue-100 text-sm font-medium mb-1">Total Pegawai</p>
            <p class="text-3xl font-bold mb-2"><?php echo $total_data; ?></p>
            <div class="flex items-center text-xs text-blue-100">
                <span class="mr-1">●</span> Semua Data
            </div>
        </div>
    </div>

    <!-- PNS Card -->
    <div class="relative overflow-hidden bg-gradient-to-br from-green-500 to-green-600 rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
        <div class="absolute top-0 right-0 -mt-4 -mr-4 w-24 h-24 bg-white opacity-10 rounded-full"></div>
        <div class="absolute bottom-0 left-0 -mb-8 -ml-8 w-32 h-32 bg-white opacity-10 rounded-full"></div>
        <div class="relative p-5 text-white">
            <div class="flex items-center justify-between mb-3">
                <div class="w-12 h-12 bg-white bg-opacity-20 rounded-lg flex items-center justify-center backdrop-blur-sm">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                </div>
            </div>
            <p class="text-green-100 text-sm font-medium mb-1">Pegawai PNS</p>
            <p class="text-3xl font-bold mb-2"><?php echo $totPns; ?></p>
            <div class="flex items-center text-xs text-green-100">
                <span class="mr-1">●</span> Status Tetap
            </div>
        </div>
    </div>

    <!-- Honorer Card -->
    <div class="relative overflow-hidden bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
        <div class="absolute top-0 right-0 -mt-4 -mr-4 w-24 h-24 bg-white opacity-10 rounded-full"></div>
        <div class="absolute bottom-0 left-0 -mb-8 -ml-8 w-32 h-32 bg-white opacity-10 rounded-full"></div>
        <div class="relative p-5 text-white">
            <div class="flex items-center justify-between mb-3">
                <div class="w-12 h-12 bg-white bg-opacity-20 rounded-lg flex items-center justify-center backdrop-blur-sm">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                </div>
            </div>
            <p class="text-orange-100 text-sm font-medium mb-1">Pegawai Honorer</p>
            <p class="text-3xl font-bold mb-2"><?php echo $totHonorer; ?></p>
            <div class="flex items-center text-xs text-orange-100">
                <span class="mr-1">●</span> Status Kontrak
            </div>
        </div>
    </div>

    <!-- Pegawai Aktif Card -->
    <div class="relative overflow-hidden bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
        <div class="absolute top-0 right-0 -mt-4 -mr-4 w-24 h-24 bg-white opacity-10 rounded-full"></div>
        <div class="absolute bottom-0 left-0 -mb-8 -ml-8 w-32 h-32 bg-white opacity-10 rounded-full"></div>
        <div class="relative p-5 text-white">
            <div class="flex items-center justify-between mb-3">
                <div class="w-12 h-12 bg-white bg-opacity-20 rounded-lg flex items-center justify-center backdrop-blur-sm">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
            <p class="text-purple-100 text-sm font-medium mb-1">Pegawai Aktif</p>
            <p class="text-3xl font-bold mb-2"><?php echo $totAktif; ?></p>
            <div class="flex items-center text-xs text-purple-100">
                <span class="w-2 h-2 bg-purple-200 rounded-full mr-2 animate-pulse"></span>
                <?php echo $totNonaktif; ?> Nonaktif
            </div>
        </div>
    </div>
</div>

<!-- SEARCH & ACTION BUTTONS -->
<div class="bg-white rounded-xl shadow-lg mb-6 p-6">
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
        <!-- Search Form -->
        <div class="flex-1 max-w-md">
            <form method="get" class="flex items-center gap-2">
                <div class="relative flex-1">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                    <input
                        type="text"
                        name="cari"
                        value="<?php echo htmlspecialchars($cari); ?>"
                        placeholder="Cari nama atau NIP..."
                        class="w-full pl-10 pr-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    >
                </div>
                <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors duration-200">
                    Cari
                </button>
                <?php if ($cari !== ""): ?>
                    <a href="index.php" class="px-4 py-2 bg-slate-200 hover:bg-slate-300 text-slate-700 rounded-lg transition-colors duration-200">
                        Reset
                    </a>
                <?php endif; ?>
            </form>
        </div>

        <!-- Action Buttons -->
        <div class="flex flex-wrap items-center gap-2">
            <!-- Tambah Pegawai -->
            <a href="tambah.php" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors duration-200 font-medium">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                Tambah Pegawai
            </a>

            <!-- Dropdown Export -->
            <div class="relative inline-block text-left" x-data="{ open: false }">
                <button @click="open = !open" type="button" class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition-colors duration-200 font-medium">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Export Excel
                    <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>

                <div x-show="open"  
                        x-cloak
                        x-show="open"
                        x-transition.origin.top.right 
                        @click.away="open = false" 
                        class="origin-top-right absolute right-0 mt-2 w-56 rounded-lg shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-10">
                    <div class="py-1">
                        <a href="../laporan/pegawai-excel.php" class="flex items-center px-4 py-2 text-sm text-slate-700 hover:bg-slate-100">
                            <svg class="w-4 h-4 mr-3 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            Export Semua Data
                        </a>
                        <a href="../laporan/pegawai-excel.php?aktif=1" class="flex items-center px-4 py-2 text-sm text-slate-700 hover:bg-slate-100">
                            <svg class="w-4 h-4 mr-3 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Export Pegawai Aktif
                        </a>
                        <div class="border-t border-slate-100 my-1"></div>
                        <form action="../laporan/pegawai-excel.php" method="get" class="px-4 py-2">
                            <label class="text-xs text-slate-500 font-medium mb-1 block">Export per Unit Kerja:</label>
                            <select name="unit" onchange="this.form.submit()" class="w-full text-sm border border-slate-300 rounded px-2 py-1 focus:outline-none focus:ring-2 focus:ring-green-500">
                                <option value="">Pilih Unit...</option>
                                <?php while ($u2 = mysqli_fetch_assoc($unit_list2)) : ?>
                                    <option value="<?php echo htmlspecialchars($u2['unit_kerja']); ?>">
                                        <?php echo $u2['unit_kerja']; ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Dropdown Cetak PDF -->
            <div class="relative inline-block text-left" x-data="{ open: false }">
                <button @click="open = !open" type="button" class="inline-flex items-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-colors duration-200 font-medium">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                    </svg>
                    Cetak PDF
                    <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>

                <div x-show="open"  
                        x-cloak
                        x-show="open"
                        x-transition.origin.top.right 
                        @click.away="open = false" 
                        class="origin-top-right absolute right-0 mt-2 w-56 rounded-lg shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-10">
                    <div class="py-1">
                        <a href="../laporan/pegawai.php" target="_blank" class="flex items-center px-4 py-2 text-sm text-slate-700 hover:bg-slate-100">
                            <svg class="w-4 h-4 mr-3 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            Cetak Semua Data
                        </a>
                        <a href="../laporan/pegawai.php?aktif=1" target="_blank" class="flex items-center px-4 py-2 text-sm text-slate-700 hover:bg-slate-100">
                            <svg class="w-4 h-4 mr-3 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Cetak Pegawai Aktif
                        </a>
                        <div class="border-t border-slate-100 my-1"></div>
                        <form id="formCetak" action="../laporan/pegawai.php" method="get" target="_blank" class="px-4 py-2">
                            <label class="text-xs text-slate-500 font-medium mb-1 block">Cetak per Unit Kerja:</label>
                            <select name="unit" onchange="document.getElementById('formCetak').submit()" class="w-full text-sm border border-slate-300 rounded px-2 py-1 focus:outline-none focus:ring-2 focus:ring-red-500">
                                <option value="">Pilih Unit...</option>
                                <?php while ($u = mysqli_fetch_assoc($unit_list)) : ?>
                                    <option value="<?php echo htmlspecialchars($u['unit_kerja']); ?>">
                                        <?php echo $u['unit_kerja']; ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- TABLE -->
<div class="bg-white rounded-xl shadow-lg overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full">
            <thead>
                <tr class="bg-gradient-to-r from-slate-100 to-slate-50 border-b border-slate-200">
                    <th class="px-6 py-4 text-left text-xs font-bold text-slate-700 uppercase tracking-wider w-16">No</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-slate-700 uppercase tracking-wider w-24">Foto</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-slate-700 uppercase tracking-wider">NIP</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-slate-700 uppercase tracking-wider">Nama</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-slate-700 uppercase tracking-wider">Jabatan</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-slate-700 uppercase tracking-wider">Unit Kerja</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-slate-700 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-slate-700 uppercase tracking-wider">Aktif</th>
                    <th class="px-6 py-4 text-center text-xs font-bold text-slate-700 uppercase tracking-wider w-32">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-slate-200">
                <?php if (mysqli_num_rows($result) == 0) : ?>
                    <tr>
                        <td colspan="9" class="px-6 py-12 text-center">
                            <svg class="w-16 h-16 mx-auto text-slate-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                            </svg>
                            <p class="text-slate-500">Tidak ada data pegawai yang ditemukan.</p>
                            <?php if ($cari !== ""): ?>
                                <p class="text-slate-400 text-sm mt-2">Coba kata kunci lain atau <a href="index.php" class="text-blue-600 hover:underline">reset pencarian</a></p>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php $no = $offset + 1; ?>
                    <?php while ($row = mysqli_fetch_assoc($result)) : ?>
                        <tr class="hover:bg-slate-50 transition-colors duration-150">
                            <td class="px-6 py-4 text-sm text-slate-600"><?php echo $no++; ?></td>
                            <td class="px-6 py-4">
                                <?php if (!empty($row['foto'])) : ?>
                                    <img src="../uploads/pegawai/<?php echo $row['foto']; ?>"
                                         class="h-14 w-14 object-cover rounded-lg border-2 border-slate-200" alt="Foto">
                                <?php else: ?>
                                    <div class="h-14 w-14 bg-slate-200 rounded-lg flex items-center justify-center">
                                        <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                        </svg>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 text-sm text-slate-700 font-medium"><?php echo $row['nip']; ?></td>
                            <td class="px-6 py-4 text-sm text-slate-800 font-semibold"><?php echo $row['nama']; ?></td>
                            <td class="px-6 py-4 text-sm text-slate-600"><?php echo $row['jabatan']; ?></td>
                            <td class="px-6 py-4 text-sm text-slate-600"><?php echo $row['unit_kerja']; ?></td>
                            <td class="px-6 py-4 text-sm">
                                <?php if ($row['status_pegawai'] == 'PNS'): ?>
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                        </svg>
                                        PNS
                                    </span>
                                <?php else: ?>
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-orange-100 text-orange-800">
                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                        </svg>
                                        Honorer
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 text-sm">
                                <?php if ($row['aktif'] == 1): ?>
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-800">
                                        <span class="w-2 h-2 bg-blue-500 rounded-full mr-1.5"></span>
                                        Aktif
                                    </span>
                                <?php else: ?>
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-800">
                                        <span class="w-2 h-2 bg-red-500 rounded-full mr-1.5"></span>
                                        Nonaktif
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 text-sm text-center">
    <div class="flex items-center justify-center gap-2">

        <!-- DETAIL / VIEW -->
        <a href="view.php?id=<?php echo $row['id']; ?>"
           class="inline-flex items-center px-3 py-1.5 bg-slate-600 hover:bg-slate-700 text-white rounded-lg text-xs transition-colors">
            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M2.458 12C3.732 7.943 7.523 5 12 5s8.268 2.943 9.542 7c-1.274 4.057-5.065 7-9.542 7s-8.268-2.943-9.542-7z"/>
            </svg>
            Detail
        </a>

        <!-- EDIT -->
        <a href="edit.php?id=<?php echo $row['id']; ?>"
           class="inline-flex items-center px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-xs transition-colors">
            Edit
        </a>

        <?php if ($row['aktif'] == 1): ?>
            <!-- NONAKTIFKAN -->
            <a href="nonaktifkan.php?id=<?php echo $row['id']; ?>"
               onclick="return confirm('Nonaktifkan pegawai ini?')"
               class="inline-flex items-center px-3 py-1.5 bg-orange-500 hover:bg-orange-600 text-white rounded-lg text-xs transition-colors">
                Nonaktifkan
            </a>
        <?php else: ?>
            <!-- AKTIFKAN -->
            <a href="aktifkan.php?id=<?php echo $row['id']; ?>"
               onclick="return confirm('Aktifkan kembali pegawai ini?')"
               class="inline-flex items-center px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-xs transition-colors">
                Aktifkan
            </a>
        <?php endif; ?>

        <!-- HAPUS -->
        <a href="hapus.php?id=<?php echo $row['id']; ?>"
           onclick="return confirm('Hapus PERMANEN pegawai ini? Data tidak bisa dikembalikan!')"
           class="inline-flex items-center px-3 py-1.5 bg-red-600 hover:bg-red-700 text-white rounded-lg text-xs transition-colors">
            Hapus
        </a>
    </div>
</td>


                                </div>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- PAGINATION -->
<?php if ($total_halaman > 1): ?>
    <div class="mt-6 flex flex-col sm:flex-row items-center justify-between gap-4">
        <div class="text-sm text-slate-600">
            Menampilkan halaman <span class="font-semibold"><?php echo $halaman_sekarang; ?></span> 
            dari <span class="font-semibold"><?php echo $total_halaman; ?></span> halaman
            <span class="mx-2">•</span>
            Total <span class="font-semibold"><?php echo $total_data; ?></span> data
        </div>
        
        <div class="flex items-center gap-2">
            <!-- Previous Button -->
            <?php if ($halaman_sekarang > 1): ?>
                <?php
                    $link_prev = "index.php?page=" . ($halaman_sekarang - 1);
                    if ($cari !== "") {
                        $link_prev .= "&cari=" . urlencode($cari);
                    }
                ?>
                <a href="<?php echo $link_prev; ?>" 
                   class="inline-flex items-center px-3 py-2 border border-slate-300 rounded-lg bg-white hover:bg-slate-50 text-slate-700 transition-colors duration-200">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                    Previous
                </a>
            <?php endif; ?>

            <!-- Page Numbers -->
            <div class="flex items-center gap-1">
                <?php
                $start_page = max(1, $halaman_sekarang - 2);
                $end_page = min($total_halaman, $halaman_sekarang + 2);
                
                if ($start_page > 1): ?>
                    <?php
                        $link = "index.php?page=1";
                        if ($cari !== "") $link .= "&cari=" . urlencode($cari);
                    ?>
                    <a href="<?php echo $link; ?>" 
                       class="px-3 py-2 border border-slate-300 rounded-lg bg-white hover:bg-slate-50 text-slate-700 transition-colors duration-200">
                        1
                    </a>
                    <?php if ($start_page > 2): ?>
                        <span class="px-2 text-slate-500">...</span>
                    <?php endif; ?>
                <?php endif; ?>

                <?php for ($i = $start_page; $i <= $end_page; $i++): ?>
                    <?php
                        $link = "index.php?page=$i";
                        if ($cari !== "") {
                            $link .= "&cari=" . urlencode($cari);
                        }
                    ?>
                    <a href="<?php echo $link; ?>"
                       class="px-3 py-2 rounded-lg transition-colors duration-200 <?php echo $i == $halaman_sekarang ? 'bg-blue-600 text-white font-semibold' : 'border border-slate-300 bg-white hover:bg-slate-50 text-slate-700'; ?>">
                        <?php echo $i; ?>
                    </a>
                <?php endfor; ?>

                <?php if ($end_page < $total_halaman): ?>
                    <?php if ($end_page < $total_halaman - 1): ?>
                        <span class="px-2 text-slate-500">...</span>
                    <?php endif; ?>
                    <?php
                        $link = "index.php?page=$total_halaman";
                        if ($cari !== "") $link .= "&cari=" . urlencode($cari);
                    ?>
                    <a href="<?php echo $link; ?>" 
                       class="px-3 py-2 border border-slate-300 rounded-lg bg-white hover:bg-slate-50 text-slate-700 transition-colors duration-200">
                        <?php echo $total_halaman; ?>
                    </a>
                <?php endif; ?>
            </div>

            <!-- Next Button -->
            <?php if ($halaman_sekarang < $total_halaman): ?>
                <?php
                    $link_next = "index.php?page=" . ($halaman_sekarang + 1);
                    if ($cari !== "") {
                        $link_next .= "&cari=" . urlencode($cari);
                    }
                ?>
                <a href="<?php echo $link_next; ?>" 
                   class="inline-flex items-center px-3 py-2 border border-slate-300 rounded-lg bg-white hover:bg-slate-50 text-slate-700 transition-colors duration-200">
                    Next
                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
            <?php endif; ?>
        </div>
    </div>
<?php endif; ?>

<!-- Alpine.js for dropdown functionality -->
<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

<?php include __DIR__ . '/../templates/footer.php'; ?>