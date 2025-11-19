<?php
require 'config.php';
cekLogin();

if ($_SESSION['level'] !== 'admin') {
    header("Location: ../profil.php");
    exit;
}

$level = $_SESSION['level'];

// hitung jumlah pegawai (aktif saja)
$pegawai_aktif = mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT COUNT(*) AS jml FROM pegawai WHERE aktif = 1")
)['jml'];

// hitung total pegawai (semua)
$pegawai_total = mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT COUNT(*) AS jml FROM pegawai")
)['jml'];

// hitung unit
$unit_total = mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT COUNT(*) AS jml FROM unit_kerja")
)['jml'];

// hitung jabatan
$jab_total = mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT COUNT(*) AS jml FROM jabatan")
)['jml'];

// hitung PNS dan Honorer
$totPns = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS jml FROM pegawai WHERE status_pegawai='PNS'"))['jml'];
$totHonorer = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS jml FROM pegawai WHERE status_pegawai='Honorer'"))['jml'];

// rekap per unit kerja untuk chart
$rekapUnitRes = mysqli_query($conn, "
    SELECT unit_kerja, COUNT(*) AS jml
    FROM pegawai
    WHERE unit_kerja <> ''
    GROUP BY unit_kerja
    ORDER BY unit_kerja ASC
");

$labelsUnit = [];
$dataUnit   = [];
while ($r = mysqli_fetch_assoc($rekapUnitRes)) {
    $labelsUnit[] = $r['unit_kerja'];
    $dataUnit[]   = (int)$r['jml'];
}

// buat ulang result untuk tabel
$rekapUnitTable = mysqli_query($conn, "
    SELECT unit_kerja, COUNT(*) AS jml
    FROM pegawai
    WHERE unit_kerja <> ''
    GROUP BY unit_kerja
    ORDER BY unit_kerja ASC
");

include 'templates/header.php';
?>

<!-- Header Section -->
<div class="mb-8">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-slate-800 mb-2 flex items-center">
                <svg class="w-9 h-9 mr-3 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                Dashboard
            </h1>
            <p class="text-slate-600">Selamat datang, <span class="font-semibold"><?php echo isset($_SESSION['nama_lengkap']) ? $_SESSION['nama_lengkap'] : $_SESSION['username']; ?></span></p>
        </div>
        <div class="text-right">
            <p class="text-sm text-slate-500">Hari ini</p>
            <p class="text-lg font-semibold text-slate-700"><?php echo date('d F Y'); ?></p>
        </div>
    </div>
</div>

<!-- Stats Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <!-- Pegawai Aktif Card -->
    <div class="relative overflow-hidden bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
        <div class="absolute top-0 right-0 -mt-4 -mr-4 w-24 h-24 bg-white opacity-10 rounded-full"></div>
        <div class="absolute bottom-0 left-0 -mb-8 -ml-8 w-32 h-32 bg-white opacity-10 rounded-full"></div>
        <div class="relative p-6 text-white">
            <div class="flex items-center justify-between mb-4">
                <div class="w-14 h-14 bg-white bg-opacity-20 rounded-xl flex items-center justify-center backdrop-blur-sm">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                </div>
            </div>
            <p class="text-blue-100 text-sm font-medium mb-1">Pegawai Aktif</p>
            <p class="text-4xl font-bold mb-2"><?php echo $pegawai_aktif; ?></p>
            <div class="flex items-center text-sm text-blue-100">
                <span class="w-2 h-2 bg-blue-200 rounded-full mr-2 animate-pulse"></span>
                Status Aktif
            </div>
        </div>
    </div>

    <!-- Total Pegawai Card -->
    <div class="relative overflow-hidden bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
        <div class="absolute top-0 right-0 -mt-4 -mr-4 w-24 h-24 bg-white opacity-10 rounded-full"></div>
        <div class="absolute bottom-0 left-0 -mb-8 -ml-8 w-32 h-32 bg-white opacity-10 rounded-full"></div>
        <div class="relative p-6 text-white">
            <div class="flex items-center justify-between mb-4">
                <div class="w-14 h-14 bg-white bg-opacity-20 rounded-xl flex items-center justify-center backdrop-blur-sm">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </div>
            </div>
            <p class="text-purple-100 text-sm font-medium mb-1">Total Pegawai</p>
            <p class="text-4xl font-bold mb-2"><?php echo $pegawai_total; ?></p>
            <div class="flex items-center text-sm text-purple-100">
                <span class="mr-1">●</span> Semua Status
            </div>
        </div>
    </div>

    <!-- Unit Kerja Card -->
    <div class="relative overflow-hidden bg-gradient-to-br from-green-500 to-green-600 rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
        <div class="absolute top-0 right-0 -mt-4 -mr-4 w-24 h-24 bg-white opacity-10 rounded-full"></div>
        <div class="absolute bottom-0 left-0 -mb-8 -ml-8 w-32 h-32 bg-white opacity-10 rounded-full"></div>
        <div class="relative p-6 text-white">
            <div class="flex items-center justify-between mb-4">
                <div class="w-14 h-14 bg-white bg-opacity-20 rounded-xl flex items-center justify-center backdrop-blur-sm">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                </div>
            </div>
            <p class="text-green-100 text-sm font-medium mb-1">Unit Kerja</p>
            <p class="text-4xl font-bold mb-2"><?php echo $unit_total; ?></p>
            <div class="flex items-center text-sm text-green-100">
                <span class="mr-1">●</span> Divisi Aktif
            </div>
        </div>
    </div>

    <!-- Jabatan Card -->
    <div class="relative overflow-hidden bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
        <div class="absolute top-0 right-0 -mt-4 -mr-4 w-24 h-24 bg-white opacity-10 rounded-full"></div>
        <div class="absolute bottom-0 left-0 -mb-8 -ml-8 w-32 h-32 bg-white opacity-10 rounded-full"></div>
        <div class="relative p-6 text-white">
            <div class="flex items-center justify-between mb-4">
                <div class="w-14 h-14 bg-white bg-opacity-20 rounded-xl flex items-center justify-center backdrop-blur-sm">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                </div>
            </div>
            <p class="text-orange-100 text-sm font-medium mb-1">Jabatan</p>
            <p class="text-4xl font-bold mb-2"><?php echo $jab_total; ?></p>
            <div class="flex items-center text-sm text-orange-100">
                <span class="mr-1">●</span> Posisi Tersedia
            </div>
        </div>
    </div>
</div>

<!-- Charts Section -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
    <!-- Unit Kerja Chart -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="bg-gradient-to-r from-slate-50 to-slate-100 px-6 py-4 border-b border-slate-200">
            <h2 class="font-bold text-slate-800 flex items-center">
                <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
                Pegawai per Unit Kerja
            </h2>
        </div>
        <div class="p-6">
            <canvas id="unitChart" height="180"></canvas>
        </div>
    </div>

    <!-- Status Chart -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="bg-gradient-to-r from-slate-50 to-slate-100 px-6 py-4 border-b border-slate-200">
            <h2 class="font-bold text-slate-800 flex items-center">
                <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"/>
                </svg>
                Komposisi Status Pegawai
            </h2>
        </div>
        <div class="p-6">
            <canvas id="statusChart" height="180"></canvas>
        </div>
    </div>
</div>

<!-- Quick Actions & Table Section -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
    <!-- Quick Actions -->
    <?php if ($level === 'admin'): ?>
    <div class="lg:col-span-1">
        <div class="bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-xl shadow-lg p-6 text-white mb-6">
            <h2 class="font-bold text-xl mb-4 flex items-center">
                <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                </svg>
                Aksi Cepat
            </h2>
            <div class="space-y-3">
                <a href="pegawai/tambah.php" class="flex items-center w-full bg-white bg-opacity-20 hover:bg-opacity-30 backdrop-blur-sm px-4 py-3 rounded-lg transition-all duration-200">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                    </svg>
                    <span class="font-medium">Tambah Pegawai</span>
                </a>
                <a href="pegawai/" class="flex items-center w-full bg-white bg-opacity-20 hover:bg-opacity-30 backdrop-blur-sm px-4 py-3 rounded-lg transition-all duration-200">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                    </svg>
                    <span class="font-medium">Kelola Data Pegawai</span>
                </a>
                <a href="laporan/pegawai.php" target="_blank" class="flex items-center w-full bg-white bg-opacity-20 hover:bg-opacity-30 backdrop-blur-sm px-4 py-3 rounded-lg transition-all duration-200">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                    </svg>
                    <span class="font-medium">Cetak Laporan PDF</span>
                </a>
                <a href="laporan/pegawai-excel.php" class="flex items-center w-full bg-white bg-opacity-20 hover:bg-opacity-30 backdrop-blur-sm px-4 py-3 rounded-lg transition-all duration-200">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <span class="font-medium">Export ke Excel</span>
                </a>
            </div>
        </div>

        <!-- Mini Stats -->
        <div class="bg-white rounded-xl shadow-lg p-6">
            <h3 class="font-semibold text-slate-800 mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
                Statistik Status
            </h3>
            <div class="space-y-3">
                <div class="flex items-center justify-between p-3 bg-green-50 rounded-lg">
                    <div class="flex items-center">
                        <div class="w-3 h-3 bg-green-500 rounded-full mr-3"></div>
                        <span class="text-sm font-medium text-slate-700">PNS</span>
                    </div>
                    <span class="text-lg font-bold text-green-700"><?php echo $totPns; ?></span>
                </div>
                <div class="flex items-center justify-between p-3 bg-orange-50 rounded-lg">
                    <div class="flex items-center">
                        <div class="w-3 h-3 bg-orange-500 rounded-full mr-3"></div>
                        <span class="text-sm font-medium text-slate-700">Honorer</span>
                    </div>
                    <span class="text-lg font-bold text-orange-700"><?php echo $totHonorer; ?></span>
                </div>
            </div>
        </div>
    </div>
    <?php else: ?>
    <div class="lg:col-span-1">
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl shadow-lg p-6 text-white">
            <h2 class="font-bold text-xl mb-3">Selamat Datang</h2>
            <p class="text-blue-100 mb-4">Silakan kelola profil Anda atau ganti password untuk keamanan akun.</p>
            <a href="profil.php" class="inline-flex items-center bg-white text-blue-600 px-4 py-2 rounded-lg font-medium hover:bg-blue-50 transition-colors duration-200">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
                Lihat Profil
            </a>
        </div>
    </div>
    <?php endif; ?>

    <!-- Tabel Rekap per Unit -->
    <div class="lg:col-span-2">
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <div class="bg-gradient-to-r from-slate-50 to-slate-100 px-6 py-4 border-b border-slate-200 flex items-center justify-between">
                <h2 class="font-bold text-slate-800 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                    </svg>
                    Rekap Pegawai per Unit Kerja
                </h2>
                <a href="pegawai/" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors duration-200">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                    </svg>
                    Lihat Semua
                </a>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead>
                        <tr class="bg-slate-100 border-b border-slate-200">
                            <th class="px-6 py-4 text-left text-xs font-bold text-slate-700 uppercase tracking-wider w-16">No</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-slate-700 uppercase tracking-wider">Unit Kerja</th>
                            <th class="px-6 py-4 text-right text-xs font-bold text-slate-700 uppercase tracking-wider w-32">Jumlah</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-slate-200">
                        <?php $no=1; while($row = mysqli_fetch_assoc($rekapUnitTable)) : ?>
                            <tr class="hover:bg-slate-50 transition-colors duration-150">
                                <td class="px-6 py-4 text-sm text-slate-600"><?php echo $no++; ?></td>
                                <td class="px-6 py-4 text-sm text-slate-800 font-medium"><?php echo $row['unit_kerja']; ?></td>
                                <td class="px-6 py-4 text-sm text-right">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-800">
                                        <?php echo $row['jml']; ?> Pegawai
                                    </span>
                                </td>
                            </tr>
                        <?php endwhile; ?>

                        <?php if ($no === 1): ?>
                            <tr>
                                <td colspan="3" class="px-6 py-12 text-center">
                                    <svg class="w-16 h-16 mx-auto text-slate-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                                    </svg>
                                    <p class="text-slate-500 text-sm">Belum ada data pegawai.</p>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include 'templates/footer.php'; ?>

<!-- Chart.js dari CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// data dari PHP ke JS
const unitLabels = <?php echo json_encode($labelsUnit); ?>;
const unitData   = <?php echo json_encode($dataUnit); ?>;

// chart bar unit kerja dengan desain modern
const ctxUnit = document.getElementById('unitChart').getContext('2d');
new Chart(ctxUnit, {
    type: 'bar',
    data: {
        labels: unitLabels,
        datasets: [{
            label: 'Jumlah Pegawai',
            data: unitData,
            backgroundColor: 'rgba(59, 130, 246, 0.8)',
            borderColor: 'rgba(37, 99, 235, 1)',
            borderWidth: 2,
            borderRadius: 8,
            borderSkipped: false,
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
            legend: {
                display: false
            },
            tooltip: {
                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                padding: 12,
                borderRadius: 8,
                titleFont: {
                    size: 14,
                    weight: 'bold'
                },
                bodyFont: {
                    size: 13
                },
                callbacks: {
                    label: function(context) {
                        return 'Jumlah: ' + context.parsed.y + ' pegawai';
                    }
                }
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                precision: 0,
                grid: {
                    color: 'rgba(0, 0, 0, 0.05)',
                    drawBorder: false
                },
                ticks: {
                    font: {
                        size: 12
                    }
                }
            },
            x: {
                grid: {
                    display: false
                },
                ticks: {
                    font: {
                        size: 12
                    }
                }
            }
        }
    }
});

// chart doughnut status dengan desain modern
const ctxStatus = document.getElementById('statusChart').getContext('2d');
new Chart(ctxStatus, {
    type: 'doughnut',
    data: {
        labels: ['PNS', 'Honorer'],
        datasets: [{
            data: [<?php echo $totPns; ?>, <?php echo $totHonorer; ?>],
            backgroundColor: [
                'rgba(34, 197, 94, 0.8)',
                'rgba(249, 115, 22, 0.8)'
            ],
            borderColor: [
                'rgba(22, 163, 74, 1)',
                'rgba(234, 88, 12, 1)'
            ],
            borderWidth: 3,
            hoverOffset: 15
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
            legend: {
                position: 'bottom',
                labels: {
                    padding: 20,
                    font: {
                        size: 13,
                        weight: '500'
                    },
                    usePointStyle: true,
                    pointStyle: 'circle'
                }
            },
            tooltip: {
                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                padding: 12,
                borderRadius: 8,
                titleFont: {
                    size: 14,
                    weight: 'bold'
                },
                bodyFont: {
                    size: 13
                },
                callbacks: {
                    label: function(context) {
                        let label = context.label || '';
                        let value = context.parsed || 0;
                        let total = context.dataset.data.reduce((a, b) => a + b, 0);
                        let percentage = ((value / total) * 100).toFixed(1);
                        return label + ': ' + value + ' pegawai (' + percentage + '%)';
                    }
                }
            }
        },
        cutout: '65%'
    }
});
</script>