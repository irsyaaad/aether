<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Masuk | Aether Data Lab — Portal Riset & Olah Data</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo_aether.png') }}">
    <link rel="shortcut icon" type="image/png" href="{{ asset('images/logo_aether.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/logo_aether.png') }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'ui-sans-serif', 'system-ui', 'sans-serif'],
                    },
                    colors: {
                        aether: {
                            blue: '#2B8BD1',
                            blue2: '#58B7EA',
                            deep: '#1768B5',
                            navy: '#102654',
                            gold: '#F6C75A',
                            orange: '#F29B3A',
                            cream: '#FBF3E5',
                        }
                    },
                    keyframes: {
                        float: {
                            '0%, 100%': { transform: 'translateY(0) scale(1.02)' },
                            '50%': { transform: 'translateY(-6px) scale(1.02)' },
                        }
                    },
                    animation: {
                        float: 'float 7s ease-in-out infinite',
                    }
                }
            }
        }
    </script>
    <style>
        body {
            font-family: 'Plus Jakarta Sans', ui-sans-serif, system-ui, sans-serif;
        }
        .pattern-bg {
            background-color: #102654;
            background-image: radial-gradient(rgba(88, 183, 234, 0.18) 1.5px, transparent 1.5px), radial-gradient(rgba(242, 155, 58, 0.12) 1.5px, #102654 1.5px);
            background-size: 30px 30px;
            background-position: 0 0, 15px 15px;
        }
    </style>
</head>
<body class="bg-slate-50 text-slate-900 antialiased h-full">

    <div class="flex min-h-screen">
        <!-- Left Panel - Aether Data Lab Visual & Branding -->
        <div class="relative hidden w-1/2 lg:flex flex-col justify-between overflow-hidden p-12 pattern-bg text-white">
            <!-- Background Image with Overlay -->
            <div class="absolute inset-0">
                <img 
                    src="https://images.unsplash.com/photo-1551288049-bebda4e38f71?auto=format&fit=crop&w=1600&q=85" 
                    alt="Aether Data Lab Analytics Dashboard" 
                    class="w-full h-full object-cover scale-105 animate-float opacity-35"
                />
            </div>
            
            <!-- Gradient Overlay for Deep Contrast -->
            <div class="absolute inset-0 bg-gradient-to-t from-[#102654] via-[#102654]/85 to-[#1768B5]/70"></div>
            
            <!-- Top Brand Badge -->
            <div class="relative z-10 flex items-center justify-between">
                <a href="{{ url('/') }}" class="inline-flex items-center gap-3 bg-white/95 px-3.5 py-2 rounded-2xl shadow-xl backdrop-blur hover:bg-white transition group border border-white/20">
                    <img src="{{ asset('images/logo_aether.png') }}" alt="Aether Data Lab" class="h-9 w-auto object-contain rounded-lg">
                    <div class="leading-tight">
                        <div class="text-[13px] font-extrabold text-[#102654] tracking-tight">Aether Data Lab</div>
                        <div class="text-[10px] font-bold text-[#2B8BD1] tracking-wider uppercase">Research &amp; Analytics</div>
                    </div>
                </a>

                <a href="{{ url('/') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold px-3 py-1.5 rounded-xl bg-white/10 hover:bg-white/20 text-white backdrop-blur border border-white/15 transition-all">
                    <span>Beranda</span>
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                </a>
            </div>

            <!-- Bottom Content & Testimonial -->
            <div class="relative z-10 mt-auto space-y-6">
                <!-- Research Metrics Pills -->
                <div class="grid grid-cols-3 gap-3">
                    <div class="bg-white/10 backdrop-blur-md p-3.5 rounded-2xl border border-white/15 shadow-lg">
                        <div class="text-xs text-sky-200 font-medium">Klien Tuntas</div>
                        <div class="text-lg font-extrabold text-white mt-0.5">1.200+</div>
                        <div class="text-[10px] text-sky-300">Skripsi & Tesis</div>
                    </div>
                    <div class="bg-white/10 backdrop-blur-md p-3.5 rounded-2xl border border-white/15 shadow-lg">
                        <div class="text-xs text-amber-200 font-medium">Metode Riset</div>
                        <div class="text-lg font-extrabold text-[#F6C75A] mt-0.5">35+ Tools</div>
                        <div class="text-[10px] text-amber-200/80">Kuantitatif & Kualitatif</div>
                    </div>
                    <div class="bg-white/10 backdrop-blur-md p-3.5 rounded-2xl border border-white/15 shadow-lg">
                        <div class="text-xs text-emerald-200 font-medium">Tingkat Lolos</div>
                        <div class="text-lg font-extrabold text-emerald-300 mt-0.5">99.4%</div>
                        <div class="text-[10px] text-emerald-200/80">Ujian Sidang</div>
                    </div>
                </div>

                <blockquote class="bg-white/10 backdrop-blur-md p-6 rounded-3xl border border-white/15 shadow-2xl space-y-3">
                    <div class="flex items-center gap-1 text-amber-300">
                        @for($i = 0; $i < 5; $i++)
                            <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/></svg>
                        @endfor
                        <span class="text-xs font-bold text-white ml-1.5">4.9 / 5.0 Rating Riset</span>
                    </div>
                    <p class="text-sm font-medium leading-relaxed text-white/95">
                        &ldquo;Data kamu ada. Insight-nya belum? Tim konsultan metodologi Aether memandu olah data, interpretasi bab 4, hingga simulasi tanya jawab penguji.&rdquo;
                    </p>
                    <footer class="flex items-center gap-2 pt-1 border-t border-white/10 text-xs font-semibold text-sky-200">
                        <span class="w-2 h-2 rounded-full bg-[#58B7EA]"></span>
                        <span>Aether Research Analytics &amp; Consulting Team</span>
                    </footer>
                </blockquote>
            </div>
        </div>

        <!-- Right Panel - Login Form -->
        <div class="flex w-full flex-col justify-center px-6 py-10 lg:w-1/2 sm:px-12 md:px-20 xl:px-28 bg-white">
            <div class="mx-auto w-full max-w-sm">
                <!-- Back to Landing Link -->
                <a href="{{ url('/') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-500 hover:text-[#2B8BD1] mb-6 transition-colors group">
                    <svg class="w-4 h-4 transition-transform group-hover:-translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    <span>Kembali ke Beranda</span>
                </a>

                <!-- Mobile Header Brand -->
                <div class="mb-6 flex items-center gap-3 lg:hidden">
                    <a href="{{ url('/') }}" class="inline-flex items-center gap-2.5">
                        <img src="{{ asset('images/logo_aether.png') }}" alt="Aether" class="h-10 w-auto object-contain rounded-lg">
                        <div>
                            <div class="text-base font-extrabold text-[#102654]">Aether Data Lab</div>
                            <div class="text-[11px] font-semibold text-[#2B8BD1]">Research &amp; Analytics</div>
                        </div>
                    </a>
                </div>

                <div class="mb-6">
                    <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-sky-50 text-[#1768B5] text-xs font-bold mb-2.5 border border-sky-100">
                        <span class="w-2 h-2 rounded-full bg-[#2B8BD1] animate-pulse"></span>
                        Portal Klien &amp; Riset
                    </div>
                    <h1 class="text-2xl font-extrabold tracking-tight text-slate-900">
                        Masuk ke Akun Anda
                    </h1>
                    <p class="text-xs sm:text-sm text-slate-500 mt-1">
                        Akses dashboard konsultasi, status olah data, dan materi bimbingan.
                    </p>
                </div>

                @if(Session::has('status'))
                    <div class="mb-4 text-xs sm:text-sm font-medium text-emerald-800 bg-emerald-50 border border-emerald-200 p-3 rounded-xl flex items-center gap-2">
                        <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        <span>{{ Session::get('status') }}</span>
                    </div>
                @endif

                @if(Session::has('success'))
                    <div class="mb-4 text-xs sm:text-sm font-medium text-emerald-800 bg-emerald-50 border border-emerald-200 p-3 rounded-xl flex items-center gap-2">
                        <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        <span>{{ Session::get('success') }}</span>
                    </div>
                @endif

                @if(Session::has('error'))
                    <div class="mb-4 text-xs sm:text-sm font-medium text-red-700 bg-red-50 border border-red-200 p-3 rounded-xl flex items-center gap-2">
                        <svg class="w-4 h-4 text-red-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <span>{{ Session::get('error') }}</span>
                    </div>
                @endif

                @if($errors->any())
                    <div class="mb-4 rounded-xl bg-red-50 p-3.5 border border-red-200">
                        <div class="text-xs text-red-700 font-semibold mb-1">Terjadi kesalahan input:</div>
                        <ul class="list-disc list-inside space-y-1 text-xs text-red-600">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ url('auth/login') }}" method="POST" id="loginForm" class="space-y-4">
                    @csrf

                    <div>
                        <label for="email" class="text-slate-700 font-bold block text-xs uppercase tracking-wider">Email atau Username</label>
                        <div class="relative mt-1.5">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                            </div>
                            <input
                                id="email"
                                type="text"
                                name="email"
                                value="{{ old('email') }}"
                                class="block w-full pl-10 pr-3.5 py-2.5 rounded-xl border border-slate-300 text-slate-900 shadow-sm focus:border-[#2B8BD1] focus:outline-none focus:ring-2 focus:ring-[#58B7EA]/30 text-sm font-medium transition"
                                placeholder="nama@email.com / username"
                                autocomplete="username"
                                required
                                autofocus
                            />
                        </div>
                    </div>

                    <div>
                        <div class="flex items-center justify-between">
                            <label for="password" class="text-slate-700 font-bold block text-xs uppercase tracking-wider">Password</label>
                            <a
                                href="{{ url('forgot-password') }}"
                                class="text-xs font-bold text-[#1768B5] hover:text-[#2B8BD1] hover:underline"
                            >
                                Lupa password?
                            </a>
                        </div>
                        <div class="relative mt-1.5">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                </svg>
                            </div>
                            <input
                                id="password"
                                type="password"
                                name="password"
                                class="block w-full pl-10 pr-10 py-2.5 rounded-xl border border-slate-300 text-slate-900 shadow-sm focus:border-[#2B8BD1] focus:outline-none focus:ring-2 focus:ring-[#58B7EA]/30 text-sm font-medium transition"
                                placeholder="••••••••"
                                autocomplete="current-password"
                                required
                            />
                            <button
                                type="button"
                                id="togglePasswordBtn"
                                class="absolute inset-y-0 right-0 flex items-center pr-3.5 text-slate-400 hover:text-slate-600"
                                aria-label="Toggle password visibility"
                            >
                                <svg id="eyeIcon" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                <svg id="eyeSlashIcon" class="h-4 w-4 hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Captcha Field -->
                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <label for="captcha" class="text-slate-700 font-bold block text-xs uppercase tracking-wider">Kode Keamanan</label>
                            <button
                                type="button"
                                onclick="refreshCaptcha()"
                                class="inline-flex items-center gap-1 text-xs font-semibold text-[#1768B5] hover:text-[#2B8BD1] transition"
                                title="Ganti kode keamanan"
                            >
                                <svg id="captchaRefreshIcon" class="h-3.5 w-3.5 text-[#2B8BD1]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                </svg>
                                <span>Ganti Kode</span>
                            </button>
                        </div>
                        
                        <div class="flex items-center gap-3">
                            <div class="cursor-pointer overflow-hidden rounded-xl border border-slate-200 shadow-xs hover:border-[#2B8BD1] transition bg-slate-50 p-1 shrink-0" onclick="refreshCaptcha()" title="Klik untuk refresh kode">
                                <img src="{{ captcha_src('default') }}" alt="Captcha" id="captcha_image" class="h-9 w-auto object-contain block rounded" />
                            </div>
                            <input
                                id="captcha"
                                type="text"
                                name="captcha"
                                class="block w-full rounded-xl border border-slate-300 px-3 py-2 text-slate-900 shadow-sm focus:border-[#2B8BD1] focus:outline-none focus:ring-2 focus:ring-[#58B7EA]/30 text-sm font-bold uppercase font-mono tracking-widest text-center"
                                placeholder="Ketik Kode"
                                required
                                autocomplete="off"
                                oninput="this.value = this.value.toUpperCase()"
                            />
                        </div>
                    </div>

                    <div class="flex items-center justify-between pt-1">
                        <label class="flex items-center gap-2 cursor-pointer select-none">
                            <input
                                type="checkbox"
                                name="remember"
                                value="1"
                                class="h-4 w-4 rounded border-slate-300 text-[#2B8BD1] focus:ring-[#2B8BD1]"
                            />
                            <span class="text-xs font-medium text-slate-600">Ingat sesi saya</span>
                        </label>
                    </div>

                    <div class="pt-2">
                        <button 
                            type="submit"
                            id="submitBtn"
                            class="w-full flex items-center justify-center py-2.5 px-4 rounded-xl shadow-lg shadow-sky-500/20 text-sm font-bold text-white bg-gradient-to-r from-[#2B8BD1] to-[#1768B5] hover:from-[#1768B5] hover:to-[#102654] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#2B8BD1] transition-all disabled:opacity-70 group"
                        >
                            <span id="btnText" class="inline-flex items-center gap-2">
                                <span>Masuk ke Dashboard</span>
                                <svg class="w-4 h-4 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                            </span>
                            <span id="btnSpinner" class="hidden items-center gap-2">
                                <svg class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <span>Memverifikasi...</span>
                            </span>
                        </button>
                    </div>
                    
                    <div class="text-center pt-3 border-t border-slate-100">
                        <span class="text-xs text-slate-500">Belum memiliki akun Aether? </span>
                        <a
                            href="{{ url('register') }}"
                            class="text-xs font-bold text-[#1768B5] hover:text-[#2B8BD1] hover:underline ml-1"
                        >
                            Daftar Akun Baru
                        </a>
                    </div>
                </form>

                <!-- Footer copyright -->
                <div class="mt-8 text-center text-[11px] text-slate-400">
                    &copy; {{ date('Y') }} Aether Data Lab. Hak Cipta Dilindungi.
                </div>
            </div>
        </div>
    </div>

    <script>
        // Password Visibility Toggle
        const togglePasswordBtn = document.getElementById('togglePasswordBtn');
        const passwordInput = document.getElementById('password');
        const eyeIcon = document.getElementById('eyeIcon');
        const eyeSlashIcon = document.getElementById('eyeSlashIcon');

        if (togglePasswordBtn && passwordInput) {
            togglePasswordBtn.addEventListener('click', function () {
                const isPassword = passwordInput.getAttribute('type') === 'password';
                passwordInput.setAttribute('type', isPassword ? 'text' : 'password');
                
                if (isPassword) {
                    eyeIcon.classList.add('hidden');
                    eyeSlashIcon.classList.remove('hidden');
                } else {
                    eyeIcon.classList.remove('hidden');
                    eyeSlashIcon.classList.add('hidden');
                }
                passwordInput.focus();
            });
        }

        // Refresh Captcha
        function refreshCaptcha() {
            const img = document.getElementById('captcha_image');
            const icon = document.getElementById('captchaRefreshIcon');
            const captchaInput = document.getElementById('captcha');

            if (icon) {
                icon.classList.add('animate-spin');
            }

            const newImg = new Image();
            newImg.src = '{{ captcha_src("default") }}' + '?' + Math.random();
            newImg.onload = function () {
                if (img) img.src = newImg.src;
                if (icon) {
                    setTimeout(() => icon.classList.remove('animate-spin'), 300);
                }
                if (captchaInput) {
                    captchaInput.value = '';
                    captchaInput.focus();
                }
            };
            newImg.onerror = function () {
                if (img) img.src = newImg.src;
                if (icon) icon.classList.remove('animate-spin');
            };
        }

        // Submit button loading state
        const loginForm = document.getElementById('loginForm');
        const submitBtn = document.getElementById('submitBtn');
        const btnText = document.getElementById('btnText');
        const btnSpinner = document.getElementById('btnSpinner');

        if (loginForm && submitBtn) {
            loginForm.addEventListener('submit', function () {
                submitBtn.disabled = true;
                if (btnText && btnSpinner) {
                    btnText.classList.add('hidden');
                    btnSpinner.classList.remove('hidden');
                    btnSpinner.classList.add('flex');
                }
            });
        }

        window.addEventListener('pageshow', function () {
            if (submitBtn) submitBtn.disabled = false;
            if (btnText && btnSpinner) {
                btnText.classList.remove('hidden');
                btnSpinner.classList.add('hidden');
                btnSpinner.classList.remove('flex');
            }
        });
    </script>
</body>
</html>
