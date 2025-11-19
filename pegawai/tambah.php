<?php
require_once __DIR__ . '/../config.php';
cekLogin();

if ($_SESSION['level'] !== 'admin') {
    header("Location: ../profil.php");
    exit;
}

// ambil data referensi jabatan & unit
$jabatan = mysqli_query($conn, "SELECT * FROM jabatan ORDER BY nama_jabatan ASC");
$unit    = mysqli_query($conn, "SELECT * FROM unit_kerja ORDER BY nama_unit ASC");

$error = '';
$success = '';

if (isset($_POST['simpan'])) {
    $nip    = mysqli_real_escape_string($conn, $_POST['nip']);
    $nama   = mysqli_real_escape_string($conn, $_POST['nama']);
    $jab    = mysqli_real_escape_string($conn, $_POST['jabatan']);
    $unitk  = mysqli_real_escape_string($conn, $_POST['unit_kerja']);
    $status = mysqli_real_escape_string($conn, $_POST['status_pegawai']);
    $nohp   = mysqli_real_escape_string($conn, $_POST['no_hp']);

    $namaFileBaru = '';

    // upload foto (opsional)
    if (!empty($_FILES['foto']['name'])) {
        $allowed = ['jpg','jpeg','png'];
        $maxSize = 2 * 1024 * 1024; // 2MB

        $origName = $_FILES['foto']['name'];
        $size     = $_FILES['foto']['size'];
        $tmp      = $_FILES['foto']['tmp_name'];
        $ext      = strtolower(pathinfo($origName, PATHINFO_EXTENSION));

        if (!in_array($ext, $allowed)) {
            $error = "Format foto harus JPG/PNG";
        } elseif ($size > $maxSize) {
            $error = "Ukuran foto maksimal 2MB";
        } else {
            $namaFileBaru = 'pegawai-' . time() . '.' . $ext;
            $tujuan = __DIR__ . '/../uploads/pegawai/' . $namaFileBaru;
            move_uploaded_file($tmp, $tujuan);
        }
    }

    if (empty($error)) {
        // simpan ke tabel pegawai
        $sql_insert = "INSERT INTO pegawai
                (nip, nama, jabatan, unit_kerja, status_pegawai, no_hp, foto, aktif)
                VALUES
                ('$nip','$nama','$jab','$unitk','$status','$nohp','$namaFileBaru',1)";
        mysqli_query($conn, $sql_insert);

        // id pegawai baru
        $pegawai_id_baru = mysqli_insert_id($conn);

        // buat akun otomatis
        if (function_exists('buatUserPegawaiOtomatis')) {
            buatUserPegawaiOtomatis($conn, $pegawai_id_baru, $nip, $nama);
        }

        tulisLog($conn, 'tambah', 'Tambah pegawai: ' . $nama);

        header("Location: index.php");
        exit;
    }
}

include '../templates/header.php';
?>

<!-- Header Section -->
<div class="mb-8">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-slate-800 mb-2 flex items-center">
                <svg class="w-9 h-9 mr-3 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                </svg>
                Tambah Pegawai Baru
            </h1>
            <p class="text-slate-600">Lengkapi form untuk menambahkan data pegawai</p>
        </div>
        <a href="index.php" class="inline-flex items-center px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg transition-colors duration-200">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Kembali
        </a>
    </div>
</div>

<!-- Error Message -->
<?php if ($error !== '') : ?>
    <div class="mb-6 bg-red-50 border-l-4 border-red-500 rounded-lg p-4 flex items-start animate-shake">
        <svg class="w-5 h-5 text-red-500 mr-3 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <div>
            <p class="text-red-800 font-medium">Error!</p>
            <p class="text-red-700 text-sm mt-1"><?php echo htmlspecialchars($error); ?></p>
        </div>
    </div>
<?php endif; ?>

<!-- Form Container -->
<div class="max-w-4xl">
    <form method="post" enctype="multipart/form-data" class="bg-white rounded-xl shadow-lg overflow-hidden">
        <!-- Form Header -->
        <div class="bg-gradient-to-r from-blue-500 to-blue-600 px-6 py-4">
            <h2 class="text-xl font-bold text-white flex items-center">
                <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Data Pegawai
            </h2>
        </div>

        <!-- Form Body -->
        <div class="p-6 space-y-6">
            <!-- Row 1: NIP & Nama -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- NIP -->
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">
                        NIP <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"/>
                            </svg>
                        </div>
                        <input 
                            type="text" 
                            name="nip" 
                            class="w-full pl-10 pr-4 py-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                            placeholder="Masukkan NIP"
                            required
                        >
                    </div>
                </div>

                <!-- Nama -->
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">
                        Nama Lengkap <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                        </div>
                        <input 
                            type="text" 
                            name="nama" 
                            class="w-full pl-10 pr-4 py-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                            placeholder="Masukkan nama lengkap"
                            required
                        >
                    </div>
                </div>
            </div>

            <!-- Row 2: Jabatan & Unit Kerja -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Jabatan -->
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">
                        Jabatan <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <select 
                            name="jabatan" 
                            class="w-full pl-10 pr-4 py-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 appearance-none bg-white"
                            required
                        >
                            <option value="">-- Pilih Jabatan --</option>
                            <?php while($j = mysqli_fetch_assoc($jabatan)) : ?>
                                <option value="<?php echo $j['nama_jabatan']; ?>"><?php echo $j['nama_jabatan']; ?></option>
                            <?php endwhile; ?>
                        </select>
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                            <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Unit Kerja -->
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">
                        Unit Kerja <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                        </div>
                        <select 
                            name="unit_kerja" 
                            class="w-full pl-10 pr-4 py-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 appearance-none bg-white"
                            required
                        >
                            <option value="">-- Pilih Unit Kerja --</option>
                            <?php while($u = mysqli_fetch_assoc($unit)) : ?>
                                <option value="<?php echo $u['nama_unit']; ?>"><?php echo $u['nama_unit']; ?></option>
                            <?php endwhile; ?>
                        </select>
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                            <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Row 3: Status Pegawai & No HP -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Status Pegawai -->
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">
                        Status Pegawai <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                            </svg>
                        </div>
                        <input 
                            type="text" 
                            name="status_pegawai" 
                            class="w-full pl-10 pr-4 py-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                            placeholder="PNS / Honorer"
                            required
                        >
                    </div>
                    <p class="text-xs text-slate-500 mt-1">Contoh: PNS atau Honorer</p>
                </div>

                <!-- No HP -->
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">
                        No HP
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                            </svg>
                        </div>
                        <input 
                            type="text" 
                            name="no_hp" 
                            class="w-full pl-10 pr-4 py-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                            placeholder="08xxxxxxxxxx"
                        >
                    </div>
                </div>
            </div>

            <!-- Foto Upload -->
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">
                    Foto Pegawai
                </label>
                <div class="flex items-center justify-center w-full">
                    <label class="flex flex-col items-center justify-center w-full h-40 border-2 border-slate-300 border-dashed rounded-lg cursor-pointer bg-slate-50 hover:bg-slate-100 transition-colors duration-200">
                        <div class="flex flex-col items-center justify-center pt-5 pb-6">
                            <svg class="w-10 h-10 mb-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                            </svg>
                            <p class="mb-2 text-sm text-slate-500"><span class="font-semibold">Klik untuk upload</span> atau drag and drop</p>
                            <p class="text-xs text-slate-400">PNG, JPG, JPEG (Maks. 2MB)</p>
                        </div>
                        <input type="file" name="foto" class="hidden" accept="image/*" onchange="previewImage(this)">
                    </label>
                </div>
                <div id="preview-container" class="mt-4 hidden">
                    <img id="preview-image" class="w-32 h-32 object-cover rounded-lg shadow-md mx-auto" alt="Preview">
                </div>
            </div>

            <!-- Info Box -->
            <div class="bg-blue-50 border-l-4 border-blue-500 rounded-lg p-4">
                <div class="flex items-start">
                    <svg class="w-5 h-5 text-blue-500 mr-3 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <div>
                        <p class="text-sm font-medium text-blue-800">Informasi</p>
                        <p class="text-sm text-blue-700 mt-1">Setelah data pegawai disimpan, sistem akan otomatis membuat akun login dengan username = NIP dan password default.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Form Footer -->
        <div class="bg-slate-50 px-6 py-4 border-t border-slate-200 flex items-center justify-end gap-3">
            <a href="index.php" class="inline-flex items-center px-5 py-2.5 bg-white border border-slate-300 hover:bg-slate-50 text-slate-700 rounded-lg transition-colors duration-200 font-medium">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
                Batal
            </a>
            <button 
                type="submit"
                name="simpan" 
                class="inline-flex items-center px-5 py-2.5 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white rounded-lg transition-all duration-200 font-medium shadow-lg hover:shadow-xl transform hover:-translate-y-0.5"
            >
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                Simpan Data
            </button>
        </div>
    </form>
</div>

<script>
function previewImage(input) {
    const preview = document.getElementById('preview-image');
    const container = document.getElementById('preview-container');
    
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        
        reader.onload = function(e) {
            preview.src = e.target.result;
            container.classList.remove('hidden');
        };
        
        reader.readAsDataURL(input.files[0]);
    }
}
</script>

<?php include '../templates/footer.php'; ?>