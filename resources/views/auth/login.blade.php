<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - SiTUMAN</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            33% { transform: translateY(-15px) rotate(1deg); }
            66% { transform: translateY(-8px) rotate(-1deg); }
        }
        @keyframes pulse-ring {
            0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(212, 175, 55, 0.4); }
            70% { transform: scale(1); box-shadow: 0 0 0 12px rgba(212, 175, 55, 0); }
            100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(212, 175, 55, 0); }
        }
        @keyframes slideUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes shimmer {
            0% { background-position: -200% center; }
            100% { background-position: 200% center; }
        }
        @keyframes orb-move-1 {
            0%, 100% { transform: translate(0, 0) scale(1); }
            50% { transform: translate(40px, -30px) scale(1.1); }
        }
        @keyframes orb-move-2 {
            0%, 100% { transform: translate(0, 0) scale(1.1); }
            50% { transform: translate(-30px, 40px) scale(0.9); }
        }
        .animate-float { animation: float 6s ease-in-out infinite; }
        .animate-pulse-ring { animation: pulse-ring 2.5s ease-in-out infinite; }
        .animate-slide-up { animation: slideUp 0.6s ease-out forwards; }
        .animate-orb-1 { animation: orb-move-1 8s ease-in-out infinite; }
        .animate-orb-2 { animation: orb-move-2 10s ease-in-out infinite; }
        .card-glass {
            background: rgba(255, 255, 255, 0.04);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        .input-modern {
            background: rgba(255, 255, 255, 0.06);
            border: 1.5px solid rgba(255, 255, 255, 0.12);
            color: white;
            transition: all 0.3s ease;
        }
        .input-modern::placeholder { color: rgba(255,255,255,0.35); }
        .input-modern:focus {
            background: rgba(255, 255, 255, 0.1);
            border-color: rgba(212, 175, 55, 0.7);
            box-shadow: 0 0 0 4px rgba(212, 175, 55, 0.12), inset 0 1px 0 rgba(255,255,255,0.1);
            outline: none;
        }
        .input-modern:focus + .input-icon { color: #d4af37; }
        .btn-login {
            background: linear-gradient(135deg, #d4af37 0%, #f0cc6d 50%, #d4af37 100%);
            background-size: 200% auto;
            transition: all 0.4s ease;
            position: relative;
            overflow: hidden;
        }
        .btn-login::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, transparent 30%, rgba(255,255,255,0.25) 50%, transparent 70%);
            transform: translateX(-100%);
            transition: transform 0.5s ease;
        }
        .btn-login:hover { background-position: right center; box-shadow: 0 8px 30px rgba(212, 175, 55, 0.4); transform: translateY(-2px); }
        .btn-login:hover::before { transform: translateX(100%); }
        .btn-login:active { transform: translateY(0); }
        .checkbox-custom:checked { background-color: #d4af37; border-color: #d4af37; }
        .logo-ring { animation: pulse-ring 2.5s ease-in-out infinite; border-radius: 50%; display: inline-block; }
        .divider-line {
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.15), transparent);
            height: 1px;
        }
        .error-shake {
            animation: shake 0.4s ease-in-out;
        }
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            20% { transform: translateX(-6px); }
            40% { transform: translateX(6px); }
            60% { transform: translateX(-4px); }
            80% { transform: translateX(4px); }
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4 overflow-hidden relative"
      style="background: linear-gradient(135deg, #0a1628 0%, #0d1f3c 40%, #111827 70%, #0a1628 100%);">

    <!-- Animated Background Orbs -->
    <div class="absolute inset-0 overflow-hidden pointer-events-none">
        <div class="animate-orb-1 absolute w-96 h-96 rounded-full opacity-20"
             style="background: radial-gradient(circle, #1e3a5f 0%, transparent 70%); top: -10%; left: -10%;"></div>
        <div class="animate-orb-2 absolute w-80 h-80 rounded-full opacity-15"
             style="background: radial-gradient(circle, #d4af37 0%, transparent 70%); bottom: -5%; right: -5%;"></div>
        <div class="absolute w-64 h-64 rounded-full opacity-10 animate-orb-1"
             style="background: radial-gradient(circle, #1a3a6b 0%, transparent 70%); top: 50%; left: 60%; animation-delay: 3s;"></div>

        <!-- Grid pattern overlay -->
        <div class="absolute inset-0 opacity-[0.03]"
             style="background-image: linear-gradient(rgba(255,255,255,0.5) 1px, transparent 1px), linear-gradient(90deg, rgba(255,255,255,0.5) 1px, transparent 1px); background-size: 50px 50px;"></div>
    </div>

    <!-- Main Content -->
    <div class="max-w-md w-full relative z-10">

        <!-- Logo Section -->
        <div class="text-center mb-8 animate-slide-up" style="animation-delay: 0s;">
            <div class="inline-flex items-center justify-center mb-5">
                <div class="logo-ring p-1" style="background: linear-gradient(135deg, #d4af37, #f0cc6d, #d4af37);">
                    <div class="w-16 h-16 rounded-full flex items-center justify-center"
                         style="background: linear-gradient(135deg, #0a1628, #0d1f3c);">
                        <!-- Lambang Kemenkeu SVG -->
                        <svg viewBox="0 0 40 40" class="w-9 h-9" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M20 4L4 14v4h32v-4L20 4z" fill="#d4af37" opacity="0.9"/>
                            <rect x="6" y="20" width="4" height="14" rx="1" fill="#d4af37" opacity="0.8"/>
                            <rect x="12" y="20" width="4" height="14" rx="1" fill="#d4af37" opacity="0.8"/>
                            <rect x="18" y="20" width="4" height="14" rx="1" fill="#d4af37" opacity="0.8"/>
                            <rect x="24" y="20" width="4" height="14" rx="1" fill="#d4af37" opacity="0.8"/>
                            <rect x="30" y="20" width="4" height="14" rx="1" fill="#d4af37" opacity="0.8"/>
                            <rect x="4" y="34" width="32" height="3" rx="1" fill="#d4af37"/>
                        </svg>
                    </div>
                </div>
            </div>
            <h1 class="text-4xl font-bold mb-2 tracking-tight"
                style="background: linear-gradient(135deg, #d4af37, #f0cc6d, #b8962e); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;">
                SiTUMAN
            </h1>
            <p class="text-sm font-medium mb-1" style="color: rgba(255,255,255,0.65);">
                Sistem Informasi Tata Usaha Biro Manajemen BMN dan Pengadaan
            </p>
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full mt-1"
                 style="background: rgba(212,175,55,0.1); border: 1px solid rgba(212,175,55,0.2);">
                <div class="w-1.5 h-1.5 rounded-full bg-green-400" style="animation: pulse 2s infinite;"></div>
                <span class="text-xs font-medium" style="color: rgba(212,175,55,0.9);">Kementerian Keuangan RI</span>
            </div>
        </div>

        <!-- Login Card -->
        <div class="card-glass rounded-2xl p-8 animate-slide-up" style="animation-delay: 0.15s;"
             x-data="{
                showPass: false,
                loading: false,
                email: '{{ old('email') }}',
                password: '',
                emailFocused: false,
                passFocused: false,
                submitForm(e) {
                    this.loading = true;
                }
             }">

            <!-- Card Header -->
            <div class="flex items-center gap-3 mb-7">
                <div class="flex-1 divider-line"></div>
                <span class="text-xs font-semibold uppercase tracking-widest px-3" style="color: rgba(255,255,255,0.4);">Masuk ke Sistem</span>
                <div class="flex-1 divider-line"></div>
            </div>

            <!-- Error Alert -->
            @if($errors->any())
            <div class="error-shake mb-6 px-4 py-3 rounded-xl flex items-start gap-3"
                 style="background: rgba(239,68,68,0.12); border: 1px solid rgba(239,68,68,0.25);">
                <div class="flex-shrink-0 w-5 h-5 rounded-full flex items-center justify-center mt-0.5"
                     style="background: rgba(239,68,68,0.2);">
                    <svg class="w-3 h-3 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <p class="text-sm text-red-300">{{ $errors->first() }}</p>
            </div>
            @endif

            <!-- Form -->
            <form method="POST" action="{{ route('login') }}" class="space-y-5" @submit="submitForm">
                @csrf

                <!-- Email Field -->
                <div class="space-y-1.5">
                    <label for="email" class="block text-xs font-semibold uppercase tracking-wider"
                           style="color: rgba(255,255,255,0.5);">Email</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <svg class="w-4 h-4 transition-colors duration-300 input-icon"
                                 :class="emailFocused ? 'text-yellow-400' : 'text-gray-500'"
                                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"/>
                            </svg>
                        </div>
                        <input type="email"
                               name="email"
                               id="email"
                               x-model="email"
                               @focus="emailFocused = true"
                               @blur="emailFocused = false"
                               placeholder="nama@kemenkeu.go.id"
                               class="input-modern w-full pl-11 pr-4 py-3.5 rounded-xl text-sm font-medium"
                               required autofocus>
                    </div>
                </div>

                <!-- Password Field -->
                <div class="space-y-1.5">
                    <label for="password" class="block text-xs font-semibold uppercase tracking-wider"
                           style="color: rgba(255,255,255,0.5);">Password</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <svg class="w-4 h-4 transition-colors duration-300"
                                 :class="passFocused ? 'text-yellow-400' : 'text-gray-500'"
                                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                        </div>
                        <input :type="showPass ? 'text' : 'password'"
                               name="password"
                               id="password"
                               x-model="password"
                               @focus="passFocused = true"
                               @blur="passFocused = false"
                               placeholder="••••••••••"
                               class="input-modern w-full pl-11 pr-12 py-3.5 rounded-xl text-sm font-medium"
                               required>
                        <!-- Toggle Password -->
                        <button type="button" @click="showPass = !showPass"
                                class="absolute inset-y-0 right-0 pr-4 flex items-center transition-colors duration-200"
                                style="color: rgba(255,255,255,0.35);"
                                :style="showPass ? 'color: #d4af37;' : ''">
                            <svg x-show="!showPass" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            <svg x-show="showPass" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Remember Me -->
                <div class="flex items-center justify-between">
                    <label class="flex items-center gap-2.5 cursor-pointer group">
                        <div class="relative">
                            <input type="checkbox" name="remember" id="remember"
                                   class="sr-only peer">
                            <div class="w-9 h-5 rounded-full transition-all duration-300 peer-checked:bg-yellow-500 peer-focus:ring-2 peer-focus:ring-yellow-500/30"
                                 style="background: rgba(255,255,255,0.1); border: 1.5px solid rgba(255,255,255,0.15);"
                                 onclick="this.previousElementSibling.click()"></div>
                            <div class="absolute top-0.5 left-0.5 w-4 h-4 rounded-full transition-all duration-300 peer-checked:translate-x-4 peer-checked:bg-white"
                                 style="background: rgba(255,255,255,0.5);"></div>
                        </div>
                        <span class="text-xs font-medium select-none" style="color: rgba(255,255,255,0.5);">Ingat saya</span>
                    </label>
                </div>

                <!-- Submit Button -->
                <button type="submit"
                        :disabled="loading"
                        class="btn-login w-full py-3.5 rounded-xl text-sm font-bold text-navy-900 flex items-center justify-center gap-2.5 mt-2 tracking-wide">
                    <template x-if="!loading">
                        <span class="flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                      d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                            </svg>
                            Masuk ke Sistem
                        </span>
                    </template>
                    <template x-if="loading">
                        <span class="flex items-center gap-2">
                            <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                            </svg>
                            Memproses...
                        </span>
                    </template>
                </button>
            </form>

            <!-- Card Footer Divider -->
            <div class="mt-7 pt-5" style="border-top: 1px solid rgba(255,255,255,0.07);">
                <div class="flex items-center justify-center gap-4">
                    <div class="flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/>
                        </svg>
                        <span class="text-xs" style="color: rgba(255,255,255,0.3);">Koneksi Aman SSL</span>
                    </div>
                    <div class="w-px h-3" style="background: rgba(255,255,255,0.1);"></div>
                    <div class="flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        <span class="text-xs" style="color: rgba(255,255,255,0.3);">Data Terenkripsi</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="text-center mt-6 animate-slide-up" style="animation-delay: 0.3s;">
            <p class="text-xs" style="color: rgba(255,255,255,0.25);">
                © 2026 Kementerian Keuangan Republik Indonesia · Hak Cipta Dilindungi
            </p>
        </div>
    </div>

</body>
</html>
