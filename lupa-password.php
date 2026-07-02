<?php
session_start();
include 'koneksi.php';

$error = '';
$success = '';
$step = 1; // Langkah 1: Cek Akun, Langkah 2: Input Password Baru
$user_id_reset = '';

// ==========================================
// PROSES LANGKAH 1: VERIFIKASI EMAIL & WHATSAPP
// ==========================================
if (isset($_POST['cek_akun'])) {
    $email    = mysqli_real_escape_string($koneksi, $_POST['email']);
    $whatsapp = mysqli_real_escape_string($koneksi, $_POST['whatsapp']);

    // Mencari apakah kombinasi email dan nomor whatsapp cocok di tabel users
    $query = mysqli_query($koneksi, "SELECT id FROM users WHERE email='$email' AND whatsapp='$whatsapp'");
    
    if (mysqli_num_rows($query) === 1) {
        $row = mysqli_fetch_assoc($query);
        $_SESSION['reset_user_id'] = $row['id']; // Simpan ID user temporer di session
        $step = 2; // Pindah ke langkah input password baru
    } else {
        $error = "Kombinasi Email dan No. WhatsApp tidak cocok atau tidak terdaftar!";
    }
}

// ==========================================
// PROSES LANGKAH 2: SIMPAN PASSWORD BARU
// ==========================================
if (isset($_POST['submit_password_baru'])) {
    // Pastikan session ID reset masih ada
    if (isset($_SESSION['reset_user_id'])) {
        $id_user       = $_SESSION['reset_user_id'];
        $password_baru = $_POST['password_baru'];
        $konfirmasi    = $_POST['konfirmasi_password'];

        if ($password_baru === $konfirmasi) {
            // Enkripsi password baru dengan Bcrypt standard
            $password_hashed = password_hash($password_baru, PASSWORD_DEFAULT);

            // Update ke tabel users kolom password berdasarkan id
            $update = mysqli_query($koneksi, "UPDATE users SET password='$password_hashed' WHERE id='$id_user'");
            
            if ($update) {
                $success = "Password berhasil diperbarui! Silakan kembali ke halaman login.";
                unset($_SESSION['reset_user_id']); // Hapus session temporer
                $step = 3; // Selesai
            } else {
                $error = "Gagal memperbarui database: " . mysqli_error($koneksi);
                $step = 2;
            }
        } else {
            $error = "Konfirmasi password baru tidak cocok!";
            $step = 2;
        }
    } else {
        $error = "Sesi reset habis. Silakan ulangi langkah awal.";
        $step = 1;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lupa Password - Market.in</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-[#0d0b04] text-white p-6 flex justify-center items-center min-h-screen">

    <div class="bg-[#0f1113] w-full max-w-md rounded-2xl border border-zinc-800 p-8 shadow-2xl">
        <h2 class="text-xl font-black text-[#f3af22] text-center mb-1">PULIHKAN AKUN</h2>
        <p class="text-xs text-zinc-500 text-center mb-6">Sistem pemulihan kata sandi otomatis Market.in</p>

        <?php if($error): ?>
            <div class="bg-red-500/10 border border-red-500 text-red-500 text-xs p-3 rounded-lg mb-4 text-center">
                <?= $error; ?>
            </div>
        <?php endif; ?>

        <?php if($success): ?>
            <div class="bg-green-500/10 border border-green-500 text-green-400 text-xs p-3 rounded-lg mb-4 text-center">
                <?= $success; ?>
            </div>
        <?php endif; ?>

        <?php if ($step === 1): ?>
            <form action="" method="POST" class="space-y-4 text-sm">
                <div>
                    <label class="block text-zinc-400 mb-1">Email Akun Anda</label>
                    <input type="email" name="email" required class="w-full bg-zinc-950 border border-zinc-800 rounded-xl px-4 py-3 focus:outline-none focus:border-[#f3af22] text-white" placeholder="Contoh: user@gmail.com">
                </div>
                <div>
                    <label class="block text-zinc-400 mb-1">No. WhatsApp Terdaftar</label>
                    <input type="text" name="whatsapp" required class="w-full bg-zinc-950 border border-zinc-800 rounded-xl px-4 py-3 focus:outline-none focus:border-[#f3af22] text-white" placeholder="Contoh: 08123456789">
                    <p class="text-[10px] text-zinc-600 mt-1">*Data harus sama persis saat Anda melakukan registrasi akun.</p>
                </div>
                <button type="submit" name="cek_akun" class="w-full bg-gradient-to-r from-[#f3af22] to-[#e67e22] text-black font-black py-3 rounded-xl hover:opacity-90 transition uppercase tracking-wider text-xs font-bold mt-2">
                    Verifikasi Akun 🔍
                </button>
            </form>

        <?php elseif ($step === 2): ?>
            <form action="" method="POST" class="space-y-4 text-sm">
                <div>
                    <label class="block text-zinc-400 mb-1">Password Baru</label>
                    <div class="relative">
                        <input type="password" id="pass_baru" name="password_baru" required class="w-full bg-zinc-950 border border-zinc-800 rounded-xl px-4 py-3 focus:outline-none focus:border-[#f3af22] text-white" placeholder="Minimal 6 karakter">
                        <button type="button" onclick="togglePassword('pass_baru', this)" class="absolute right-3 top-3.5 text-zinc-500 focus:outline-none hover:text-white">👁️</button>
                    </div>
                </div>
                <div>
                    <label class="block text-zinc-400 mb-1">Konfirmasi Password Baru</label>
                    <div class="relative">
                        <input type="password" id="pass_konfirmasi" name="konfirmasi_password" required class="w-full bg-zinc-950 border border-zinc-800 rounded-xl px-4 py-3 focus:outline-none focus:border-[#f3af22] text-white" placeholder="Ulangi password baru">
                        <button type="button" onclick="togglePassword('pass_konfirmasi', this)" class="absolute right-3 top-3.5 text-zinc-500 focus:outline-none hover:text-white">👁️</button>
                    </div>
                </div>
                <button type="submit" name="submit_password_baru" class="w-full bg-emerald-500 text-black font-black py-3 rounded-xl hover:opacity-90 transition uppercase tracking-wider text-xs font-bold mt-2">
                    Simpan Perubahan Password 🔒
                </button>
            </form>

        <?php else: ?>
            <div class="pt-2">
                <a href="login.php" class="block w-full text-center bg-[#f3af22] text-black font-black py-3 rounded-xl uppercase tracking-wider text-xs font-bold">
                    Ke Halaman Login 🔑
                </a>
            </div>
        <?php endif; ?>

        <div class="text-center mt-6 pt-4 border-t border-zinc-800/60">
            <a href="login.php" class="text-xs text-zinc-500 hover:text-[#f3af22] transition">← Kembali ke Login</a>
        </div>
    </div>

    <script>
        function togglePassword(inputId, btn) {
            const input = document.getElementById(inputId);
            if (input.type === "password") {
                input.type = "text";
                btn.textContent = "🙈";
            } else {
                input.type = "password";
                btn.textContent = "👁️";
            }
        }
    </script>
</body>
</html>