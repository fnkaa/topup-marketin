<?php
session_start();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bantuan - Market.in</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-[#0d0b04] text-white p-6 flex justify-center items-center min-h-screen">

    <div class="bg-[#0f1113] w-full max-w-[1150px] rounded-2xl border border-zinc-800 shadow-2xl p-8 grid grid-cols-1 md:grid-cols-2 gap-8 items-center">
        <div>
            <a href="index.php" class="text-xs text-[#f3af22] hover:underline">← Kembali ke Beranda</a>
            <h2 class="text-2xl font-black mt-4 text-[#f3af22]"> Pusat Bantuan Market.in</h2>
            <p class="text-zinc-400 text-sm mt-1">Mengalami kendala saat top up atau pembayaran belum masuk? Hubungi kami langsung.</p>
            
            <div class="mt-6 space-y-4">
                <div class="p-4 bg-zinc-900 border border-zinc-800 rounded-xl">
                    <h4 class="font-bold text-sm text-white">Hubungi via WhatsApp</h4>
                    <p class="text-xs text-zinc-500 mt-1">Layanan respon cepat CS 24 jam nonstop.</p>
                    <a href="https://wa.me/6285858026589" class="mt-3 inline-block text-xs bg-green-600 text-white font-bold px-4 py-2 rounded-lg hover:bg-green-700">Chat WA Now</a>
                </div>
            </div>
        </div>
        
        <div class="hidden md:flex justify-center text-8xl opacity-20 select-none">
            🎧
        </div>
    </div>

</body>
</html>