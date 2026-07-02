<?php
session_start();
include 'koneksi.php'; // Mengaktifkan jembatan penghubung database MySQL

// Proteksi halaman: Jika user belum login, alihkan kembali ke halaman login atau index
if (!isset($_SESSION['login']) || !isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Jalankan query tarik data terbaru dari database users berdasarkan id session
$query_user = mysqli_query($koneksi, "SELECT * FROM users WHERE id='$user_id'");
if ($query_user && mysqli_num_rows($query_user) > 0) {
    $user = mysqli_fetch_assoc($query_user);
} else {
    session_destroy();
    header("Location: login.php");
    exit;
}

// Menentukan data string teks
$username = $user['username'];
$email = !empty($user['email']) ? $user['email'] : $username . '@gmail.com';
// UTAMAKAN MEMBACA FOTO AVATAR LANGSUNG DARI KOLOM DATABASE AGAR SINKRON PERMANEN
$avatar_data = !empty($user['avatar']) ? $user['avatar'] : '';

$success_message = "";
$error_message = "";

// Logika pemrosesan Form ketika user menekan tombol "Simpan Perubahan"
if (isset($_POST['update_account'])) {
    // Menggunakan fungsi pembersih SQL Injection demi keamanan data server
    $new_username = mysqli_real_escape_string($koneksi, htmlspecialchars($_POST['username']));
    $new_email = mysqli_real_escape_string($koneksi, htmlspecialchars($_POST['email']));
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    $new_avatar = $_POST['avatar_base64']; // Menangkap data gambar string base64 dari input hidden

    $is_password_valid = true;
    $query_password = "";

    // Validasi perubahan password jika kolom diisi
    if (!empty($new_password)) {
        if ($new_password === $confirm_password) {
            // Mengamankan password baru menggunakan enkripsi standar Bcrypt
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $query_password = ", password='$hashed_password'";
        } else {
            $error_message = "Konfirmasi password baru tidak cocok!";
            $is_password_valid = false;
        }
    }

    // Jika konfirmasi password aman, eksekusi pembaruan ke tabel MySQL
    if ($is_password_valid) {
        // PERBAIKAN UTAMA: Perbarui kolom avatar langsung ke baris database user
        if (!empty($new_avatar)) {
            $update_query = "UPDATE users SET username='$new_username', email='$new_email', avatar='$new_avatar' $query_password WHERE id='$user_id'";
        } else {
            $update_query = "UPDATE users SET username='$new_username', email='$new_email' $query_password WHERE id='$user_id'";
        }
        
        if (mysqli_query($koneksi, $update_query)) {
            // Sinkronisasikan juga isi data session global
            $_SESSION['username'] = $new_username;
            $_SESSION['email'] = $new_email;
            
            if (!empty($new_avatar)) {
                $_SESSION['avatar'] = $new_avatar;
                $avatar_data = $new_avatar;
            }
            
            // Segarkan variabel lokal tampilan
            $username = $new_username;
            $email = $new_email;
            
            $success_message = "Data akun berhasil diperbarui secara permanen ke database!";
        } else {
            $error_message = "Gagal menyimpan perubahan ke database: " . mysqli_error($koneksi);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Account - Market.in</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { background-color: #0a0a0a; color: #ffffff; }
        .text-gold { color: #f3af22; }
        .border-gold { border-color: #f3af22; }
        .bg-gold { background-color: #f3af22; }
        .bg-card { background-color: rgba(24, 24, 27, 0.75); backdrop-filter: blur(8px); }
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
<body class="bg-[#0a0a0a] text-white">

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
                        <a href="index.php" class="flex items-center gap-2 text-sm font-semibold text-zinc-400 hover:text-gold transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-house-door-fill" viewBox="0 0 16 16">
                                <path d="M6.5 14.5v-3.505c0-.245.25-.495.5-.495h2c.25 0 .5.25.5.5v3.5a.5.5 0 0 0 .5.5h4a.5.5 0 0 0 .5-.5v-7a.5.5 0 0 0-.146-.354L13 5.793V2.5a.5.5 0 0 0-.5-.5h-1a.5.5 0 0 0-.5.5v1.293L8.354 1.146a.5.5 0 0 0-.708 0l-6 6A.5.5 0 0 0 1.5 7.5v7a.5.5 0 0 0 .5.5h4a.5.5 0 0 0 .5-.5z"/>
                            </svg>
                            <span>Home</span>
                        </a>
                    </li>
                    <li>
                        <a href="daftar-game.php" class="flex items-center gap-2 text-sm font-semibold text-zinc-400 hover:text-gold transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-gamepad" viewBox="0 0 16 16">
                                <path d="M2 9a2 2 0 1 0 0 4 2 2 0 0 0 0-4zm12 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4z"/>
                                <path d="M6.257 6.434c-.143-.304-.436-.504-.757-.504H3.23c-.33 0-.618.21-.748.513L1.31 9.17A1 1 0 0 0 2.221 10.5h1.761c.424 0 .79-.272.923-.678L5.4 8.25h1.2c.424 0 .79-.272.923-.678l.493-1.488a.5.5 0 0 0-.482-.66H6.257zm6.286 0c-.33 0-.623.204-.757.504l-.494 1.488a1.002 1.002 0 0 0 .923.678h1.2l.493 1.482c.134.406.499.678.923.678h1.762a1 1 0 0 0 .91-1.33l-1.171-2.737a.807.807 0 0 0-.748-.513h-2.268z"/>
                                <path d="M8 2a5.978 5.978 0 0 0-4.757 2.336A4.953 4.953 0 0 1 8 6a4.953 4.953 0 0 1 4.757-1.664A5.978 5.978 0 0 0 8 2z"/>
                            </svg>
                            <span>Daftar Game</span>
                        </a>
                    </li>

                    <li class="profile-dropdown-wrapper">
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
                            <a href="edit-account.php" class="dropdown-item bg-zinc-800 text-white">⚙️ Edit Account</a>
                            <a href="history.php" class="dropdown-item">📜 History Transaksi</a>
                            <a href="bantuan.php" class="dropdown-item">❓ Bantuan</a>
                            <div class="border-t border-zinc-800 my-1"></div>
                            <a href="logout.php" class="dropdown-item text-red-400 hover:text-red-300">🚪 Log Out</a>
                        </div>
                    </li>
                </ul>
            </nav>
        </div>
    </header>

    <main class="max-w-5xl mx-auto px-4 py-8 pt-28 min-h-[90vh] flex flex-col justify-center">
        
        <?php if(!empty($success_message)): ?>
            <div class="bg-emerald-950/80 border border-emerald-500 text-emerald-200 px-4 py-3 rounded-xl mb-6 text-sm text-center backdrop-blur-sm">
                🎉 <?php echo $success_message; ?>
            </div>
        <?php endif; ?>

        <?php if(!empty($error_message)): ?>
            <div class="bg-red-950/80 border border-red-500 text-red-200 px-4 py-3 rounded-xl mb-6 text-sm text-center backdrop-blur-sm">
                ⚠️ <?php echo $error_message; ?>
            </div>
        <?php endif; ?>

        <div class="bg-card border border-zinc-800 rounded-2xl overflow-hidden grid grid-cols-1 md:grid-cols-5 shadow-2xl">
            
            <div class="md:col-span-2 bg-zinc-900/40 p-8 flex flex-col items-center justify-between border-b md:border-b-0 md:border-r border-zinc-800/80 text-center">
                <div class="space-y-4 w-full flex flex-col items-center">
                    <div class="text-xs font-bold text-zinc-500 uppercase tracking-widest">Foto Avatar</div>
                    
                    <div onclick="triggerUpload()" id="avatar-preview" class="w-32 h-32 rounded-full border-2 border-dashed border-zinc-700 hover:border-gold cursor-pointer flex flex-col items-center justify-center bg-zinc-950 bg-cover bg-center transition relative group" style="<?php echo !empty($avatar_data) ? "background-image: url('$avatar_data');" : ''; ?>">
                        <div class="text-2xl group-hover:scale-110 transition" style="<?php echo !empty($avatar_data) ? 'display: none;' : ''; ?>">📷</div>
                        <span class="text-[10px] text-zinc-500 mt-1 px-2" style="<?php echo !empty($avatar_data) ? 'display: none;' : ''; ?>">Ganti Avatar</span>
                        <input type="file" id="file-input" class="hidden" accept="image/*" onchange="previewImage(event)">
                    </div>

                    <div class="pt-2">
                        <h2 class="text-xl font-bold tracking-wide text-zinc-100"><?php echo htmlspecialchars($username); ?></h2>
                        <span class="inline-block bg-gold/10 border border-gold/30 text-gold text-[10px] uppercase font-black px-2.5 py-0.5 rounded-full mt-1 tracking-wider">Member Verified</span>
                    </div>
                </div>

                <div class="w-full pt-8 space-y-3">
                    <button type="button" onclick="window.location.href='delete-account.php'" class="w-full bg-zinc-950 border border-zinc-900 hover:border-red-600 hover:text-red-400 text-zinc-500 text-xs py-3 rounded-xl font-semibold transition uppercase tracking-wider">
                        🚨 Hapus Akun Saya
                    </button>
                </div>
            </div>

            <div class="md:col-span-3 p-8 flex flex-col justify-between">
                <form action="" method="POST" class="space-y-4">
                    <input type="hidden" name="avatar_base64" id="avatar_base64">

                    <div class="flex items-center gap-2 mb-4 border-b border-zinc-800 pb-3">
                        <span class="text-gold font-bold text-lg">|</span>
                        <h3 class="font-bold text-lg uppercase tracking-wider text-zinc-100">Edit Akun</h3>
                    </div>

                    <div>
                        <label class="text-xs text-zinc-400 block mb-1.5 font-semibold uppercase tracking-wider">Username Baru</label>
                        <input type="text" name="username" value="<?php echo htmlspecialchars($username); ?>" class="w-full bg-zinc-950 border border-zinc-800 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-gold text-white transition placeholder-zinc-600" required>
                    </div>

                    <div>
                        <label class="text-xs text-zinc-400 block mb-1.5 font-semibold uppercase tracking-wider">Alamat Email Baru</label>
                        <input type="email" name="email" value="<?php echo htmlspecialchars($email); ?>" class="w-full bg-zinc-950 border border-zinc-800 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-gold text-white transition placeholder-zinc-600" required>
                    </div>

                    <div class="relative">
                        <label class="text-xs text-zinc-400 block mb-1.5 font-semibold uppercase tracking-wider">Password Baru (Opsional)</label>
                        <input type="password" id="new_password" name="new_password" class="w-full bg-zinc-950 border border-zinc-800 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-gold text-white transition placeholder-zinc-600" placeholder="Kosongkan jika tidak ingin diubah">
                        <span onclick="togglePasswordVisibility('new_password', this)" class="absolute right-4 bottom-3 cursor-pointer text-zinc-500 select-none">👁️</span>
                    </div>

                    <div class="relative">
                        <label class="text-xs text-zinc-400 block mb-1.5 font-semibold uppercase tracking-wider">Konfirmasi Password Baru</label>
                        <input type="password" id="confirm_password" name="confirm_password" class="w-full bg-zinc-950 border border-zinc-800 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-gold text-white transition placeholder-zinc-600" placeholder="Masukkan kembali password baru">
                        <span onclick="togglePasswordVisibility('confirm_password', this)" class="absolute right-4 bottom-3 cursor-pointer text-zinc-500 select-none">👁️</span>
                    </div>

                    <div class="pt-4">
                        <button type="submit" name="update_account" class="w-full bg-gradient-to-r from-[#f3af22] to-[#e67e22] hover:from-[#fbc531] hover:to-[#f39c12] text-black font-black py-3.5 rounded-xl uppercase tracking-wider text-xs transition shadow-lg shadow-gold/10 hover:shadow-gold/20">
                            💾 Simpan Perubahan
                        </button>
                    </div>
                </form>

                <div class="pt-6 text-right hidden md:block select-none opacity-40">
                    <span class="text-xl font-black tracking-widest text-zinc-700">MARKET.<span class="text-zinc-600">IN</span></span>
                </div>
            </div>

        </div>
    </main>

    <footer class="text-center py-8 text-zinc-600 text-xs border-t border-zinc-900/50">
        &copy; 2026 Market.in. All rights reserved.
    </footer>

    <script>
        function triggerUpload() {
            document.getElementById('file-input').click();
        }

        function previewImage(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const avatar = document.getElementById('avatar-preview');
                    const base64Data = e.target.result;

                    avatar.style.backgroundImage = `url('${base64Data}')`;
                    avatar.querySelector('div').style.display = 'none';
                    avatar.querySelector('span').style.display = 'none';

                    document.getElementById('avatar_base64').value = base64Data;
                }
                reader.readAsDataURL(file);
            }
        }

        function togglePasswordVisibility(idInput, element) {
            const inputField = document.getElementById(idInput);
            if (inputField.type === "password") {
                inputField.type = "text";
                element.textContent = "🙈";
            } else {
                inputField.type = "password";
                element.textContent = "👁️";
            }
        }

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
</body>
</html>