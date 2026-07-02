<?php
session_start();
include 'koneksi.php';

// 1. SISTEM PENGAMAN: Cek session 'login' dan 'user_id' sesuai dengan yang dibuat di login.php
if (!isset($_SESSION['login']) || !isset($_SESSION['user_id'])) {
    // Jika tidak ada session, alihkan langsung ke login secara aman
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// 2. Jalankan query ke tabel `users` (sesuai nama tabel di login.php)
$query = mysqli_query($koneksi, "SELECT * FROM users WHERE id='$user_id'");

// 3. Validasi hasil query sebelum mengambil data
if ($query && mysqli_num_rows($query) > 0) {
    $user = mysqli_fetch_assoc($query);
} else {
    // Jika data tidak ditemukan di DB, hancurkan session dan minta login ulang
    session_destroy();
    header("Location: login.php");
    exit;
}

// Set variabel navigasi header agar seragam dengan index.php
$is_logged_in = true;
$username = isset($_SESSION['username']) ? $_SESSION['username'] : $user['username'];
$email = isset($user['email']) ? $user['email'] : $username . '@gmail.com';

// KODE BARU: Ambil data avatar dari session jika tersedia
$avatar_data = isset($_SESSION['avatar']) ? $_SESSION['avatar'] : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Saya - Market.in</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://unpkg.com/swiper@8/swiper-bundle.min.css"/>
    <style>
        body {
            background-color: #0a0a0a;
            color: #ffffff;
        }
        /* Mengatur toggle show/hide untuk dropdown */
        .dropdown-menu-box {
            display: none;
        }
        .dropdown-menu-box.show {
            display: flex;
        }
    </style>
</head>
<body class="bg-[#0a0a0a] text-white pt-24 flex flex-col min-h-screen">

    <header class="bg-[#0a0a0a]/90 border-b border-zinc-800 backdrop-blur-md px-6 py-4 fixed top-0 left-0 w-full z-50">
        <div class="max-w-[1200px] mx-auto flex flex-wrap justify-between items-center gap-4">
            
            <div class="flex items-center gap-6">
                <a href="index.php">
                    <span class="text-xl font-bold tracking-wider text-[#f3af22]">MARKET.<span class="text-white">IN</span></span>
                </a>
            </div>
            
            <nav class="flex items-center">
                <ul class="flex items-center gap-6 m-0 p-0 list-none">
                    <li>
                        <a href="index.php" class="flex items-center gap-2 text-sm font-semibold text-zinc-400 hover:text-[#f3af22] transition-colors">
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

                    <li class="profile-dropdown-wrapper relative">
                        <div class="flex items-center gap-3 bg-zinc-900 border border-zinc-800 px-3 py-1.5 rounded-xl hover:border-zinc-700 transition cursor-pointer select-none" id="profileTrigger">
                            <div class="text-zinc-400 flex items-center justify-center">
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

                        <div class="dropdown-menu-box absolute right-0 mt-2 w-56 bg-zinc-900 border border-zinc-800 rounded-xl shadow-xl p-2 flex-col z-50" id="myDropdown">
                            <div class="p-2 border-b border-zinc-800 mb-1 flex items-center gap-2">
                                <div class="text-[#f3af22] text-sm flex items-center justify-center">
                                    <?php if (!empty($avatar_data)): ?>
                                        <img src="<?php echo $avatar_data; ?>" class="w-6 h-6 rounded-full object-cover">
                                    <?php else: ?>
                                        👤
                                    <?php endif; ?>
                                </div>
                                <div class="overflow-hidden">
                                    <h4 class="text-xs font-bold text-white truncate"><?php echo htmlspecialchars($username); ?></h4>
                                    <p class="text-[10px] text-zinc-500 truncate"><?php echo htmlspecialchars($email); ?></p>
                                </div>
                            </div>
                            <a href="profile.php" class="flex items-center gap-2 text-xs text-zinc-300 hover:text-white hover:bg-zinc-800 px-2 py-2 rounded-lg transition-colors">👤 Profile</a>
                            <a href="edit-account.php" class="flex items-center gap-2 text-xs text-zinc-300 hover:text-white hover:bg-zinc-800 px-2 py-2 rounded-lg transition-colors">⚙️ Edit Account</a>
                            <a href="history.php" class="flex items-center gap-2 text-xs text-zinc-300 hover:text-white hover:bg-zinc-800 px-2 py-2 rounded-lg transition-colors">📜 History Transaksi</a>
                            <a href="bantuan.php" class="flex items-center gap-2 text-xs text-zinc-300 hover:text-white hover:bg-zinc-800 px-2 py-2 rounded-lg transition-colors">❓ Bantuan</a>
                            <div class="border-t border-zinc-800 my-1"></div>
                            <a href="logout.php" class="flex items-center gap-2 text-xs text-red-400 hover:text-red-300 hover:bg-red-500/10 px-2 py-2 rounded-lg transition-colors">🚪 Log Out</a>
                        </div>
                    </li>
                </ul>
            </nav>
        </div>
    </header>

    <main class="max-w-[500px] mx-auto px-6 mt-16 pb-16 flex-grow flex items-center justify-center w-full">
        <div class="bg-zinc-900 border border-zinc-800 rounded-2xl p-6 sm:p-8 shadow-xl text-center w-full">
            
            <div class="flex flex-col items-center mb-6">
                <?php if (!empty($avatar_data)): ?>
                    <div class="w-24 h-24 rounded-full mb-3 border-2 border-[#f3af22] p-0.5 shadow-lg shadow-[#f3af22]/10 overflow-hidden">
                        <img src="<?php echo $avatar_data; ?>" class="w-full h-full rounded-full object-cover">
                    </div>
                <?php else: ?>
                    <div class="text-[#f3af22] bg-[#f3af22]/10 p-4 rounded-full mb-3 border border-[#f3af22]/20">
                        <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" fill="currentColor" class="bi bi-person-badge-fill" viewBox="0 0 16 16">
                            <path d="M2 2a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2zm4.5 0a.5.5 0 0 0 0 1h3a.5.5 0 0 0 0-1zM8 11a3 3 0 1 0 0-6 3 3 0 0 0 0 6m5 2.755C12.146 12.825 10.623 12 8 12s-4.146.826-5 1.755V14a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1z"/>
                        </svg>
                    </div>
                <?php endif; ?>
                
                <h2 class="text-xl font-bold text-white tracking-wide"><?= htmlspecialchars($user['fullname']); ?></h2>
                <span class="text-xs bg-zinc-800 text-zinc-400 px-3 py-1 rounded-full mt-1.5 border border-zinc-700/50">✨ Akun Member Resmi</span>
            </div>

            <div class="border-t border-zinc-800/80 my-4 pt-4 text-left space-y-4">
                <div class="flex justify-between items-center border-b border-zinc-800/40 pb-2.5">
                    <span class="text-xs text-zinc-500 font-medium">Username</span>
                    <span class="text-sm text-zinc-300 font-semibold">@<?= htmlspecialchars($user['username']); ?></span>
                </div>
                <div class="flex justify-between items-center border-b border-zinc-800/40 pb-2.5">
                    <span class="text-xs text-zinc-500 font-medium">Alamat Email</span>
                    <span class="text-sm text-zinc-300 font-semibold"><?= htmlspecialchars($user['email']); ?></span>
                </div>
                <div class="flex justify-between items-center pb-1">
                    <span class="text-xs text-zinc-500 font-medium">No. WhatsApp</span>
                    <span class="text-sm text-zinc-300 font-semibold"><?= htmlspecialchars($user['whatsapp']); ?></span>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3 pt-6 border-t border-zinc-800/80 mt-6">
                <a href="edit-account.php" class="bg-zinc-800 border border-zinc-700 text-white text-xs font-bold py-3 rounded-xl hover:bg-zinc-750 hover:border-zinc-600 transition flex items-center justify-center gap-1.5">
                    ⚙️ Edit Akun
                </a>
                <a href="history.php" class="bg-[#f3af22] text-black text-xs font-bold py-3 rounded-xl hover:bg-[#e2a21e] transition flex items-center justify-center gap-1.5">
                    📜 Transaksi
                </a>
            </div>

        </div>
    </main>

    <div class="copy border-t border-zinc-900 py-6 w-full">
        <div class="max-w-[1200px] mx-auto px-6 flex flex-col sm:flex-row justify-between items-center gap-4 text-xs text-zinc-500">
            <div class="flex gap-4">
                <a href="#" target="_blank" class="hover:text-white transition">Instagram</a>
                <a href="#" target="_blank" class="hover:text-white transition">YouTube</a>
            </div>
            <p>© 2026 Market.in. All Rights Reserved.</p>
        </div>
    </div>

    <script>
        const profileTrigger = document.getElementById('profileTrigger');
        const myDropdown = document.getElementById('myDropdown');

        if (profileTrigger && myDropdown) {
            profileTrigger.addEventListener('click', function(e) {
                e.stopPropagation();
                myDropdown.classList.toggle('show');
            });

            myDropdown.addEventListener('click', function(e) {
                e.stopPropagation();
            });

            document.addEventListener('click', function() {
                if (myDropdown.classList.contains('show')) {
                    myDropdown.classList.remove('show');
                }
            });
        }
    </script>
</body>
</html>