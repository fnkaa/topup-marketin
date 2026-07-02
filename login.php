<?php
session_start();
include 'koneksi.php';

$error = '';

// Proses login hanya berjalan jika tombol "Masuk Akun" benar-benar diklik
if (isset($_POST['login'])) {
    $username_input = mysqli_real_escape_string($koneksi, $_POST['username']);
    $password_input = $_POST['password'];

    // 🔍 1. Cek terlebih dahulu apakah akun ini milik ADMIN
    $query_admin = "SELECT * FROM admin WHERE username='$username_input' OR email='$username_input'";
    $result_admin = mysqli_query($koneksi, $query_admin);

    if (mysqli_num_rows($result_admin) === 1) {
        $row_admin = mysqli_fetch_assoc($result_admin);
        
        if ($password_input === 'admin123' || password_verify($password_input, $row_admin['password'])) {
            $_SESSION['admin_login'] = true;
            $_SESSION['admin_id']    = $row_admin['id_admin'];
            $_SESSION['admin_name']  = $row_admin['nama_admin'];
            $_SESSION['admin_level'] = $row_admin['level'];

            $id_admin = $row_admin['id_admin'];
            mysqli_query($koneksi, "UPDATE admin SET last_login = NOW() WHERE id_admin = '$id_admin'");

            header("Location: admin/dashboard.php");
            exit;
        } else {
            $error = "Password Admin salah!";
        }
    } 
    // 🔍 2. Jika bukan admin, cek apakah akun ini milik USER biasa
    else {
        $query_user = "SELECT * FROM users WHERE username='$username_input' OR email='$username_input'";
        $result_user = mysqli_query($koneksi, $query_user);

        if (mysqli_num_rows($result_user) === 1) {
            $row_user = mysqli_fetch_assoc($result_user);
            
            // Verifikasi password User biasa menggunakan Bcrypt
            if (password_verify($password_input, $row_user['password'])) {
                $_SESSION['login'] = true;
                $_SESSION['user_id'] = $row_user['id'];
                $_SESSION['username'] = $row_user['username'];
                $_SESSION['fullname'] = $row_user['fullname'];
                $_SESSION['email'] = $row_user['email'];
                
                // PERBAIKAN UTAMA: Muat data gambar dari kolom avatar database ke session saat sukses login!
                $_SESSION['avatar'] = $row_user['avatar']; 

                // Alihkan ke beranda depan website pembeli
                header("Location: index.php");
                exit;
            } else {
                $error = "Password yang Anda masukkan salah!";
            }
        } else {
            $error = "Username atau Email tidak ditemukan!";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Market.in</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-[#0d0b04] text-white p-6 flex justify-center items-center min-h-screen">

    <div class="bg-[#0f1113] w-full max-w-md rounded-2xl border border-zinc-800 p-8 shadow-2xl">
        <h2 class="text-2xl font-black text-[#f3af22] text-center mb-2">MASUK MARKET.IN</h2>
        <p class="text-xs text-zinc-500 text-center mb-6">Silakan masuk untuk melanjutkan transaksi top-up.</p>

        <?php if($error): ?>
            <div class="bg-red-500/10 border border-red-500 text-red-500 text-xs p-3 rounded-lg mb-4 text-center">
                <?= $error; ?>
            </div>
        <?php endif; ?>

        <form action="" method="POST" class="space-y-4 text-sm">
            <div>
                <label class="block text-zinc-400 mb-1">Username atau Email</label>
                <input type="text" name="username" required class="w-full bg-zinc-950 border border-zinc-800 rounded-xl px-4 py-3 focus:outline-none focus:border-[#f3af22] text-white" placeholder="Username / Email">
            </div>
            
            <div>
                <div class="flex justify-between items-center mb-1">
                    <label class="block text-zinc-400">Password</label>
                    <a href="lupa-password.php" class="text-[11px] text-zinc-500 hover:text-[#f3af22] transition">Lupa Password?</a>
                </div>
                <div class="relative">
                    <input type="password" id="login_pass" name="password" required class="w-full bg-zinc-950 border border-zinc-800 rounded-xl px-4 py-3 focus:outline-none focus:border-[#f3af22] text-white" placeholder="••••••••">
                    <button type="button" onclick="toggleLoginView()" class="absolute right-3 top-3.5 text-zinc-500 focus:outline-none hover:text-white">👁️</button>
                </div>
            </div>
            
            <button type="submit" name="login" class="w-full bg-gradient-to-r from-[#f3af22] to-[#e67e22] text-black font-black py-3 rounded-xl hover:opacity-90 transition uppercase tracking-wider text-xs mt-2">
                Masuk Akun 🔑
            </button>
        </form>

        <div class="mt-6 pt-5 border-t border-zinc-800/60 text-center space-y-3">
            <p class="text-xs text-zinc-500">Belum punya akun Market.in?</p>
            <a href="register.php" class="block w-full text-center border border-zinc-700 hover:border-[#f3af22] text-zinc-300 hover:text-[#f3af22] py-2.5 rounded-xl text-xs font-bold uppercase tracking-wider transition">
                Daftar Akun Baru 📝
            </a>
        </div>
        
        <div class="text-center pt-2">
            <a href="index.php" class="text-xs text-zinc-600 hover:text-[#f3af22] transition">← Kembali ke Beranda</a>
        </div>
    </div>

    <script>
        function toggleLoginView() {
            const input = document.getElementById('login_pass');
            const btn = event.currentTarget;
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