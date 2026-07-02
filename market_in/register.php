<?php
include 'koneksi.php';

$error = '';
$success = '';

if (isset($_POST['register'])) {
    // Sesuaikan dengan nama kolom di tabel `users` Anda
    $fullname = mysqli_real_escape_string($koneksi, $_POST['fullname']);
    $username = mysqli_real_escape_string($koneksi, $_POST['username']);
    $email    = mysqli_real_escape_string($koneksi, $_POST['email']);
    $whatsapp = mysqli_real_escape_string($koneksi, $_POST['whatsapp']);
    $password = $_POST['password']; 

    // Enkripsi password dengan Bcrypt agar aman
    $password_hashed = password_hash($password, PASSWORD_DEFAULT);

    // Cek duplikasi username atau email pada tabel `users`
    $cek_user = mysqli_query($koneksi, "SELECT * FROM users WHERE username='$username' OR email='$email'");
    if (mysqli_num_rows($cek_user) > 0) {
        $error = "Username atau Email sudah digunakan!";
    } else {
        // Query disesuaikan ke tabel `users`
        $query = "INSERT INTO users (fullname, username, email, whatsapp, password) 
                  VALUES ('$fullname', '$username', '$email', '$whatsapp', '$password_hashed')";
        
        if (mysqli_query($koneksi, $query)) {
            $success = "Registrasi berhasil! Silakan login.";
            header("Location: login.php?pesan=berhasil");
            exit;
        } else {
            $error = "Registrasi gagal: " . mysqli_error($koneksi);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Register - Market.in</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-[#0d0b04] text-white flex min-h-screen items-center justify-center p-4">

    <div class="bg-[#0f1113] w-full max-w-md rounded-2xl border border-zinc-800 p-8 shadow-2xl">
        <h2 class="text-2xl font-black text-[#f3af22] text-center mb-2">DAFTAR MARKET.IN</h2>
        <p class="text-xs text-zinc-500 text-center mb-6">Lengkapi data di bawah untuk mendaftar akun.</p>

        <?php if($error): ?>
            <div class="bg-red-500/10 border border-red-500 text-red-500 text-xs p-3 rounded-lg mb-4"><?= $error; ?></div>
        <?php endif; ?>
        <?php if($success): ?>
            <div class="bg-green-500/10 border border-green-500 text-green-500 text-xs p-3 rounded-lg mb-4"><?= $success; ?></div>
        <?php endif; ?>

        <form action="" method="POST" class="space-y-4 text-sm">
            <div>
                <label class="block text-zinc-400 mb-1">Nama Lengkap</label>
                <input type="text" name="fullname" required class="w-full bg-zinc-950 border border-zinc-800 rounded-xl px-4 py-3 focus:outline-none focus:border-[#f3af22] text-white">
            </div>
            <div>
                <label class="block text-zinc-400 mb-1">Username</label>
                <input type="text" name="username" required class="w-full bg-zinc-950 border border-zinc-800 rounded-xl px-4 py-3 focus:outline-none focus:border-[#f3af22] text-white">
            </div>
            <div>
                <label class="block text-zinc-400 mb-1">Email</label>
                <input type="email" name="email" required class="w-full bg-zinc-950 border border-zinc-800 rounded-xl px-4 py-3 focus:outline-none focus:border-[#f3af22] text-white">
            </div>
            <div>
                <label class="block text-zinc-400 mb-1">No. WhatsApp</label>
                <input type="text" name="whatsapp" required class="w-full bg-zinc-950 border border-zinc-800 rounded-xl px-4 py-3 focus:outline-none focus:border-[#f3af22] text-white">
            </div>
            <div>
                <label class="block text-zinc-400 mb-1">Password</label>
                <input type="password" name="password" required class="w-full bg-zinc-950 border border-zinc-800 rounded-xl px-4 py-3 focus:outline-none focus:border-[#f3af22] text-white">
            </div>
            <button type="submit" name="register" class="w-full bg-[#f3af22] text-black font-bold py-3 rounded-xl hover:opacity-90 transition">Daftar Sekarang</button>
        </form>
        
        <div class="text-center text-xs text-zinc-400 mt-6">
            Sudah punya akun? <a href="login.php" class="text-[#f3af22] font-bold hover:underline">Login Disini</a>
        </div>
    </div>

</body>
</html>