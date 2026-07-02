<?php
session_start();
include 'koneksi.php';

$is_logged_in = isset($_SESSION['login']);
$username = $is_logged_in ? $_SESSION['username'] : '';
$email = $is_logged_in ? (isset($_SESSION['email']) ? $_SESSION['email'] : $username . '@gmail.com') : '';

// 🟢 KODE BARU: Ambil data avatar dari session jika user sudah login
$avatar_data = $is_logged_in && isset($_SESSION['avatar']) ? $_SESSION['avatar'] : '';

// Mengambil seluruh daftar game yang terdaftar di database
$query_all_games = "SELECT * FROM game ORDER BY nama_game ASC";
$result_all_games = mysqli_query($koneksi, $query_all_games);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Game - Market.in</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://unpkg.com/swiper@8/swiper-bundle.min.css"/>
    <style>
        body {
            background-color: #0d0d0d;
            color: #ffffff;
            background-image: linear-gradient(to bottom, rgba(13, 13, 13, 0.85), rgba(13, 13, 13, 0.95)), url('https://images.unsplash.com/photo-1542751371-adc38448a05e?q=80&w=1000');
            background-size: cover;
            background-attachment: fixed;
            background-position: center;
        }
        .text-gold { color: #f3af22; }
        .border-gold { border-color: #f3af22; }
        .bg-card { background-color: rgba(20, 20, 20, 0.75); backdrop-filter: blur(8px); }
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
    </style>
</head>
<body class="bg-[#0a0a0a] text-white pt-24">

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
                        <a href="daftar-game.php" class="flex items-center gap-2 text-sm font-semibold text-[#f3af22]">
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
                            <!-- 2. 🟢 PERBAIKAN TRIGGER AVATAR DI NAVBAR -->
                            <div class="flex items-center gap-3 bg-zinc-900 border border-zinc-800 px-3 py-1.5 rounded-xl border-gold" id="profileTrigger">
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

                            <!-- 3. 🟢 PERBAIKAN GAMBAR DI DALAM DROPDOWN MENU -->
                            <div class="dropdown-menu-box" id="myDropdown">
                                <div class="p-2 border-b border-zinc-800 mb-2 flex items-center gap-2">
                                    <div class="text-[#f3af22] flex items-center justify-center">
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

    <main class="max-w-[1200px] mx-auto px-6 mt-10">
        <div class="mb-8">
            <h2 class="text-2xl font-black text-[#f3af22]">🎮 SEMUA GAME</h2>
            <p class="text-xs text-zinc-400 mt-1">Pilih game favoritmu dan lakukan top-up instan dalam hitungan detik.</p>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-6">
            <?php if(mysqli_num_rows($result_all_games) > 0): ?>
                <?php while($game = mysqli_fetch_assoc($result_all_games)): ?>
                    <a href="<?= htmlspecialchars($game['slug_game']); ?>" class="bg-zinc-900 border border-zinc-800 rounded-xl overflow-hidden hover:border-[#f3af22] transition group">
                        <div class="h-36 bg-cover bg-center" style="background-image: url('<?= htmlspecialchars($game['bg_initial']); ?>')"></div>
                        <div class="p-4">
                            <h3 class="font-bold text-sm group-hover:text-[#f3af22] transition"><?= htmlspecialchars($game['nama_game']); ?></h3>
                            <span class="text-[10px] bg-zinc-800 text-zinc-400 px-2 py-0.5 rounded mt-2 inline-block"><?= htmlspecialchars($game['icon_text']); ?></span>
                        </div>
                    </a>
                <?php endwhile; ?>
            <?php else: ?>
                <p class="text-zinc-500 text-sm col-span-full text-center py-12">Belum ada data game di database.</p>
            <?php endif; ?>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-16 pt-12 border-t border-zinc-900/50">
            <div class="bg-card p-6 rounded-xl border border-zinc-800/80 text-center space-y-2">
                <div class="text-gold text-2xl">⚡</div>
                <h4 class="font-bold text-gold">Proses Instan</h4>
                <p class="text-xs text-zinc-400">Pesanan Anda akan diproses secara otomatis dalam hitungan detik setelah pembayaran terverifikasi oleh sistem kami.</p>
            </div>
            <div class="bg-card p-6 rounded-xl border border-zinc-800/80 text-center space-y-2">
                <div class="text-gold text-2xl">🛡️</div>
                <h4 class="font-bold text-gold">100% Aman & Legal</h4>
                <p class="text-xs text-zinc-400">Jaminan top up resmi menggunakan jalur API game langsung sehingga akun Anda tetap aman dari ban.</p>
            </div>
            <div class="bg-card p-6 rounded-xl border border-zinc-800/80 text-center space-y-2">
                <div class="text-gold text-2xl">🎧</div>
                <h4 class="font-bold text-gold">Layanan CS 24/7</h4>
                <p class="text-xs text-zinc-400">Tim customer service kami siap membantu kendala transaksi Anda kapan saja, siang maupun malam.</p>
            </div>
        </div>

    </main>

    <footer class="text-center py-8 text-zinc-600 text-xs mt-12 border-t border-zinc-900/50">
        &copy; 2026 Market.in. All rights reserved.
    </footer>

    <script src="https://unpkg.com/swiper@8/swiper-bundle.min.js"></script>
    <script>
        const profileTrigger = document.getElementById('profileTrigger');
        const myDropdown = document.getElementById('myDropdown');

        if(profileTrigger && myDropdown) {
            profileTrigger.addEventListener('click', function(e) {
                e.stopPropagation();
                // Menggunakan sistem class toggle "show" agar sesuai dengan style CSS bawaan Anda di atas
                myDropdown.classList.toggle('show');
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