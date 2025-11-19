<?php
require 'config.php';
cekLogin(); // optional, boleh ada atau tidak

// catat sebelum hancurkan session
$username = $_SESSION['username'] ?? '';
tulisLog($conn, 'logout', 'Logout user: ' . $username);


session_destroy();
header("Location: login.php");
exit;
