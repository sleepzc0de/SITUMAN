<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>500 - Kesalahan Sistem</title>
    @vite(['resources/css/app.css'])
</head>
<body class="bg-gradient-to-br from-navy-700 via-navy-800 to-navy-900 min-h-screen flex items-center justify-center p-4">
    <div class="text-center max-w-md">
        <div class="w-24 h-24 bg-navy-600/50 rounded-3xl flex items-center justify-center mx-auto mb-6">
            <svg class="w-12 h-12 text-gold-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                      d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
        </div>
        <h1 class="text-8xl font-bold text-gold-400 mb-4">500</h1>
        <p class="text-2xl font-semibold text-white mb-2">Kesalahan Sistem</p>
        <p class="text-navy-200 mb-8 leading-relaxed">
            Terjadi kesalahan pada sistem.<br>
            Tim kami telah diberitahu dan sedang menangani masalah ini.
        </p>
        <div class="flex flex-col sm:flex-row gap-3 justify-center">
            <a href="{{ route('dashboard') }}"
               class="inline-flex items-center justify-center gap-2 px-6 py-3
                      bg-gold-500 hover:bg-gold-600 text-navy-900 font-semibold
                      rounded-xl transition-colors duration-200">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                Kembali ke Dashboard
            </a>
            <button onclick="window.history.back()"
                    class="inline-flex items-center justify-center gap-2 px-6 py-3
                           bg-navy-600 hover:bg-navy-500 text-white font-semibold
                           rounded-xl transition-colors duration-200">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Halaman Sebelumnya
            </button>
        </div>
        <p class="text-navy-500 text-xs mt-8">
            SiTUMAN v2.0 · © {{ date('Y') }} Biro Manajemen BMN dan Pengadaan
        </p>
    </div>
</body>
</html>
