<?php
session_start();
// Cek status login user
$is_logged_in = isset($_SESSION['login']); // baris bawaan index.php
$username = $is_logged_in ? $_SESSION['username'] : ''; // baris bawaan index.php
$email = $is_logged_in ? (isset($_SESSION['email']) ? $_SESSION['email'] : $username . '@gmail.com') : ''; // baris bawaan index.php

// TAMBAHKAN BARIS INI: Ambil data gambar dari session jika ada
$avatar_data = $is_logged_in && isset($_SESSION['avatar']) ? $_SESSION['avatar'] : '';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Market.in - Toko Top Up Game Terpercaya</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://unpkg.com/swiper@8/swiper-bundle.min.css"/>
    <style>
        /* Dropdown Profil Menu Navbar */
        .profile-dropdown-wrapper { position: relative; cursor: pointer; }
        .dropdown-menu-box {
            display: none;
            position: absolute;
            right: 0;
            top: 100%;
            margin-top: 0.5rem;
            width: 220px;
            background-color: #18181b;
            border: 1px solid #27272a;
            border-radius: 0.75rem;
            padding: 0.5rem;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.5);
            z-index: 100;
        }
        .dropdown-menu-box.show { display: block; }
        .dropdown-item {
            display: block;
            padding: 0.5rem 0.75rem;
            font-size: 0.875rem;
            color: #d4d4d8;
            border-radius: 0.375rem;
            transition: all 0.2s;
        }
        .dropdown-item:hover { background-color: #27272a; color: #ffffff; }

        /* Sembunyikan Scrollbar Bawaan Browser Agar Carousel Mulus */
        .hide-scrollbar::-webkit-scrollbar {
            display: none;
        }
        .hide-scrollbar {
            -ms-overflow-style: none;  /* IE and Edge */
            scrollbar-width: none;  /* Firefox */
        }

        /* STYLE BARU: Dropdown Hasil Pencarian di Bawah Search Bar */
        .search-results-box {
            display: none;
            position: absolute;
            left: 0;
            top: 110%;
            width: 100%;
            max-height: 280px;
            overflow-y: auto;
            background-color: #111113;
            border: 1px solid #27272a;
            border-radius: 1rem;
            padding: 0.5rem;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.7);
            z-index: 200;
        }
        .search-results-box.show { display: block; }
    </style>
</head>
<body class="bg-[#0a0a0a] text-white">

    <header class="bg-[#0a0a0a]/90 border-b border-zinc-800 backdrop-blur-md px-6 py-4 fixed top-0 left-0 w-full z-50">
        <div class="max-w-[1200px] mx-auto flex flex-wrap justify-between items-center gap-4">
            
            <div class="flex items-center gap-6">
                <a href="index.php">
                    <span class="text-xl font-bold tracking-wider text-[#f3af22]">MARKET.<span class="text-white">IN</span></span>
                </a>
                <div class="relative">
                    <input type="text" id="gameSearchInput" autocomplete="off" placeholder="Search Here" class="bg-zinc-900 border border-zinc-700 text-sm rounded-full px-4 py-1.5 w-64 focus:outline-none focus:border-[#f3af22] text-white placeholder-zinc-500">
                    
                    <div id="searchResultsDropdown" class="search-results-box hide-scrollbar space-y-1">
                        <a href="topup-ml.php" data-search="mobile legends mlbb ml" class="flex items-center gap-3 p-2 rounded-xl text-xs text-zinc-300 hover:bg-zinc-800 hover:text-white transition">
                            <img src="img/logo-ml.jpg" class="w-8 h-8 rounded-lg object-cover border border-zinc-700">
                            <div>
                                <span class="block font-bold">Mobile Legends</span>
                                <span class="text-[10px] text-zinc-500">Moonton</span>
                            </div>
                        </a>
                        <a href="topup-ff.php" data-search="free fire ff garena" class="flex items-center gap-3 p-2 rounded-xl text-xs text-zinc-300 hover:bg-zinc-800 hover:text-white transition">
                            <img src="img/logo-ff.png" class="w-8 h-8 rounded-lg object-cover border border-zinc-700">
                            <div>
                                <span class="block font-bold">Free Fire</span>
                                <span class="text-[10px] text-zinc-500">Garena</span>
                            </div>
                        </a>
                        <a href="topup-pubg.php" data-search="pubg mobile pubgm uc" class="flex items-center gap-3 p-2 rounded-xl text-xs text-zinc-300 hover:bg-zinc-800 hover:text-white transition">
                            <img src="img/logo-pubg.jpg" class="w-8 h-8 rounded-lg object-cover border border-zinc-700">
                            <div>
                                <span class="block font-bold">PUBG Mobile</span>
                                <span class="text-[10px] text-zinc-500">Tencent Games</span>
                            </div>
                        </a>
                        <a href="topup-roblox.php" data-search="roblox robux giftcard" class="flex items-center gap-3 p-2 rounded-xl text-xs text-zinc-300 hover:bg-zinc-800 hover:text-white transition">
                            <img src="img/logo-roblox.webp" class="w-8 h-8 rounded-lg object-cover border border-zinc-700">
                            <div>
                                <span class="block font-bold">Roblox</span>
                                <span class="text-[10px] text-zinc-500">Roblox Corp</span>
                            </div>
                        </a>
                        <div id="noSearchMatch" class="text-center py-3 text-zinc-600 text-[11px] hidden">⚠️ Game tidak ditemukan...</div>
                    </div>
                </div>
            </div>
            
            <nav class="flex items-center">
                <ul class="flex items-center gap-6 m-0 p-0 list-none">
                    <li>
                        <a href="index.php" class="flex items-center gap-2 text-sm font-semibold text-[#f3af22]">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-house-door-fill" viewBox="0 0 16 16">
                                <path d="M6.5 14.5v-3.505c0-.245.25-.495.5-.495h2c.25 0 .5.25.5.5v3.5a.5.5 0 0 0 .5.5h4a.5.5 0 0 0 .5-.5v-7a.5.5 0 0 0-.146-.354L13 5.793V2.5a.5.5 0 0 0-.5-.5h-1a.5.5 0 0 0-.5.5v1.293L8.354 1.146a.5.5 0 0 0-.708 0l-6 6A.5.5 0 0 0 1.5 7.5v7a.5.5 0 0 0 .5.5h4a.5.5 0 0 0 .5-.5z"/>
                            </svg>
                            <span>Home</span>
                        </a>
                    </li>
                    <li>
                        <a href="daftar-game.php" class="flex items-center gap-2 text-sm font-semibold text-zinc-400 hover:text-[#f3af22] transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-gamepad" viewBox="0 0 16 16">
                                <path d="M2 9a2 2 0 1 0 0 4 2 2 0 0 0 0-4zm12 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4z"/>
                                <path d="M6.257 6.434c-.143-.304-.436-.504-.757-.504H3.23c-.33 0-.618.21-.748.513L1.31 9.17A1 1 0 0 0 2.221 10.5h1.761c.424 0 .79-.272.923-.678L5.4 8.25h1.2c.424 0 .79-.272.923-.678l.493-1.488a.5.5 0 0 0-.482-.66H6.257zm6.286 0c-.33 0-.623.204-.757.504l-.494 1.488a1.002 1.002 0 0 0 .923.678h1.2l.493 1.482c.134.406.499.678.923.678h1.762a1 1 0 0 0 .91-1.33l-1.171-2.737a.807.807 0 0 0-.748-.513h-2.268z"/>
                                <path d="M8 2a5.978 5.978 0 0 0-4.757 2.336A4.953 4.953 0 0 1 8 6a4.953 4.953 0 0 1 4.757-1.664A5.978 5.978 0 0 0 8 2z"/>
                            </svg>
                            <span>Daftar Game</span>
                        </a>
                    </li>

                    <?php if ($is_logged_in) : ?>
                        <li class="profile-dropdown-wrapper">
                            <div class="flex items-center gap-3 bg-zinc-900 border border-zinc-800 px-3 py-1.5 rounded-xl border-gold cursor-pointer" id="profileTrigger">
                                <div class="text-gold flex items-center justify-center">
                                    <?php if (!empty($avatar_data)): ?>
                                        <img src="<?php echo $avatar_data; ?>" class="w-5 h-5 rounded-full object-cover">
                                    <?php else: ?>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-person-circle" viewBox="0 0 16 16">
                                            <path d="M11 6a3 3 0 1 1-6 0 3 3 0 0 1 6 0z"/>
                                            <path fill-rule="evenodd" d="M0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8zm8-7a7 7 0 0 0-5.468 11.37C3.242 11.226 4.805 10 8 10s4.757 1.225 5.468 2.37A7 7 0 0 0 8 1z"/>
                                        </svg>
                                    <?php endif; ?>
                                </div>
                                <div class="text-left text-xs hidden sm:block">
                                    <span class="block font-medium text-white">Halo, <?php echo htmlspecialchars($username); ?></span>
                                    <span class="block text-[10px] text-zinc-500">Akun Member</span>
                                </div>
                            </div>

                            <div class="dropdown-menu-box" id="myDropdown">
                                <div class="p-2 border-b border-zinc-800 mb-2 flex items-center gap-2">
                                    <div class="text-[#f3af22] flex items-center justify-center">
                                        <?php if (!empty($avatar_data)): ?>
                                            <img src="<?php echo $avatar_data; ?>" class="w-6 h-6 rounded-full object-cover">
                                        <?php else: ?>
                                            👤
                                        <?php endif; ?>
                                    </div>
                                    <div>
                                        <h4 class="text-sm font-bold text-white"><?php echo htmlspecialchars($username); ?></h4>
                                        <p class="text-[10px] text-zinc-500"><?php echo htmlspecialchars($email); ?></p>
                                    </div>
                                </div>
                                <a href="profile.php" class="dropdown-item">👤 Profile</a>
                                <a href="edit-account.php" class="dropdown-item">⚙️ Edit Account</a>
                                <a href="history.php" class="dropdown-item">📜 History Transaksi</a>
                                <a href="bantuan.php" class="dropdown-item">❓ Bantuan</a>
                                <div class="border-t border-zinc-800 my-1"></div>
                                <a href="logout.php" class="dropdown-item text-red-400 hover:text-red-300">🚪 Log Out</a>
                            </div>
                        </li>
                    <?php else : ?>
                        <li>
                            <a href="login.php" class="border border-[#f3af22] text-[#f3af22] px-4 py-1.5 rounded-full text-sm font-medium hover:bg-[#f3af22] hover:text-black transition flex items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" class="bi bi-box-arrow-in-right" viewBox="0 0 16 16">
                                    <path fill-rule="evenodd" d="M6 3.5a.5.5 0 0 1 .5-.5h8a.5.5 0 0 1 .5 .5v9a.5.5 0 0 1-.5 .5h-8a.5.5 0 0 1-.5-.5v-2a.5.5 0 0 0-1 0v2A1.5 1.5 0 0 0 6.5 14h8a1.5 1.5 0 0 0 1.5-1.5v-9A1.5 1.5 0 0 0 14.5 2h-8A1.5 1.5 0 0 0 5 3.5v2a.5.5 0 0 0 1 0v-2z"/>
                                    <path fill-rule="evenodd" d="M11.854 8.354a.5.5 0 0 0 0-.708l-3-3a.5.5 0 1 0-.708.708L10.293 7.5H1.5a.5.5 0 0 0 0 1h8.793l-2.147 2.146a.5.5 0 0 0 .708.708l3-3z"/>
                                </svg>
                                <span>Masuk</span>
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    </header>

    <div class="max-w-[1200px] mx-auto px-6 pt-28 pb-4">
        <div class="swiper rounded-2xl overflow-hidden border border-zinc-800/80 shadow-2xl relative aspect-[12/5] md:aspect-[21/8]">
            <div class="swiper-wrapper">
                
                <div class="swiper-slide relative">
                    <img src="img/banner1.png" alt="Promo 1" class="w-full h-full object-cover">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/20 to-transparent flex flex-col justify-end p-6 md:p-12">
                        <span class="text-xs font-bold text-black bg-[#f3af22] px-2.5 py-1 rounded-md w-fit mb-2 uppercase tracking-wider">Event Terbatas</span>
                        <h2 class="text-xl md:text-3xl font-black text-white tracking-wide">Weekly Diamond Pass MLBB</h2>
                        <p class="text-xs md:text-sm text-zinc-300 mt-1 max-w-md">Makin hemat top up mingguan otomatis pakai kode promo khusus pengguna baru.</p>
                    </div>
                </div>

                <div class="swiper-slide relative">
                    <img src="img/banner2.png" alt="Promo 2" class="w-full h-full object-cover">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/20 to-transparent flex flex-col justify-end p-6 md:p-12">
                        <span class="text-xs font-bold text-black bg-[#f3af22] px-2.5 py-1 rounded-md w-fit mb-2 uppercase tracking-wider">Diskon Kilat</span>
                        <h2 class="text-xl md:text-3xl font-black text-white tracking-wide">Free Fire Mega Sale</h2>
                        <p class="text-xs md:text-sm text-zinc-300 mt-1 max-w-md">Dapatkan bonus up to 50% diamonds ekstra di setiap kelipatan pembelian tertentu.</p>
                    </div>
                </div>

                <div class="swiper-slide relative">
                    <img src="img/banner3.png" alt="Promo 3" class="w-full h-full object-cover">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/20 to-transparent flex flex-col justify-end p-6 md:p-12">
                        <span class="text-xs font-bold text-black bg-[#f3af22] px-2.5 py-1 rounded-md w-fit mb-2 uppercase tracking-wider">LEVEL UP</span>
                        <h2 class="text-xl md:text-3xl font-black text-white tracking-wide">UC PUBG MOBILE</h2>
                        <p class="text-xs md:text-sm text-zinc-300 mt-1 max-w-md">Dapatkan harga termurah dengan disc 10% terbatas, ayo segera beli.</p>
                    </div>
                </div>

                <div class="swiper-slide relative">
                    <img src="img/banner4.png" alt="Promo 4" class="w-full h-full object-cover">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/20 to-transparent flex flex-col justify-end p-6 md:p-12">
                        <span class="text-xs font-bold text-black bg-[#f3af22] px-2.5 py-1 rounded-md w-fit mb-2 uppercase tracking-wider">Roblox Voucher</span>
                        <h2 class="text-xl md:text-3xl font-black text-white tracking-wide">Roblox Gift Card</h2>
                        <p class="text-xs md:text-sm text-zinc-300 mt-1 max-w-md">Sekrung beli Roblox Gift Card resmi makin mudah dengan QRIS instan otomatis sukses.</p>
                    </div>
                </div>

            </div>
            <div class="swiper-pagination !bottom-4"></div>
        </div>
    </div>

    <div class="max-w-[1200px] mx-auto px-6 py-4">
        <div class="flex gap-4 md:gap-6 overflow-x-auto pb-4 hide-scrollbar snap-x snap-mandatory">
            
            <div class="min-w-[260px] sm:min-w-[300px] md:flex-1 snap-start group relative overflow-hidden rounded-2xl border border-zinc-800/80 shadow-lg aspect-[16/8] bg-zinc-900 transition-all duration-300 hover:border-[#f3af22]/40 hover:-translate-y-1 hover:shadow-[0_10px_20px_rgba(243,175,34,0.05)]">
                <img src="img/promocode.png" alt="Promo Mini 1" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105">
                <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/10 to-transparent p-4 flex flex-col justify-end">
                    <span class="text-[10px] font-bold text-[#f3af22] uppercase tracking-wider">TOPUPM4RKETIN</span>
                </div>
            </div>

            <div class="min-w-[260px] sm:min-w-[300px] md:flex-1 snap-start group relative overflow-hidden rounded-2xl border border-zinc-800/80 shadow-lg aspect-[16/8] bg-zinc-900 transition-all duration-300 hover:border-[#f3af22]/40 hover:-translate-y-1 hover:shadow-[0_10px_20px_rgba(243,175,34,0.05)]">
                <img src="img/promocode.png" alt="Promo Mini 2" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105">
                <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/10 to-transparent p-4 flex flex-col justify-end">
                    <span class="text-[10px] font-bold text-[#f3af22] uppercase tracking-wider">TOPUPM4RKETIN</span>
                </div>
            </div>

        </div>
    </div>

    <div class="wrap-card max-w-[1200px] mx-auto px-6 py-4">
        <h2 class="text-lg font-bold tracking-wider text-[#f3af22] mb-6 uppercase flex items-center gap-2">
            <span class="w-1.5 h-5 bg-[#f3af22] rounded-full"></span>
            Daftar Game Terpopuler
        </h2>

        <!-- 🛠️ UPDATE: PERUBAHAN WADAH UTAMA MENJADI GRID RESPONSIF YANG SEJAJAR -->
        <div class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-4 gap-4 md:gap-6 pb-4">
            
            <!-- 1. Mobile Legends (Lebar diubah ke w-full) -->
            <a href="topup-ml.php" class="w-full group bg-zinc-900/40 border border-zinc-800/80 rounded-2xl overflow-hidden backdrop-blur-sm transition-all duration-300 hover:border-[#f3af22]/60 hover:bg-zinc-900/80 hover:-translate-y-1.5 hover:shadow-[0_10px_30px_rgba(243,175,34,0.1)] flex flex-col justify-between">
                <div class="relative aspect-square overflow-hidden bg-zinc-800">
                    <img src="img/logo-ml.jpg" alt="Mobile Legends" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-end p-3">
                        <span class="text-[11px] font-bold text-black bg-[#f3af22] px-2 py-1 rounded-md w-full text-center shadow-lg uppercase tracking-wider">Top Up</span>
                    </div>
                </div>
                <div class="p-3 text-center">
                    <h3 class="font-bold text-xs sm:text-sm text-zinc-200 group-hover:text-[#f3af22] transition-colors truncate">Mobile Legends</h3>
                    <p class="text-[10px] text-zinc-500 mt-0.5 truncate">Moonton</p>
                </div>
            </a>

            <!-- 2. Free Fire (Lebar diubah ke w-full) -->
            <a href="topup-ff.php" class="w-full group bg-zinc-900/40 border border-zinc-800/80 rounded-2xl overflow-hidden backdrop-blur-sm transition-all duration-300 hover:border-[#f3af22]/60 hover:bg-zinc-900/80 hover:-translate-y-1.5 hover:shadow-[0_10px_30px_rgba(243,175,34,0.1)] flex flex-col justify-between">
                <div class="relative aspect-square overflow-hidden bg-zinc-800">
                    <img src="img/logo-ff.png" alt="Free Fire" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-end p-3">
                        <span class="text-[11px] font-bold text-black bg-[#f3af22] px-2 py-1 rounded-md w-full text-center shadow-lg uppercase tracking-wider">Top Up</span>
                    </div>
                </div>
                <div class="p-3 text-center">
                    <h3 class="font-bold text-xs sm:text-sm text-zinc-200 group-hover:text-[#f3af22] transition-colors truncate">Free Fire</h3>
                    <p class="text-[10px] text-zinc-500 mt-0.5 truncate">Garena</p>
                </div>
            </a>

            <!-- 3. PUBG Mobile (Lebar diubah ke w-full) -->
            <a href="topup-pubg.php" class="w-full group bg-zinc-900/40 border border-zinc-800/80 rounded-2xl overflow-hidden backdrop-blur-sm transition-all duration-300 hover:border-[#f3af22]/60 hover:bg-zinc-900/80 hover:-translate-y-1.5 hover:shadow-[0_10px_30px_rgba(243,175,34,0.1)] flex flex-col justify-between">
                <div class="relative aspect-square overflow-hidden bg-zinc-800">
                    <img src="img/logo-pubg.jpg" alt="PUBG Mobile" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-end p-3">
                        <span class="text-[11px] font-bold text-black bg-[#f3af22] px-2 py-1 rounded-md w-full text-center shadow-lg uppercase tracking-wider">Top Up</span>
                    </div>
                </div>
                <div class="p-3 text-center">
                    <h3 class="font-bold text-xs sm:text-sm text-zinc-200 group-hover:text-[#f3af22] transition-colors truncate">PUBG Mobile</h3>
                    <p class="text-[10px] text-zinc-500 mt-0.5 truncate">Tencent Games</p>
                </div>
            </a>

            <!-- 4. Roblox (Lebar diubah ke w-full) -->
            <a href="topup-roblox.php" class="w-full group bg-zinc-900/40 border border-zinc-800/80 rounded-2xl overflow-hidden backdrop-blur-sm transition-all duration-300 hover:border-[#f3af22]/60 hover:bg-zinc-900/80 hover:-translate-y-1.5 hover:shadow-[0_10px_30px_rgba(243,175,34,0.1)] flex flex-col justify-between">
                <div class="relative aspect-square overflow-hidden bg-zinc-800">
                    <img src="img/logo-roblox.webp" alt="Roblox" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-end p-3">
                        <span class="text-[11px] font-bold text-black bg-[#f3af22] px-2 py-1 rounded-md w-full text-center shadow-lg uppercase tracking-wider">Top Up</span>
                    </div>
                </div>
                <div class="p-3 text-center">
                    <h3 class="font-bold text-xs sm:text-sm text-zinc-200 group-hover:text-[#f3af22] transition-colors truncate">Roblox</h3>
                    <p class="text-[10px] text-zinc-500 mt-0.5 truncate">Roblox Corp</p>
                </div>
            </a>

        </div>
    </div>

    <div class="max-w-[1200px] mx-auto px-6 py-10">
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
            
            <div class="border border-zinc-800/80 rounded-xl p-6 bg-zinc-900/40 flex flex-col items-center text-center gap-3 transition-all duration-300 hover:border-[#f3af22]/40 hover:shadow-[0_4px_20px_rgba(243,175,34,0.05)]">
                <div class="text-[#f3af22]">
                    <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor" class="bi bi-globe" viewBox="0 0 16 16">
                        <path d="M0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8m7.5-6.923c-.67.424-1.248 1.14-1.64 2.024H7.5zM6.588 4.5H4.122A7 7 0 0 1 7.5 1.077zM4.122 5.5H7.5v2H3.603a7 7 0 0 1 .52-2M8.5 1.077A7 7 0 0 1 11.878 4.5H9.412zM9.412 5.5h3.378a7 7 0 0 1-.52 2H8.5zM3.603 8.5H7.5v2H4.122a7 7 0 0 1-.52-2m6.275 2H11.88a7 7 0 0 1-.52-2H8.5zM8.5 11.5h1.912c.392.884.97 1.6 1.64 2.024A7 7 0 0 1 8.5 14.923zm-.912 3.423A7 7 0 0 1 4.122 11.5H6.54c.392.884.97 1.6 1.64 2.024zm2.324-3.423h2.466a7 7 0 0 1-.52 2H11.88zM3.603 11.5H6.07a8 8 0 0 0 1.64 2.024 7 7 0 0 1-4.107-2.024"/>
                    </svg>
                </div>
                <h3 class="text-sm font-bold text-[#f3af22] tracking-wide uppercase">Market.In Website</h3>
                <p class="text-xs text-zinc-400 leading-relaxed">Website platform top-up game terbesar, tercepat, dan paling terpercaya nomor satu di Indonesia.</p>
            </div>

            <div class="border border-zinc-800/80 rounded-xl p-6 bg-zinc-900/40 flex flex-col items-center text-center gap-3 transition-all duration-300 hover:border-[#f3af22]/40 hover:shadow-[0_4px_20px_rgba(243,175,34,0.05)]">
                <div class="text-[#f3af22]">
                    <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor" class="bi bi-lightning-charge-fill" viewBox="0 0 16 16">
                        <path d="M11.251.068a.5.5 0 0 1 .227.58L9.677 6.5H13a.5.5 0 0 1 .364.843l-8 8.5a.5.5 0 0 1-.842-.49L6.323 9.5H3a.5.5 0 0 1-.364-.843l8-8.5a.5.5 0 0 1 .615-.09z"/>
                    </svg>
                </div>
                <h3 class="text-sm font-bold text-[#f3af22] tracking-wide uppercase">Dapat Dalam Hitungan Detik</h3>
                <p class="text-xs text-zinc-400 leading-relaxed">Hanya dibutuhkan beberapa detik saja untuk merampungkan verifikasi pembayaran produk di Market.In.</p>
            </div>

            <div class="border border-zinc-800/80 rounded-xl p-6 bg-zinc-900/40 flex flex-col items-center text-center gap-3 transition-all duration-300 hover:border-[#f3af22]/40 hover:shadow-[0_4px_20px_rgba(243,175,34,0.05)]">
                <div class="text-[#f3af22]">
                    <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor" class="bi bi-send-check-fill" viewBox="0 0 16 16">
                        <path d="M15.964.686a.5.5 0 0 0-.65-.65L.767 5.855H.766l-.452.18a.5.5 0 0 0-.082.887l.41.26.001.002 4.995 3.178 1.59 2.498c.15.236.46.301.68.142l1.014-.725a.5.5 0 0 1 .657-.02l3.414 2.56a.5.5 0 0 0 .763-.344l1.8-12.338zm-6.61 7.218-5.329-3.393L13.1 1.748z"/>
                    </svg>
                </div>
                <h3 class="text-sm font-bold text-[#f3af22] tracking-wide uppercase">Pengiriman Instan</h3>
                <p class="text-xs text-zinc-400 leading-relaxed">Item atau barang diamond yang kamu beli akan otomatis dikirim masuk ke akun game Anda tanpa delay.</p>
            </div>

            <div class="border border-zinc-800/80 rounded-xl p-6 bg-zinc-900/40 flex flex-col items-center text-center gap-3 transition-all duration-300 hover:border-[#f3af22]/40 hover:shadow-[0_4px_20px_rgba(243,175,34,0.05)]">
                <div class="text-[#f3af22]">
                    <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor" class="bi bi-wallet2" viewBox="0 0 16 16">
                        <path d="M12.136.326A1.5 1.5 0 0 1 14 1.78V3h.5A1.5 1.5 0 0 1 16 4.5v9a1.5 1.5 0 0 1-1.5 1.5h-13A1.5 1.5 0 0 1 0 13.5v-9a1.5 1.5 0 0 1 1.432-1.499zM5.562 3H13V1.78a.5.5 0 0 0-.621-.484zM1.5 4a.5.5 0 0 0-.5.5v9a.5.5 0 0 0 .5.5h13a.5.5 0 0 0 .5-.5v-9a.5.5 0 0 0-.5-.5z"/>
                    </svg>
                </div>
                <h3 class="text-sm font-bold text-[#f3af22] tracking-wide uppercase">Metode Pembayaran Terbaik</h3>
                <p class="text-xs text-zinc-400 leading-relaxed">Kami menawarkan begitu banyak pilihan pembayaran mulai dari QRIS instan, e-wallet, hingga transfer bank.</p>
            </div>

            <div class="border border-zinc-800/80 rounded-xl p-6 bg-zinc-900/40 flex flex-col items-center text-center gap-3 transition-all duration-300 hover:border-[#f3af22]/40 hover:shadow-[0_4px_20px_rgba(243,175,34,0.05)]">
                <div class="text-[#f3af22]">
                    <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor" class="bi bi-headset" viewBox="0 0 16 16">
                        <path d="M8 1a5 5 0 0 0-5 5v1h1a1 1 0 0 1 1 1v3a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1V6a6 6 0 1 1 12 0v6a2.5 2.5 0 0 1-2.5 2.5H9.366a1 1 0 0 1-.866-.5l-1.177-2.06A1 1 0 0 1 8.2 11.5H11a.5.5 0 0 0 .5-.5V8a1 1 0 0 1 1-1h1V6a5 5 0 0 0-5-5z"/>
                    </svg>
                </div>
                <h3 class="text-sm font-bold text-[#f3af22] tracking-wide uppercase">Layanan Pelanggan 24/7</h3>
                <p class="text-xs text-zinc-400 leading-relaxed">Tim customer service terbaik kami selalu siap mendampingi dan membantu kendala Anda kapan pun.</p>
            </div>

            <div class="border border-zinc-800/80 rounded-xl p-6 bg-zinc-900/40 flex flex-col items-center text-center gap-3 transition-all duration-300 hover:border-[#f3af22]/40 hover:shadow-[0_4px_20px_rgba(243,175,34,0.05)]">
                <div class="text-[#f3af22]">
                    <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor" class="bi bi-percent" viewBox="0 0 16 16">
                        <path d="M13.442 2.558a.625.625 0 0 1 0 .884l-10 10a.625.625 0 1 1-.884-.884l10-10a.625.625 0 0 1 .884 0M4.5 6a1.5 1.5 0 1 1 0-3 1.5 1.5 0 0 1 0 3m0-1a.5.5 0 1 0 0-1 .5.5 0 0 0 0 1m7 8a1.5 1.5 0 1 1 0-3 1.5 1.5 0 0 1 0 3m0-1a.5.5 0 1 0 0-1 .5.5 0 0 0 0 1"/>
                    </svg>
                </div>
                <h3 class="text-sm font-bold text-[#f3af22] tracking-wide uppercase">Promosi-Promosi Menarik</h3>
                <p class="text-xs text-zinc-400 leading-relaxed">Para gamers bisa mengandalkan platform kami karena Market.In selalu menawarkan penawaran diskon spesial.</p>
            </div>

            <div class="border border-zinc-800/80 rounded-xl p-6 bg-zinc-900/40 flex flex-col items-center text-center gap-3 transition-all duration-300 hover:border-[#f3af22]/40 hover:shadow-[0_4px_20px_rgba(243,175,34,0.05)]">
                <div class="text-[#f3af22]">
                    <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor" class="bi bi-shield-fill-check" viewBox="0 0 16 16">
                        <path d="M8 0c-.69 0-1.843.265-2.928.56-1.11.3-2.229.655-2.887.87a1.54 1.54 0 0 0-1.044 1.262c-.596 4.477.787 7.795 2.465 9.99a11.775 11.775 0 0 0 2.517 2.453c.386.273.744.482 1.048.625.28.132.581.24.829.24s.548-.108.829-.24a7.159 7.159 0 0 0 1.048-.625 11.777 11.777 0 0 0 2.517-2.453c1.678-2.195 3.061-5.513 2.465-9.99a1.54 1.54 0 0 0-1.044-1.263 62.439 62.439 0 0 0-2.887-.87C9.843.266 8.69 0 8 0zm2.146 5.146a.5.5 0 0 1 .708.708l-3 3a.5.5 0 0 1-.708 0l-1.5-1.5a.5.5 0 1 1 .708-.708L7.5 7.793l2.646-2.647z"/>
                    </svg>
                </div>
                <h3 class="text-sm font-bold text-[#f3af22] tracking-wide uppercase">100% Aman & Legal</h3>
                <p class="text-xs text-zinc-400 leading-relaxed">Jaminan keamanan penuh karena kami menggunakan jalur API integrasi game langsung resmi bebas ban.</p>
            </div>

            <div class="border border-zinc-800/80 rounded-xl p-6 bg-zinc-900/40 flex flex-col items-center text-center gap-3 transition-all duration-300 hover:border-[#f3af22]/40 hover:shadow-[0_4px_20px_rgba(243,175,34,0.05)]">
                <div class="text-[#f3af22]">
                    <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor" class="bi bi-award-fill" viewBox="0 0 16 16">
                        <path d="m8 0 1.669.864 1.858.282.842 1.68 1.337 1.32L13.4 6l.306 1.854-1.337 1.32-.842 1.68-1.858.282L8 12l-1.669-.864-1.858-.282-.842-1.68-1.337-1.32L2.6 6l-.306-1.854 1.337-1.32.842-1.68 1.858-.282z"/>
                        <path d="M4 11.794V16l4-1 4 1v-4.206l-2.018.306L8 13.126 5.992 12.1z"/>
                    </svg>
                </div>
                <h3 class="text-sm font-bold text-[#f3af22] tracking-wide uppercase">Kemitraan Resmi Publisher</h3>
                <p class="text-xs text-zinc-400 leading-relaxed">Bekerja sama langsung dengan pihak korporasi publisher game dunia demi legalitas pasokan produk.</p>
            </div>

        </div>
    </div>

    <div class="copy border-t border-zinc-950 bg-black/40 py-6">
        <div class="wrap-copy max-w-[1200px] mx-auto px-6 flex flex-col sm:flex-row justify-between items-center gap-4 text-xs text-zinc-500">
            <div class="flex gap-4">
                <a href="#" target="_blank" class="hover:text-white transition">Instagram</a>
                <a href="#" target="_blank" class="hover:text-white transition">YouTube</a>
            </div>
            <p class="copyright">© 2026 Market.in. All Rights Reserved.</p>
        </div>
    </div>

    <script src="https://unpkg.com/swiper@8/swiper-bundle.min.js"></script>
    <script>
        // Inisialisasi Slider Promo Utama
        const swiper = new Swiper('.swiper', {
            loop: true,
            autoplay: {
                delay: 4000,
                disableOnInteraction: false,
            },
            pagination: {
                el: '.swiper-pagination',
                clickable: true,
            },
        });

        // Dropdown Toggle Navbar Account
        const profileTrigger = document.getElementById('profileTrigger');
        const myDropdown = document.getElementById('myDropdown');

        if(profileTrigger && myDropdown) {
            profileTrigger.addEventListener('click', function(e) {
                e.stopPropagation();
                myDropdown.classList.toggle('show');
            });

            document.addEventListener('click', function() {
                if (myDropdown.classList.contains('show')) {
                    myDropdown.classList.remove('show');
                }
            });
        }
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('gameSearchInput');
            const searchDropdown = document.getElementById('searchResultsDropdown');
            const dropdownItems = searchDropdown.querySelectorAll('a[data-search]');
            const noMatchMsg = document.getElementById('noSearchMatch');

            if (searchInput && searchDropdown) {
                searchInput.addEventListener('input', function() {
                    const query = this.value.toLowerCase().trim();
                    let matchCount = 0;

                    if (query === '') {
                        // Jika inputan kosong, sembunyikan kotak popup rekomendasi
                        searchDropdown.classList.remove('show');
                        return;
                    }

                    // Tampilkan kontainer box dropdown hasil pencarian
                    searchDropdown.classList.add('show');

                    dropdownItems.forEach(item => {
                        const keywords = item.getAttribute('data-search') || '';
                        
                        // Logika filter kata kunci pencarian
                        if (keywords.includes(query)) {
                            item.style.display = 'flex'; // Tampilkan baris list game
                            matchCount++;
                        } else {
                            item.style.display = 'none'; // Sembunyikan baris list game
                        }
                    });

                    // Tampilkan pesan error jika game tidak ada yang cocok
                    if (matchCount === 0) {
                        noMatchMsg.classList.remove('hidden');
                    } else {
                        noMatchMsg.classList.add('hidden');
                    }
                });

                // Tutup dropdown popup secara otomatis jika user mengklik sembarang area di luar search bar
                document.addEventListener('click', function(e) {
                    if (!searchInput.contains(e.target) && !searchDropdown.contains(e.target)) {
                        searchDropdown.classList.remove('show');
                    }
                });
            }
        });
    </script>

</body>
</html>