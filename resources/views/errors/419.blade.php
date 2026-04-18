<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>419 - Sesi Kedaluwarsa</title>
    @vite(['resources/css/app.css'])
</head>
<body class="bg-gradient-to-br from-navy-700 via-navy-800 to-navy-900 min-h-screen flex items-center justify-center p-4">
    <div class="text-center max-w-md">
        <div class="w-24 h-24 bg-navy-600/50 rounded-3xl flex items-center justify-center mx-auto mb-6">
            <svg class="w-12 h-12 text-gold-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                      d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>
        <h1 class="text-8xl font-bold text-gold-400 mb-4">419</h1>
        <p class="text-2xl font-semibold text-white mb-2">Sesi Kedaluwarsa</p>
        <p class="text-navy-200 mb-8 leading-relaxed">
            Sesi Anda telah berakhir karena tidak ada aktivitas.<br>
            Silakan muat ulang halaman atau login kembali.
        </p>
        <div class="flex flex-col sm:flex-row gap-3 justify-center">
            <button onclick="window.location.reload()"
                    class="inline-flex items-center justify-center gap-2 px-6 py-3
                           bg-gold-500 hover:bg-gold-600 text-navy-900 font-semibold
                           rounded-xl transition-colors duration-200">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
                Muat Ulang Halaman
            </button>
            <a href="{{ route('login') }}"
               class="inline-flex items-center justify-center gap-2 px-6 py-3
                      bg-navy-600 hover:bg-navy-500 text-white font-semibold
                      rounded-xl transition-colors duration-200">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                </svg>
                Login Kembali
            </a>
        </div>
        <p class="text-navy-500 text-xs mt-8">
            SiTUMAN v2.0 · © {{ date('Y') }} Biro Manajemen BMN dan Pengadaan
        </p>
    </div>
</body>
</html>
