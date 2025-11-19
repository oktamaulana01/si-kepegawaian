<?php
require 'config.php';
cekLogin();

if ($_SESSION['level'] !== 'admin') {
    header("Location: profil.php");
    exit;
}

$tgl_mulai   = $_GET['tgl_mulai']   ?? '';
$tgl_selesai = $_GET['tgl_selesai'] ?? '';

$where = "1=1";
if ($tgl_mulai !== '' && $tgl_selesai !== '') {
    $where = "waktu BETWEEN '$tgl_mulai 00:00:00' AND '$tgl_selesai 23:59:59'";
} elseif ($tgl_mulai !== '') {
    $where = "DATE(waktu) = '$tgl_mulai'";
}

// hanya tampilkan selain 'hapus' supaya tidak ada nesting "Hapus log aktivitas: ... "
$sql_logs_list = "SELECT * FROM log_aktivitas WHERE ($where) AND aksi <> 'hapus' ORDER BY waktu DESC";
$logs = mysqli_query($conn, $sql_logs_list);


// Hitung total log
$cardWhere  = $where . " AND aksi <> 'hapus'";
$total_logs = (int) mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT COUNT(*) AS jml FROM log_aktivitas WHERE {$cardWhere}")
)['jml'];

$stat_login  = (int) mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT COUNT(*) AS jml FROM log_aktivitas WHERE {$where} AND aksi='login'")
)['jml'];
$stat_tambah = (int) mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT COUNT(*) AS jml FROM log_aktivitas WHERE {$where} AND aksi='tambah'")
)['jml'];
$stat_edit   = (int) mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT COUNT(*) AS jml FROM log_aktivitas WHERE {$where} AND aksi='edit'")
)['jml'];
$stat_hapus  = (int) mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT COUNT(*) AS jml FROM log_aktivitas WHERE {$where} AND aksi='hapus'")
)['jml'];

include 'templates/header.php';
?>

<!-- Header Section -->
<div class="mb-8">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-slate-800 mb-2 flex items-center">
                <svg class="w-9 h-9 mr-3 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Log Aktivitas Sistem
            </h1>
            <p class="text-slate-600">Riwayat aktivitas pengguna dan sistem</p>
        </div>
        <a href="index.php" class="inline-flex items-center px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg transition-colors duration-200">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
            </svg>
            Dashboard
        </a>
    </div>
</div>

<!-- Stats Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4 mb-6">
    <!-- Total Log Card -->
    <div class="relative overflow-hidden bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl shadow-lg p-4 text-white">
        <div class="absolute top-0 right-0 -mt-4 -mr-4 w-20 h-20 bg-white opacity-10 rounded-full"></div>
        <div class="relative">
            <div class="flex items-center justify-between mb-2">
                <div class="w-10 h-10 bg-white bg-opacity-20 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
            </div>
            <p class="text-purple-100 text-xs font-medium mb-1">Total Log</p>
            <p class="text-2xl font-bold"><?php echo $total_logs; ?></p>
        </div>
    </div>

    <!-- Login Card -->
    <div class="relative overflow-hidden bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl shadow-lg p-4 text-white">
        <div class="absolute top-0 right-0 -mt-4 -mr-4 w-20 h-20 bg-white opacity-10 rounded-full"></div>
        <div class="relative">
            <div class="flex items-center justify-between mb-2">
                <div class="w-10 h-10 bg-white bg-opacity-20 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                    </svg>
                </div>
            </div>
            <p class="text-blue-100 text-xs font-medium mb-1">Login</p>
            <p class="text-2xl font-bold"><?php echo $stat_login; ?></p>
        </div>
    </div>

    <!-- Tambah Card -->
    <div class="relative overflow-hidden bg-gradient-to-br from-green-500 to-green-600 rounded-xl shadow-lg p-4 text-white">
        <div class="absolute top-0 right-0 -mt-4 -mr-4 w-20 h-20 bg-white opacity-10 rounded-full"></div>
        <div class="relative">
            <div class="flex items-center justify-between mb-2">
                <div class="w-10 h-10 bg-white bg-opacity-20 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                </div>
            </div>
            <p class="text-green-100 text-xs font-medium mb-1">Tambah</p>
            <p class="text-2xl font-bold"><?php echo $stat_tambah; ?></p>
        </div>
    </div>

    <!-- Edit Card -->
    <div class="relative overflow-hidden bg-gradient-to-br from-amber-500 to-amber-600 rounded-xl shadow-lg p-4 text-white">
        <div class="absolute top-0 right-0 -mt-4 -mr-4 w-20 h-20 bg-white opacity-10 rounded-full"></div>
        <div class="relative">
            <div class="flex items-center justify-between mb-2">
                <div class="w-10 h-10 bg-white bg-opacity-20 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                </div>
            </div>
            <p class="text-amber-100 text-xs font-medium mb-1">Edit</p>
            <p class="text-2xl font-bold"><?php echo $stat_edit; ?></p>
        </div>
    </div>

    <!-- Hapus Card -->
    <div class="relative overflow-hidden bg-gradient-to-br from-red-500 to-red-600 rounded-xl shadow-lg p-4 text-white">
        <div class="absolute top-0 right-0 -mt-4 -mr-4 w-20 h-20 bg-white opacity-10 rounded-full"></div>
        <div class="relative">
            <div class="flex items-center justify-between mb-2">
                <div class="w-10 h-10 bg-white bg-opacity-20 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                </div>
            </div>
            <p class="text-red-100 text-xs font-medium mb-1">Hapus</p>
            <p class="text-2xl font-bold"><?php echo $stat_hapus; ?></p>
        </div>
    </div>
</div>

<!-- Filter Section -->
<div class="bg-white rounded-xl shadow-lg mb-6 p-6">
    <div class="flex items-center mb-4">
        <svg class="w-5 h-5 mr-2 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
        </svg>
        <h2 class="font-bold text-slate-800">Filter Berdasarkan Tanggal</h2>
    </div>
    
    <form method="get" class="flex flex-col md:flex-row items-end gap-4">
        <div class="flex-1">
            <label class="block text-sm font-semibold text-slate-700 mb-2">
                Tanggal Mulai
            </label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
                <input 
                    type="date" 
                    name="tgl_mulai" 
                    value="<?php echo htmlspecialchars($tgl_mulai); ?>" 
                    class="w-full pl-10 pr-4 py-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                >
            </div>
        </div>

        <div class="flex-1">
            <label class="block text-sm font-semibold text-slate-700 mb-2">
                Tanggal Selesai
            </label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
                <input 
                    type="date" 
                    name="tgl_selesai" 
                    value="<?php echo htmlspecialchars($tgl_selesai); ?>" 
                    class="w-full pl-10 pr-4 py-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                >
            </div>
        </div>

        <div class="flex gap-2">
            <button 
                type="submit"
                class="inline-flex items-center px-5 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors duration-200 font-medium shadow-lg hover:shadow-xl">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                </svg>
                Filter
            </button>
            
            <?php if ($tgl_mulai !== '' || $tgl_selesai !== ''): ?>
            <a 
                href="log.php" 
                class="inline-flex items-center px-5 py-3 bg-slate-200 hover:bg-slate-300 text-slate-700 rounded-lg transition-colors duration-200 font-medium">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
                Reset
            </a>
            <?php endif; ?>
        </div>
    </form>
</div>

<!-- Table Section -->
<div class="bg-white rounded-xl shadow-lg overflow-hidden">
    <div class="bg-gradient-to-r from-slate-50 to-slate-100 px-6 py-4 border-b border-slate-200">
        <h2 class="font-bold text-slate-800 flex items-center">
            <svg class="w-5 h-5 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
            </svg>
            Riwayat Aktivitas
            <?php if ($tgl_mulai !== '' || $tgl_selesai !== ''): ?>
                <span class="ml-2 text-sm font-normal text-slate-600">
                    (<?php echo $tgl_mulai ? date('d M Y', strtotime($tgl_mulai)) : 'Awal'; ?> -
                    <?php echo $tgl_selesai ? date('d M Y', strtotime($tgl_selesai)) : 'Sekarang'; ?>)
                </span>
            <?php endif; ?>
        </h2>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full">
            <thead>
                <tr class="bg-slate-100 border-b border-slate-200">
                    <th class="px-6 py-4 text-left text-xs font-bold text-slate-700 uppercase tracking-wider w-48">Waktu</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-slate-700 uppercase tracking-wider">User</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-slate-700 uppercase tracking-wider w-32">Aksi</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-slate-700 uppercase tracking-wider">Keterangan</th>
                    <th class="px-6 py-4 text-center text-xs font-bold text-slate-700 uppercase tracking-wider w-20">Hapus</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-slate-200">
                <?php if (mysqli_num_rows($logs) == 0): ?>
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center">
                            <svg class="w-16 h-16 mx-auto text-slate-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            <p class="text-slate-500 text-lg font-medium">Tidak ada log aktivitas</p>
                            <p class="text-slate-400 text-sm mt-2">Belum ada aktivitas yang tercatat dalam periode ini</p>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php while ($row = mysqli_fetch_assoc($logs)) : ?>
                        <tr class="hover:bg-slate-50 transition-colors duration-150">
                            <!-- waktu -->
                            <td class="px-6 py-4">
                                <div class="flex items-center text-sm">
                                    <svg class="w-4 h-4 mr-2 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <span class="text-slate-700 font-medium">
                                        <?php echo date('d M Y, H:i', strtotime($row['waktu'])); ?>
                                    </span>
                                </div>
                            </td>

                            <!-- user -->
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <div class="w-8 h-8 bg-gradient-to-br from-blue-500 to-blue-600 rounded-full flex items-center justify-center mr-3">
                                        <span class="text-white text-xs font-bold">
                                            <?php echo strtoupper(substr($row['username'] !== '' ? $row['username'] : 'G', 0, 1)); ?>
                                        </span>
                                    </div>
                                    <span class="text-sm font-semibold text-slate-800">
                                        <?php echo $row['username'] !== '' ? htmlspecialchars($row['username']) : '<span class="text-slate-400">Guest</span>'; ?>
                                    </span>
                                </div>
                            </td>

                            <!-- aksi badge -->
                            <td class="px-6 py-4">
                                <?php
                                $aksi = htmlspecialchars($row['aksi']);
                                $badge_class = 'bg-slate-100 text-slate-800';
                                $icon = '';
                                switch($aksi) {
                                    case 'login':
                                        $badge_class = 'bg-blue-100 text-blue-800';
                                        $icon = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>';
                                        break;
                                    case 'logout':
                                        $badge_class = 'bg-slate-100 text-slate-800';
                                        $icon = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>';
                                        break;
                                    case 'tambah':
                                        $badge_class = 'bg-green-100 text-green-800';
                                        $icon = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>';
                                        break;
                                    case 'edit':
                                        $badge_class = 'bg-amber-100 text-amber-800';
                                        $icon = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>';
                                        break;
                                    case 'hapus':
                                        $badge_class = 'bg-red-100 text-red-800';
                                        $icon = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>';
                                        break;
                                    case 'ganti-password':
                                        $badge_class = 'bg-indigo-100 text-indigo-800';
                                        $icon = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>';
                                        break;
                                }
                                ?>
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold <?php echo $badge_class; ?>">
                                    <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <?php echo $icon; ?>
                                    </svg>
                                    <?php echo ucfirst($aksi); ?>
                                </span>
                            </td>

                            <!-- keterangan -->
                            <td class="px-6 py-4 text-sm text-slate-600">
                                <?php echo htmlspecialchars($row['keterangan']); ?>
                            </td>

                            <!-- tombol hapus -->
                            <td class="px-6 py-4 text-center">
                                <a href="log-hapus.php?id=<?php echo $row['id']; ?>"
                                   onclick="return confirm('Hapus log ini?');"
                                   class="inline-flex items-center px-2 py-1 bg-red-500 hover:bg-red-600 text-white text-xs rounded">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                    Hapus
                                </a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include 'templates/footer.php'; ?>
