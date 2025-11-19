<?php
// config.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$host = "localhost";
$user = "root";
$pass = "";
$db   = "si_kepegawaian";

$conn = mysqli_connect($host, $user, $pass, $db);
if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

// ini cuma ngasih tau FPDF folder font-nya di mana
if (!defined('FPDF_FONTPATH')) {
    define('FPDF_FONTPATH', __DIR__ . '/font/');
}

if (!function_exists('cekLogin')) {
    function cekLogin() {
        if (!isset($_SESSION['login'])) {
            header("Location: /si-kepegawaian/login.php");
            exit;
        }
    }
    function tulisLog($conn, $aksi, $keterangan = '')
{
    if (!isset($_SESSION['username'])) {
        $user = 'guest';
    } else {
        $user = $_SESSION['username'];
    }

    $aksi       = mysqli_real_escape_string($conn, $aksi);
    $keterangan = mysqli_real_escape_string($conn, $keterangan);
    $waktu      = date('Y-m-d H:i:s');

    $sql = "INSERT INTO log_aktivitas (username, aksi, keterangan, waktu)
            VALUES ('$user', '$aksi', '$keterangan', '$waktu')";
    mysqli_query($conn, $sql);
}
function buatUserPegawaiOtomatis($conn, $pegawai_id, $nip, $nama)
{
    // kalau nggak ada NIP, skip aja
    if (empty($nip)) return;

    $nip_safe  = mysqli_real_escape_string($conn, $nip);
    $nama_safe = mysqli_real_escape_string($conn, $nama);

    // cek apakah user dengan username NIP ini sudah ada
    $cek = mysqli_query($conn, "SELECT id FROM users WHERE username = '$nip_safe' LIMIT 1");
    if (mysqli_num_rows($cek) > 0) {
        return; // sudah ada, jangan buat lagi
    }

    // sesuaikan dengan cara loginmu
    // kalau login kamu pakai md5:
    $password_hash = md5($nip_safe);
    // kalau login kamu pakai plain text, ganti jadi:
    // $password_hash = $nip_safe;

    // INSERT-nya harus ikut nama_lengkap karena tabel kamu minta itu
    $sql = "
        INSERT INTO users (username, password, level, pegawai_id, nama_lengkap)
        VALUES ('$nip_safe', '$password_hash', 'pegawai', $pegawai_id, '$nama_safe')
    ";
    mysqli_query($conn, $sql);
}


}
