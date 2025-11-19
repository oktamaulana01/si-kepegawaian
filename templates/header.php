<?php
if (!isset($_SESSION)) session_start();
$level = $_SESSION['level'] ?? 'pegawai';
$username = $_SESSION['username'] ?? 'User';
$nama_lengkap = $_SESSION['nama_lengkap'] ?? $username;

// Ambil initial untuk avatar
$initial = strtoupper(substr($nama_lengkap, 0, 1));
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SI Kepegawaian - Dinas Ketahanan Pangan, Pertanian dan Perikanan</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <style>
        [x-cloak] { display: none !important; }
        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        .dropdown-enter {
            animation: slideDown 0.2s ease-out;
        }
    </style>
</head>
<body class="bg-slate-50">

<!-- Navbar -->
<nav class="bg-gradient-to-r from-blue-700 via-blue-800 to-blue-900 shadow-lg sticky top-0 z-50" x-data="{ mobileMenuOpen: false, profileOpen: false }">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">
            <!-- Logo & Brand -->
            <div class="flex items-center">
                <div class="flex-shrink-0 flex items-center">
                    <div class="w-10 h-10 bg-white rounded-lg flex items-center justify-center mr-3 shadow-md">
                        <svg class="w-6 h-6 text-blue-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                    </div>
                    <div class="hidden md:block">
                        <div class="text-white font-bold text-lg">SI Kepegawaian</div>
                        <div class="text-blue-200 text-xs">DKP3 Kota Banjarbaru</div>
                    </div>
                    <div class="md:hidden">
                        <div class="text-white font-bold text-base">SI Kepegawaian</div>
                    </div>
                </div>
            </div>

            <!-- Desktop Menu -->
            <div class="hidden md:flex items-center space-x-1">
                <?php if ($level === 'admin'): ?>
                    <a href="/si-kepegawaian/index.php" class="flex items-center px-3 py-2 rounded-lg text-white hover:bg-blue-600 transition-colors duration-200">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                        </svg>
                        Dashboard
                    </a>
                    <a href="/si-kepegawaian/pegawai/" class="flex items-center px-3 py-2 rounded-lg text-white hover:bg-blue-600 transition-colors duration-200">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                        Pegawai
                    </a>
                    <a href="/si-kepegawaian/jabatan/" class="flex items-center px-3 py-2 rounded-lg text-white hover:bg-blue-600 transition-colors duration-200">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                        Jabatan
                    </a>
                    <a href="/si-kepegawaian/unit-kerja/" class="flex items-center px-3 py-2 rounded-lg text-white hover:bg-blue-600 transition-colors duration-200">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                        Unit Kerja
                    </a>
                    <a href="/si-kepegawaian/log.php" class="flex items-center px-3 py-2 rounded-lg text-white hover:bg-blue-600 transition-colors duration-200">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Log
                    </a>
                <?php endif; ?>
            </div>

            <!-- Profile Dropdown & Mobile Menu Button -->
            <div class="flex items-center space-x-3">
                <!-- Profile Dropdown (Desktop) -->
                <div class="hidden md:block relative" x-data="{ open: false }">
                    <button @click="open = !open" class="flex items-center space-x-3 px-3 py-2 rounded-lg text-white hover:bg-blue-600 transition-colors duration-200">
                        <div class="w-9 h-9 bg-gradient-to-br from-blue-400 to-blue-500 rounded-full flex items-center justify-center shadow-md ring-2 ring-blue-300">
                            <span class="text-white font-bold text-sm"><?php echo $initial; ?></span>
                        </div>
                        <div class="text-left hidden lg:block">
                            <div class="text-sm font-semibold"><?php echo htmlspecialchars($nama_lengkap); ?></div>
                            <div class="text-xs text-blue-200 capitalize"><?php echo $level; ?></div>
                        </div>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>

                    <!-- Dropdown Menu -->
                    <div x-show="open"
                         x-cloak 
                         @click.away="open = false"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 transform scale-95"
                         x-transition:enter-end="opacity-100 transform scale-100"
                         x-transition:leave="transition ease-in duration-150"
                         x-transition:leave-start="opacity-100 transform scale-100"
                         x-transition:leave-end="opacity-0 transform scale-95"
                         class="absolute right-0 mt-2 w-56 bg-white rounded-xl shadow-xl ring-1 ring-black ring-opacity-5 overflow-hidden z-50">
                        
                        <!-- Profile Header -->
                        <div class="px-4 py-3 bg-gradient-to-r from-blue-50 to-blue-100 border-b border-blue-200">
                            <div class="flex items-center space-x-3">
                                <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-full flex items-center justify-center shadow-md">
                                    <span class="text-white font-bold text-lg"><?php echo $initial; ?></span>
                                </div>
                                <div>
                                    <p class="text-sm font-semibold text-slate-800"><?php echo htmlspecialchars($nama_lengkap); ?></p>
                                    <p class="text-xs text-slate-600 capitalize"><?php echo $level; ?></p>
                                </div>
                            </div>
                        </div>

                        <!-- Menu Items -->
                        <div class="py-2">
                            <a href="/si-kepegawaian/profil.php" class="flex items-center px-4 py-3 text-sm text-slate-700 hover:bg-blue-50 transition-colors duration-150">
                                <svg class="w-5 h-5 mr-3 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                                Profil Saya
                            </a>
                            <a href="/si-kepegawaian/ganti-password.php" class="flex items-center px-4 py-3 text-sm text-slate-700 hover:bg-blue-50 transition-colors duration-150">
                                <svg class="w-5 h-5 mr-3 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                                </svg>
                                Ganti Password
                            </a>
                        </div>

                        <!-- Logout -->
                        <div class="border-t border-slate-200">
                            <a href="/si-kepegawaian/logout.php" class="flex items-center px-4 py-3 text-sm text-red-700 hover:bg-red-50 transition-colors duration-150">
                                <svg class="w-5 h-5 mr-3 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                </svg>
                                Logout
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Mobile Menu Button -->
                <button @click="mobileMenuOpen = !mobileMenuOpen" class="md:hidden inline-flex items-center justify-center p-2 rounded-lg text-white hover:bg-blue-600 focus:outline-none">
                    <svg x-show="!mobileMenuOpen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                    <svg x-show="mobileMenuOpen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile Menu -->
    <div x-show="mobileMenuOpen" 
         x-cloak
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 transform -translate-y-2"
         x-transition:enter-end="opacity-100 transform translate-y-0"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 transform translate-y-0"
         x-transition:leave-end="opacity-0 transform -translate-y-2"
         class="md:hidden bg-blue-800 border-t border-blue-600">
        <div class="px-2 pt-2 pb-3 space-y-1">
            <!-- User Info Mobile -->
            <div class="px-3 py-3 bg-blue-700 rounded-lg mb-2">
                <div class="flex items-center space-x-3">
                    <div class="w-12 h-12 bg-gradient-to-br from-blue-400 to-blue-500 rounded-full flex items-center justify-center shadow-md">
                        <span class="text-white font-bold text-lg"><?php echo $initial; ?></span>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-white"><?php echo htmlspecialchars($nama_lengkap); ?></p>
                        <p class="text-xs text-blue-200 capitalize"><?php echo $level; ?></p>
                    </div>
                </div>
            </div>

            <?php if ($level === 'admin'): ?>
                <a href="/si-kepegawaian/index.php" class="flex items-center px-3 py-2 rounded-lg text-white hover:bg-blue-600">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                    Dashboard
                </a>
                <a href="/si-kepegawaian/pegawai/" class="flex items-center px-3 py-2 rounded-lg text-white hover:bg-blue-600">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                    Pegawai
                </a>
                <a href="/si-kepegawaian/jabatan/" class="flex items-center px-3 py-2 rounded-lg text-white hover:bg-blue-600">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                    Jabatan
                </a>
                <a href="/si-kepegawaian/unit-kerja/" class="flex items-center px-3 py-2 rounded-lg text-white hover:bg-blue-600">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                    Unit Kerja
                </a>
                <a href="/si-kepegawaian/log.php" class="flex items-center px-3 py-2 rounded-lg text-white hover:bg-blue-600">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Log
                </a>
            <?php endif; ?>

            <div class="border-t border-blue-600 my-2"></div>

            <a href="/si-kepegawaian/profil.php" class="flex items-center px-3 py-2 rounded-lg text-white hover:bg-blue-600">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
                Profil Saya
            </a>
            <a href="/si-kepegawaian/ganti-password.php" class="flex items-center px-3 py-2 rounded-lg text-white hover:bg-blue-600">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                </svg>
                Ganti Password
            </a>
            <a href="/si-kepegawaian/logout.php" class="flex items-center px-3 py-2 rounded-lg text-red-300 hover:bg-red-600 hover:text-white">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                </svg>
                Logout
            </a>
        </div>
    </div>
</nav>

<!-- Main Content Container -->
<main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">