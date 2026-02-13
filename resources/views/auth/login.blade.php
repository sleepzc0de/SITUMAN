<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - SiTUMAN</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gradient-to-br from-navy-700 via-navy-800 to-navy-900 min-h-screen flex items-center justify-center p-4">
    <div class="max-w-md w-full">
        <!-- Logo & Title -->
        <div class="text-center mb-8">
            <h1 class="text-4xl font-bold text-gold-400 mb-2">SiTUMAN</h1>
            <p class="text-navy-200">Sistem Informasi Tata Usaha dan Manajemen</p>
            <p class="text-navy-300 text-sm mt-1">Kementerian Keuangan RI</p>
        </div>

        <!-- Login Card -->
        <div class="bg-white rounded-2xl shadow-2xl p-8">
            <h2 class="text-2xl font-bold text-navy-800 mb-6 text-center">Login</h2>

            @if($errors->any())
            <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg mb-6">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                    </svg>
                    {{ $errors->first() }}
                </div>
            </div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="space-y-6">
                @csrf

                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                    <input type="email"
                           name="email"
                           id="email"
                           value="{{ old('email') }}"
                           class="input-field w-full"
                           required
                           autofocus>
                </div>

                <!-- Password -->
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">Password</label>
                    <input type="password"
                           name="password"
                           id="password"
                           class="input-field w-full"
                           required>
                </div>

                <!-- Remember Me -->
                <div class="flex items-center">
                    <input type="checkbox"
                           name="remember"
                           id="remember"
                           class="h-4 w-4 text-navy-600 focus:ring-navy-500 border-gray-300 rounded">
                    <label for="remember" class="ml-2 block text-sm text-gray-900">
                        Ingat saya
                    </label>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="w-full btn-primary justify-center">
                    Login
                </button>
            </form>
        </div>

        <!-- Footer -->
        <div class="text-center mt-6">
            <p class="text-navy-300 text-sm">
                © 2026 Kementerian Keuangan RI
            </p>
        </div>
    </div>
</body>
</html>
