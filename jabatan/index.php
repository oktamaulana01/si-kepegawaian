<?php
require '../config.php';
cekLogin();

if ($_SESSION['level'] !== 'admin') {
    header("Location: ../profil.php");
    exit;
}

/* =========================
   PENCARIAN & PAGINATION
   ========================= */
$cari  = isset($_GET['cari']) ? trim($_GET['cari']) : '';
$where = '';
if ($cari !== '') {
    $cari_safe = mysqli_real_escape_string($conn, $cari);
    $where     = "WHERE nama_jabatan LIKE '%$cari_safe%'";
}

$limit  = 10;
$page   = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$offset = ($page - 1) * $limit;

$total = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS jml FROM jabatan $where"))['jml'];
$total_halaman = max(1, (int)ceil($total / $limit));

$q = mysqli_query($conn, "SELECT * FROM jabatan $where ORDER BY nama_jabatan ASC LIMIT $limit OFFSET $offset");

include '../templates/header.php';
?>
<!-- WRAPPER: Alpine untuk modal konfirmasi -->
<div x-data="{ modalOpen:false, hapusId:null, hapusNama:'' }">

    <!-- Header + CTA -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-6">
        <div>
            <h1 class="text-3xl font-bold text-slate-800 flex items-center">
                <svg class="w-8 h-8 mr-3 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
                Data Jabatan
            </h1>
            <p class="text-slate-600">Kelola daftar jabatan di instansi Anda</p>
        </div>

        <div class="flex items-center gap-2">
            <a href="../index.php"
               class="inline-flex items-center px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg transition">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                Dashboard
            </a>

            <a href="tambah.php"
               class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition font-medium shadow">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 6v12m6-6H6"/>
                </svg>
                Tambah Jabatan
            </a>
        </div>
    </div>

    <!-- Stats ringkas -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
        <div class="relative overflow-hidden bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-xl shadow p-4 text-white">
            <div class="absolute -top-6 -right-6 w-24 h-24 bg-white/10 rounded-full"></div>
            <p class="text-indigo-100 text-xs">Total Jabatan</p>
            <p class="text-3xl font-bold mt-1"><?php echo (int)$total; ?></p>
        </div>
        <div class="rounded-xl border border-slate-200 p-4 bg-white">
            <p class="text-sm text-slate-600">Halaman Saat Ini</p>
            <p class="text-xl font-semibold text-slate-800"><?php echo $page; ?> / <?php echo $total_halaman; ?></p>
        </div>
        <div class="rounded-xl border border-slate-200 p-4 bg-white">
            <p class="text-sm text-slate-600">Ditampilkan per Halaman</p>
            <p class="text-xl font-semibold text-slate-800"><?php echo $limit; ?> data</p>
        </div>
    </div>

    <!-- Pencarian -->
    <div class="bg-white rounded-xl shadow mb-6 p-4">
        <form method="get" class="flex flex-col sm:flex-row gap-3 items-start sm:items-center">
            <div class="relative flex-1 max-w-lg w-full">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
                <input type="text" name="cari" value="<?php echo htmlspecialchars($cari); ?>"
                       class="w-full pl-10 pr-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                       placeholder="Cari nama jabatan…">
            </div>

            <div class="flex gap-2">
                <button class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition">
                    Cari
                </button>
                <?php if ($cari !== ''): ?>
                    <a href="index.php"
                       class="px-4 py-2 bg-slate-200 hover:bg-slate-300 text-slate-700 rounded-lg transition">
                        Reset
                    </a>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <!-- Tabel -->
    <div class="bg-white rounded-xl shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead>
                <tr class="bg-gradient-to-r from-slate-100 to-slate-50 border-b border-slate-200">
                    <th class="px-6 py-4 text-left text-xs font-bold text-slate-700 uppercase tracking-wider w-16">No</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-slate-700 uppercase tracking-wider">Nama Jabatan</th>
                    <th class="px-6 py-4 text-center text-xs font-bold text-slate-700 uppercase tracking-wider w-40">Aksi</th>
                </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                <?php if ($total == 0): ?>
                    <tr>
                        <td colspan="3" class="px-6 py-12 text-center">
                            <svg class="w-16 h-16 mx-auto text-slate-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586A1 1 0 0114 3.293L19.707 9A1 1 0 0120 9.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            <p class="text-slate-500">Belum ada data jabatan.</p>
                            <p class="text-slate-400 text-sm mt-1">Klik <span class="font-semibold">“Tambah Jabatan”</span> untuk menambahkan.</p>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php $no = $offset + 1; ?>
                    <?php while ($row = mysqli_fetch_assoc($q)) : ?>
                        <tr class="hover:bg-slate-50">
                            <td class="px-6 py-3 text-sm text-slate-600"><?php echo $no++; ?></td>
                            <td class="px-6 py-3 text-sm font-medium text-slate-800">
                                <?php echo htmlspecialchars($row['nama_jabatan']); ?>
                            </td>
                            <td class="px-6 py-3 text-sm">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="edit.php?id=<?php echo $row['id']; ?>"
                                       class="inline-flex items-center px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-xs font-medium transition">
                                        <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                        Edit
                                    </a>

                                    <button
                                        @click.prevent="modalOpen=true; hapusId='<?php echo $row['id']; ?>'; hapusNama='<?php echo htmlspecialchars($row['nama_jabatan'], ENT_QUOTES); ?>'"
                                        class="inline-flex items-center px-3 py-1.5 bg-red-600 hover:bg-red-700 text-white rounded-lg text-xs font-medium transition">
                                        <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                        Hapus
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    <?php if ($total_halaman > 1): ?>
        <div class="mt-6 flex flex-wrap items-center justify-between gap-3">
            <div class="text-sm text-slate-600">
                Menampilkan halaman <span class="font-semibold"><?php echo $page; ?></span> dari
                <span class="font-semibold"><?php echo $total_halaman; ?></span> • Total
                <span class="font-semibold"><?php echo $total; ?></span> jabatan
            </div>

            <div class="flex items-center gap-2">
                <?php if ($page > 1):
                    $prev = "index.php?page=".($page-1).($cari!=='' ? "&cari=".urlencode($cari) : '');
                    ?>
                    <a href="<?php echo $prev; ?>"
                       class="inline-flex items-center px-3 py-2 border border-slate-300 rounded-lg bg-white hover:bg-slate-50 text-slate-700">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                        </svg>
                        Prev
                    </a>
                <?php endif; ?>

                <?php
                $start = max(1, $page-2);
                $end   = min($total_halaman, $page+2);
                for ($i=$start; $i<=$end; $i++):
                    $link = "index.php?page=$i".($cari!=='' ? "&cari=".urlencode($cari) : '');
                    $active = $i==$page ? 'bg-blue-600 text-white font-semibold' : 'border border-slate-300 bg-white hover:bg-slate-50 text-slate-700';
                    ?>
                    <a href="<?php echo $link; ?>"
                       class="px-3 py-2 rounded-lg <?php echo $active; ?>"><?php echo $i; ?></a>
                <?php endfor; ?>

                <?php if ($page < $total_halaman):
                    $next = "index.php?page=".($page+1).($cari!=='' ? "&cari=".urlencode($cari) : '');
                    ?>
                    <a href="<?php echo $next; ?>"
                       class="inline-flex items-center px-3 py-2 border border-slate-300 rounded-lg bg-white hover:bg-slate-50 text-slate-700">
                        Next
                        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </a>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>

    <!-- Modal konfirmasi hapus -->
    <div x-show="modalOpen" x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4"
         @keydown.escape.window="modalOpen=false">
        <div @click.outside="modalOpen=false"
             class="w-full max-w-md bg-white rounded-xl shadow-xl p-6">
            <div class="flex items-start">
                <div class="w-10 h-10 rounded-lg bg-red-100 text-red-600 flex items-center justify-center mr-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-slate-800">Hapus Jabatan?</h3>
                    <p class="text-slate-600 mt-1">
                        Data jabatan <span class="font-semibold" x-text="hapusNama"></span> akan dihapus permanen.
                        Tindakan ini tidak dapat dibatalkan.
                    </p>
                </div>
            </div>

            <div class="mt-6 flex items-center justify-end gap-2">
                <button @click="modalOpen=false"
                        class="px-4 py-2 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700">
                    Batal
                </button>
                <a :href="`hapus.php?id=${hapusId}`"
                   class="px-4 py-2 rounded-lg bg-red-600 hover:bg-red-700 text-white font-medium">
                    Hapus
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Alpine.js -->
<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

<?php include '../templates/footer.php'; ?>
