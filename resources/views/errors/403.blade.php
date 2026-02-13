<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 - Akses Ditolak</title>
    @vite(['resources/css/app.css'])
</head>
<body class="bg-gradient-to-br from-navy-700 via-navy-800 to-navy-900 min-h-screen flex items-center justify-center p-4">
    <div class="text-center">
        <h1 class="text-9xl font-bold text-gold-400">403</h1>
        <p class="text-2xl font-semibold text-white mt-4">Akses Ditolak</p>
        <p class="text-navy-200 mt-2">Anda tidak memiliki izin untuk mengakses halaman ini.</p>
        <a href="{{ route('dashboard') }}" class="inline-block mt-6 px-6 py-3 bg-gold-500 hover:bg-gold-600 text-navy-900 font-semibold rounded-lg transition">
            Kembali ke Dashboard
        </a>
    </div>
</body>
</html>
