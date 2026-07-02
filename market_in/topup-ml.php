<?php
session_start();
include 'koneksi.php';

// Cek status login user
$is_logged_in = isset($_SESSION['login']);
$username = $is_logged_in ? $_SESSION['username'] : '';
$email = $is_logged_in ? (isset($_SESSION['email']) ? $_SESSION['email'] : $username . '@gmail.com') : '';

// Ambil data avatar dari session jika login
$avatar_data = $is_logged_in && isset($_SESSION['avatar']) ? $_SESSION['avatar'] : '';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Top Up Mobile Legends - Market.in</title>
    <script src="https://cdn.tailwindcss.com"></script>
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
        .btn-glow {
            background: linear-gradient(135deg, #f3af22 0%, #e67e22 100%);
            box-shadow: 0 0 20px rgba(243, 175, 34, 0.6), inset 0 0 10px rgba(255, 255, 255, 0.3);
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.3);
            transition: all 0.3s ease;
        }
        .btn-glow:hover {
            background: linear-gradient(135deg, #fbc531 0%, #f39c12 100%);
            box-shadow: 0 0 30px rgba(243, 175, 34, 0.9), inset 0 0 15px rgba(255, 255, 255, 0.5);
            transform: translateY(-2px);
        }
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
<body>

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
                            <span>Home</span>
                        </a>
                    </li>
                    <li>
                        <a href="daftar-game.php" class="flex items-center gap-2 text-sm font-semibold text-zinc-400 hover:text-[#f3af22] transition-colors">
                            <span>Daftar Game</span>
                        </a>
                    </li>

                    <?php if ($is_logged_in) : ?>
                    <li class="profile-dropdown-wrapper">
                        <div class="flex items-center gap-3 bg-zinc-900 border border-zinc-800 px-3 py-1.5 rounded-xl hover:border-zinc-700 transition" id="profileTrigger">
                            <div class="text-gold flex items-center justify-center">
                                <?php if (!empty($avatar_data)): ?>
                                    <img src="<?= $avatar_data; ?>" class="w-5 h-5 rounded-full object-cover">
                                <?php else: ?>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-person-circle" viewBox="0 0 16 16">
                                        <path d="M11 6a3 3 0 1 1-6 0 3 3 0 0 1 6 0z"/>
                                        <path fill-rule="evenodd" d="M0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8zm8-7a7 7 0 0 0-5.468 11.37C3.242 11.226 4.805 10 8 10s4.757 1.225 5.468 2.37A7 7 0 0 0 8 1z"/>
                                    </svg>
                                <?php endif; ?>
                            </div>
                            <div class="text-left text-xs hidden sm:block">
                                <span class="block font-medium text-white">Halo, <?= htmlspecialchars($username); ?></span>
                                <span class="block text-[10px] text-zinc-500">Akun Member</span>
                            </div>
                        </div>

                        <div class="dropdown-menu-box" id="myDropdown">
                            <div class="p-2 border-b border-zinc-800 mb-2 flex items-center gap-2">
                                <div class="text-[#f3af22] flex items-center justify-center">
                                    <?php if (!empty($avatar_data)): ?>
                                        <img src="<?= $avatar_data; ?>" class="w-6 h-6 rounded-full object-cover">
                                    <?php else: ?>
                                        👤
                                    <?php endif; ?>
                                </div>
                                <div>
                                    <h4 class="text-sm font-bold text-white"><?= htmlspecialchars($username); ?></h4>
                                    <p class="text-[10px] text-zinc-500"><?= htmlspecialchars($email); ?></p>
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
                                <span>Masuk</span>
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    </header>

    <main class="max-w-6xl mx-auto px-4 py-8 pt-28">
        
        <div class="bg-gradient-to-r from-zinc-900/90 to-black/90 backdrop-blur-md rounded-xl p-8 mb-8 flex flex-col md:flex-row justify-between items-center gap-6 border border-zinc-800">
            <div class="flex items-center gap-6">
                <div class="w-24 h-24 md:w-32 md:h-32 bg-zinc-800 rounded-xl overflow-hidden flex items-center justify-center font-bold text-zinc-600">
                    <img src="img/logo-ml.jpg" alt="Mobile Legends" class="w-24 h-24 md:w-32 md:h-32 rounded-xl object-cover">
                </div>
                <div>
                    <h1 class="text-2xl md:text-3xl font-bold tracking-wide">MOBILE LEGENDS</h1>
                    <p class="text-zinc-400 text-sm mt-1">Bang Bang • Moonton</p>
                </div>
            </div>
            <div class="text-right hidden md:block">
                <h2 class="text-3xl font-black text-gold tracking-wider">TOP UP</h2>
                <h2 class="text-2xl font-bold text-zinc-300">MOBILE LEGENDS</h2>
            </div>
        </div>

        <form action="pembayaran.php" method="POST" id="formTopup" class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- ID Game disesuaikan dengan database Mobile Legends Anda (yaitu 4) -->
            <input type="hidden" name="id_game" value="1">
            
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-card p-6 rounded-xl border border-zinc-800/80">
                    
                    <div class="flex items-center gap-2 mb-6">
                        <span class="text-zinc-400 font-semibold text-lg">2</span>
                        <h3 class="font-semibold text-xl tracking-wide text-zinc-100">Pilih nominal</h3>
                    </div>

                    <!-- 1. SEKSYEN WEEKLY PASS (DIAMBIL DARI DATABASE) -->
                    <div class="mb-8">
                        <h4 class="text-lg font-serif italic font-semibold text-zinc-200 mb-4 tracking-wide">Weekly Diamond Pass ML Murah</h4>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <?php
                            $id_game_ml = 1;
                            // Menarik item khusus yang mempunyai teks 'Weekly' dalam namanya
                            $query_weekly = mysqli_query($koneksi, "SELECT * FROM game_items WHERE id_game = $id_game_ml AND nama_item LIKE '%Weekly%' AND status = 'Tersedia' ORDER BY harga_jual ASC");

                            if ($query_weekly && mysqli_num_rows($query_weekly) > 0) {
                                while ($wp = mysqli_fetch_assoc($query_weekly)) {
                                    echo '
                                    <label class="cursor-pointer group relative">
                                        <input type="radio" name="nominal" value="'.$wp['nama_item'].'" data-price="'.(int)$wp['harga_jual'].'" class="hidden peer" required>
                                        <div class="bg-zinc-900/40 backdrop-blur-sm border border-zinc-800/60 p-5 rounded-xl text-left transition duration-200 peer-checked:border-gold peer-checked:bg-zinc-800/50 group-hover:border-zinc-600">
                                            <div class="text-sm font-medium text-zinc-300 group-hover:text-white transition">'.htmlspecialchars($wp['nama_item']).'</div>
                                            <div class="text-sm font-normal text-zinc-400 mt-2">Rp '.number_format($wp['harga_jual'], 0, ',', '.').'</div>
                                        </div>
                                    </label>';
                                }
                            } else {
                                echo '<div class="text-zinc-500 text-xs col-span-2 py-4">Belum ada varian Weekly Pass yang di-input / berstatus Tersedia di database.</div>';
                            }
                            ?>
                        </div>
                    </div>

                    <!-- 2. SEKSYEN DIAMOND BIASA (DIAMBIL DARI DATABASE) -->
                    <div>
                        <h4 class="text-lg font-serif italic font-semibold text-zinc-200 mb-4 tracking-wide">Top Up ML Murah</h4>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <?php
                            // Menarik item yang murni Diamond biasa (TIDAK mengandung kata 'Weekly')
                            $query_items = mysqli_query($koneksi, "SELECT * FROM game_items WHERE id_game = $id_game_ml AND nama_item NOT LIKE '%Weekly%' AND status = 'Tersedia' ORDER BY harga_jual ASC");

                            if ($query_items && mysqli_num_rows($query_items) > 0) {
                                while ($item = mysqli_fetch_assoc($query_items)) {
                                    echo '
                                    <label class="cursor-pointer group relative">
                                        <input type="radio" name="nominal" value="'.$item['nama_item'].'" data-price="'.(int)$item['harga_jual'].'" class="hidden peer" required>
                                        <div class="bg-zinc-900/40 backdrop-blur-sm border border-zinc-800/60 p-5 rounded-xl text-left transition duration-200 peer-checked:border-gold peer-checked:bg-zinc-800/50 group-hover:border-zinc-600">
                                            <div class="text-sm font-medium text-zinc-300 group-hover:text-white transition">'.htmlspecialchars($item['nama_item']).'</div>
                                            <div class="text-sm font-normal text-zinc-400 mt-2">Rp '.number_format($item['harga_jual'], 0, ',', '.').'</div>
                                        </div>
                                    </label>';
                                }
                            } else {
                                echo '<div class="text-zinc-500 text-xs col-span-2 py-4">Belum ada varian Diamond yang di-input / berstatus Tersedia di database.</div>';
                            }
                            ?>
                        </div>
                    </div>

                </div>
            </div>

            <div class="space-y-6">
                
                <div class="bg-card p-6 rounded-xl border border-zinc-800/80 space-y-4">
                    <div class="flex items-center gap-2">
                        <span class="text-gold font-bold">|</span>
                        <h3 class="font-semibold text-lg">Masukkan Data Akun</h3>
                    </div>
                    <div>
                        <label class="text-xs text-zinc-400 block mb-1">Masukkan User ID</label>
                        <input type="number" name="user_id" placeholder="Contoh: 12345678" class="w-full bg-[#1a1a1a]/80 border border-zinc-800 rounded px-3 py-2 text-sm focus:outline-none focus:border-gold" required>
                    </div>
                    <div>
                        <label class="text-xs text-zinc-400 block mb-1">Masukkan Zone ID</label>
                        <input type="number" name="zone_id" placeholder="Contoh: 1234" class="w-full bg-[#1a1a1a]/80 border border-zinc-800 rounded px-3 py-2 text-sm focus:outline-none focus:border-gold" required>
                    </div>
                </div>

                <div class="bg-card p-6 rounded-xl border border-zinc-800/80">
                    <div class="flex items-center gap-2 mb-4">
                        <span class="text-zinc-400 font-semibold text-lg">3</span>
                        <h3 class="font-semibold text-xl tracking-wide text-zinc-100">Pilih Pembayaran</h3>
                    </div>
                    
                    <div class="space-y-3">
                        <?php
                        $metode = [
                            ['id' => 'bni_va', 'nama' => 'BNI Virtual Account', 'logo' => 'img/logo-bni.png'],
                            ['id' => 'bca_va', 'nama' => 'BCA Virtual Account', 'logo' => 'img/logo-bca.jpg'],
                            ['id' => 'qris', 'nama' => 'QRIS', 'logo' => 'img/logo-qris.png']
                        ];

                        foreach ($metode as $mw) {
                            echo '
                            <label class="flex items-center justify-between p-4 bg-white/80 backdrop-blur-md border border-white/20 rounded-2xl cursor-pointer transition hover:bg-white/90 relative group">
                                <input type="radio" name="payment" value="'.$mw['id'].'" class="absolute right-4 top-1/2 -translate-y-1/2 accent-zinc-900 w-4 h-4" required>
                                <div class="flex items-center gap-4">
                                    <div class="w-16 h-8 flex items-center justify-center bg-transparent rounded p-1 object-contain">
                                        <img src="'.$mw['logo'].'" alt="'.$mw['nama'].'" class="max-h-full max-w-full">
                                    </div>
                                    <span class="text-sm font-medium italic tracking-wide text-zinc-900">'.$mw['nama'].'</span>
                                </div>
                            </label>';
                        }
                        ?>
                    </div>
                </div>

                <div class="bg-card p-6 rounded-xl border border-zinc-800/80 space-y-3">
                    <div class="flex items-center gap-2">
                        <span class="text-zinc-400 font-semibold text-lg">4</span>
                        <h3 class="font-semibold text-xl tracking-wide text-zinc-100">Kode Promo</h3>
                    </div>
                    <div>
                        <input type="text" name="kode_promo" id="kode_promo" placeholder="Masukkan kode promo" class="w-full bg-transparent border border-zinc-600 rounded-xl px-4 py-2.5 text-sm placeholder-zinc-500 focus:outline-none focus:border-zinc-400">
                    </div>
                    <div id="promo_status" class="text-xs font-semibold mt-1 hidden"></div>
                    <div class="flex items-center gap-2 text-xs text-zinc-500 bg-zinc-900/30 p-2 rounded-lg border border-zinc-800/50">
                        <span>🎟️</span>
                        <span>Pakai kode promo <strong class="text-gold cursor-pointer" onclick="copypromo()">TOPUPM4RKETIN</strong></span>
                    </div>
                </div>

                <div class="bg-card p-6 rounded-xl border border-zinc-800/80 space-y-4">
                    <div class="flex items-center gap-2">
                        <span class="text-zinc-400 font-semibold text-lg">5</span>
                        <h3 class="font-semibold text-xl tracking-wide text-zinc-100">Detail Kontak</h3>
                    </div>
                    <div>
                        <label class="text-sm text-zinc-300 block mb-2 font-medium">Email</label>
                        <input type="email" name="email" value="<?php echo htmlspecialchars($email); ?>" placeholder="Masukkan email" class="w-full bg-transparent border border-zinc-600 rounded-xl px-4 py-2.5 text-sm placeholder-zinc-500 focus:outline-none focus:border-zinc-400" required>
                    </div>
                    <div class="flex items-start gap-2 text-xs text-zinc-500 bg-zinc-900/40 p-3 rounded-lg border border-zinc-800/50 leading-relaxed">
                        <span class="mt-0.5">⚠️</span>
                        <span>Bukti transaksi akan dikirimkan lewat email yang kamu isi di atas.</span>
                    </div>

                    <input type="hidden" name="total" id="total_harga" value="0">
                    <input type="hidden" name="order_id" value="<?php echo 'INV/'.date('Ymd').'/'.rand(10000, 99999); ?>">

                    <?php if ($is_logged_in) : ?>
                        <button type="submit" name="submit_topup" class="w-full mt-4 btn-glow text-black font-black py-3.5 rounded-xl uppercase tracking-wider text-sm">
                            Beli Sekarang (<span id="display_total">Rp 0</span>)
                        </button>
                    <?php else : ?>
                        <a href="login.php" class="w-full mt-4 block text-center bg-zinc-800 border border-zinc-700 text-zinc-400 font-bold py-3.5 rounded-xl uppercase tracking-wider text-sm hover:bg-zinc-700 hover:text-white transition">
                            🔒 Login Terlebih Dahulu Untuk Membeli
                        </a>
                    <?php endif; ?>
                </div>

            </div>
        </form>

    </main>

    <footer class="text-center py-8 text-zinc-600 text-xs mt-12 border-t border-zinc-900/50">
        &copy; 2026 Market.in. All rights reserved.
    </footer>

    <script>
        function copypromo() {
            document.getElementById('kode_promo').value = 'TOPUPM4RKETIN';
            document.getElementById('kode_promo').dispatchEvent(new Event('input'));
        }

        document.addEventListener('DOMContentLoaded', function () {
            const nominalRadios = document.querySelectorAll('input[name="nominal"]');
            const totalHargaInput = document.getElementById('total_harga');
            const displayTotal = document.getElementById('display_total');
            const kodePromoInput = document.getElementById('kode_promo');
            const promoStatus = document.getElementById('promo_status');

            let basePrice = 0;

            function hitungTotalAkhir() {
                let currentTotal = basePrice;
                let kode = kodePromoInput.value.trim().toUpperCase();

                if (kode === "TOPUPM4RKETIN") {
                    if (basePrice >= 50000) {
                        currentTotal = basePrice - 10000;
                        promoStatus.innerText = "🎉 Kode promo berhasil digunakan! Diskon Rp 10.000";
                        promoStatus.className = "text-xs font-semibold mt-1 text-green-400 block";
                    } else {
                        promoStatus.innerText = "❌ Minimal pembelian Rp 50.000 untuk kode ini.";
                        promoStatus.className = "text-xs font-semibold mt-1 text-yellow-500 block";
                    }
                } else if (kode !== "") {
                    promoStatus.innerText = "❌ Kode promo tidak valid.";
                    promoStatus.className = "text-xs font-semibold mt-1 text-red-500 block";
                } else {
                    promoStatus.className = "hidden";
                }

                totalHargaInput.value = currentTotal;
                if(displayTotal) {
                    displayTotal.innerText = "Rp " + currentTotal.toLocaleString('id-ID');
                }
            }

            nominalRadios.forEach(radio => {
                radio.addEventListener('change', function () {
                    if (this.checked) {
                        basePrice = parseInt(this.getAttribute('data-price')) || 0;
                        hitungTotalAkhir();
                    }
                });
            });

            if(kodePromoInput) {
                kodePromoInput.addEventListener('input', function() {
                    if (basePrice > 0) {
                        hitungTotalAkhir();
                    }
                });
            }

            // Script Dropdown Profil Navbar
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
        });
    </script>

</body>
</html>