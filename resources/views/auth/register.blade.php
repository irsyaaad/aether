<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Daftar Akun | Aether Data Lab — Solusi Riset & Olah Data</title>
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
        .valid-rule {
            color: #10b981;
            font-weight: 600;
        }
        .invalid-rule {
            color: #94a3b8;
        }
        .valid-rule::before {
            content: "✓ ";
            color: #10b981;
            font-weight: 800;
        }
        .invalid-rule::before {
            content: "○ ";
            color: #cbd5e1;
        }
    </style>
</head>
<body class="bg-slate-50 text-slate-900 antialiased h-full">

    <div class="flex min-h-screen">
        <!-- Left Panel - Aether Data Lab Visual & Value Props -->
        <div class="relative hidden w-1/2 lg:flex flex-col justify-between overflow-hidden p-8 lg:p-12 pattern-bg text-white lg:sticky lg:top-0 lg:h-screen">
            <!-- Background Image with Overlay -->
            <div class="absolute inset-0">
                <img 
                    src="https://images.unsplash.com/photo-1522071820081-009f0129c71c?auto=format&fit=crop&w=1600&q=85" 
                    alt="Aether Data Lab Mentorship Collaboration" 
                    class="w-full h-full object-cover scale-105 animate-float opacity-30"
                />
            </div>
            
            <!-- Gradient Overlay for Deep Contrast -->
            <div class="absolute inset-0 bg-gradient-to-t from-[#102654] via-[#102654]/85 to-[#1768B5]/70"></div>
            
            <!-- Top Brand Bar -->
            <div class="relative z-10 flex items-center justify-between">
                <a href="{{ url('/') }}" class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl bg-white/10 hover:bg-white/20 text-white backdrop-blur border border-white/15 transition-all text-xs font-semibold group">
                    <span class="w-2 h-2 rounded-full bg-[#58B7EA] group-hover:scale-125 transition-transform"></span>
                    <span>Aether Data Lab</span>
                </a>

                <a href="{{ url('/') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold px-3 py-1.5 rounded-xl bg-white/10 hover:bg-white/20 text-white backdrop-blur border border-white/15 transition-all">
                    <span>Beranda</span>
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                </a>
            </div>

            <!-- Center Hero Logo -->
            <div class="relative z-10 my-auto flex flex-col items-center justify-center text-center py-6 px-4">
                <div class="relative group max-w-sm sm:max-w-md w-full">
                    <!-- Ambient Glow behind card matching Aether brand colors -->
                    <div class="absolute -inset-2 bg-gradient-to-r from-sky-400/35 via-[#F6C75A]/25 to-sky-300/35 rounded-3xl blur-2xl opacity-75 group-hover:opacity-100 transition duration-700"></div>
                    
                    <!-- White Glassmorphism Card for Logo -->
                    <div class="relative bg-white/95 backdrop-blur-xl px-8 py-8 sm:px-12 sm:py-10 rounded-3xl shadow-2xl shadow-sky-950/40 border border-white/40 flex flex-col items-center justify-center transition-all duration-300 group-hover:scale-[1.02]">
                        <img 
                            src="{{ asset('images/logo_aether.png') }}" 
                            alt="Aether Data Lab" 
                            class="h-20 sm:h-24 md:h-28 lg:h-32 w-auto object-contain max-w-full drop-shadow-sm transition-transform duration-300"
                        />
                    </div>
                </div>
            </div>

            <!-- Bottom Content & Benefits -->
            <div class="relative z-10 mt-auto space-y-6">
                <!-- Why Join Us List -->
                <div class="bg-white/10 backdrop-blur-md p-6 rounded-3xl border border-white/15 shadow-2xl space-y-4">
                    <div class="text-xs font-bold uppercase tracking-widest text-[#F6C75A]">Keuntungan Akun Klien Aether</div>
                    <ul class="space-y-3 text-xs sm:text-sm text-white/95">
                        <li class="flex items-start gap-2.5">
                            <span class="w-5 h-5 rounded-full bg-emerald-400/20 text-emerald-300 flex items-center justify-center shrink-0 mt-0.5 text-xs font-bold">✓</span>
                            <span><strong>Diagnosis Dataset Gratis:</strong> Cek kelayakan data, multikolinearitas, dan normalitas sebelum bimbingan.</span>
                        </li>
                        <li class="flex items-start gap-2.5">
                            <span class="w-5 h-5 rounded-full bg-sky-400/20 text-sky-300 flex items-center justify-center shrink-0 mt-0.5 text-xs font-bold">✓</span>
                            <span><strong>Private 1-on-1 Mentor:</strong> Bimbingan langsung dengan praktisi data riset hingga paham bab 4.</span>
                        </li>
                        <li class="flex items-start gap-2.5">
                            <span class="w-5 h-5 rounded-full bg-amber-400/20 text-amber-300 flex items-center justify-center shrink-0 mt-0.5 text-xs font-bold">✓</span>
                            <span><strong>Garansi Revisi &amp; Re-run:</strong> Dampingan penuh bila ada masukan dari dosen pembimbing/penguji.</span>
                        </li>
                    </ul>
                </div>

                <!-- Footer Quote -->
                <div class="flex items-center justify-between text-xs text-sky-200 px-2">
                    <span>Lebih dari 1.200+ periset mempercayakan datanya</span>
                    <span class="font-bold text-[#F6C75A]">⭐ 4.9/5 Rating</span>
                </div>
            </div>
        </div>

        <!-- Right Panel - Register Form -->
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
                <div class="mb-6 flex items-center justify-between lg:hidden">
                    <a href="{{ url('/') }}" class="inline-flex items-center">
                        <img src="{{ asset('images/logo_aether.png') }}" alt="Aether Data Lab" class="h-10 w-auto object-contain">
                    </a>
                    <a href="{{ url('/') }}" class="inline-flex items-center gap-1 text-xs font-semibold text-slate-500 hover:text-[#2B8BD1] transition-colors">
                        <span>Beranda</span>
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                    </a>
                </div>

                <div class="mb-6">
                    <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-sky-50 text-[#1768B5] text-xs font-bold mb-2.5 border border-sky-100">
                        <span class="w-2 h-2 rounded-full bg-[#2B8BD1] animate-pulse"></span>
                        Registrasi Klien Baru
                    </div>
                    <h1 class="text-2xl font-extrabold tracking-tight text-slate-900">
                        Daftar Akun Aether
                    </h1>
                    <p class="text-xs sm:text-sm text-slate-500 mt-1">
                        Mulai konsultasi olah data riset dan terhubung dengan mentor ahli.
                    </p>
                </div>

                @if(Session::has('error'))
                    <div class="mb-4 text-xs sm:text-sm font-medium text-red-700 bg-red-50 border border-red-200 p-3 rounded-xl flex items-center gap-2">
                        <svg class="w-4 h-4 text-red-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <span>{{ Session::get('error') }}</span>
                    </div>
                @endif

                @if($errors->any())
                    <div class="mb-4 rounded-xl bg-red-50 p-3.5 border border-red-200">
                        <div class="text-xs text-red-700 font-semibold mb-1">Pendaftaran belum berhasil:</div>
                        <ul class="list-disc list-inside space-y-1 text-xs text-red-600">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ url('register') }}" method="POST" id="registerForm" class="space-y-4">
                    @csrf

                    <!-- Nama Lengkap -->
                    <div>
                        <label for="full_name" class="text-slate-700 font-bold block text-xs uppercase tracking-wider">Nama Lengkap</label>
                        <div class="relative mt-1.5">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                            </div>
                            <input
                                id="full_name"
                                type="text"
                                name="full_name"
                                value="{{ old('full_name') }}"
                                class="block w-full pl-10 pr-3.5 py-2.5 rounded-xl border border-slate-300 text-slate-900 shadow-sm focus:border-[#2B8BD1] focus:outline-none focus:ring-2 focus:ring-[#58B7EA]/30 text-sm font-medium transition"
                                placeholder="Contoh: Sarah Anindita"
                                required
                                autofocus
                            />
                        </div>
                    </div>

                    <!-- Email -->
                    <div>
                        <label for="email" class="text-slate-700 font-bold block text-xs uppercase tracking-wider">Alamat Email</label>
                        <div class="relative mt-1.5">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <input
                                id="email"
                                type="email"
                                name="email"
                                value="{{ old('email') }}"
                                class="block w-full pl-10 pr-3.5 py-2.5 rounded-xl border border-slate-300 text-slate-900 shadow-sm focus:border-[#2B8BD1] focus:outline-none focus:ring-2 focus:ring-[#58B7EA]/30 text-sm font-medium transition"
                                placeholder="nama@kampus.ac.id"
                                required
                            />
                        </div>
                    </div>

                    <!-- Jenis Kelamin & Tanggal Lahir (Grid 2 Kolom) -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                        <!-- Jenis Kelamin -->
                        <div>
                            <label for="jenis_kelamin" class="text-slate-700 font-bold block text-xs uppercase tracking-wider">Jenis Kelamin</label>
                            <div class="relative mt-1.5">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                </div>
                                <select
                                    id="jenis_kelamin"
                                    name="jenis_kelamin"
                                    class="block w-full pl-10 pr-8 py-2.5 rounded-xl border border-slate-300 text-slate-900 bg-white shadow-sm focus:border-[#2B8BD1] focus:outline-none focus:ring-2 focus:ring-[#58B7EA]/30 text-sm font-medium transition cursor-pointer appearance-none"
                                    required
                                >
                                    <option value="" disabled {{ old('jenis_kelamin') ? '' : 'selected' }}>Pilih Jenis Kelamin</option>
                                    <option value="L" {{ old('jenis_kelamin') == 'L' ? 'selected' : '' }}>Laki-Laki</option>
                                    <option value="P" {{ old('jenis_kelamin') == 'P' ? 'selected' : '' }}>Perempuan</option>
                                </select>
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none text-slate-400">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                </div>
                            </div>
                        </div>

                        <!-- Tanggal Lahir -->
                        <div>
                            <label for="tgl_lahir" class="text-slate-700 font-bold block text-xs uppercase tracking-wider">Tanggal Lahir</label>
                            <div class="relative mt-1.5">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </div>
                                <input
                                    id="tgl_lahir"
                                    type="date"
                                    name="tgl_lahir"
                                    value="{{ old('tgl_lahir') }}"
                                    max="{{ date('Y-m-d') }}"
                                    class="block w-full pl-10 pr-3.5 py-2.5 rounded-xl border border-slate-300 text-slate-900 shadow-sm focus:border-[#2B8BD1] focus:outline-none focus:ring-2 focus:ring-[#58B7EA]/30 text-sm font-medium transition"
                                    required
                                />
                            </div>
                        </div>
                    </div>

                    <!-- Password -->
                    <div>
                        <label for="password" class="text-slate-700 font-bold block text-xs uppercase tracking-wider">Password</label>
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
                                placeholder="Minimal 6 karakter"
                                required
                                oninput="validatePassword()"
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

                    <!-- Konfirmasi Password -->
                    <div>
                        <label for="confirmPassword" class="text-slate-700 font-bold block text-xs uppercase tracking-wider">Konfirmasi Password</label>
                        <div class="relative mt-1.5">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                </svg>
                            </div>
                            <input
                                id="confirmPassword"
                                type="password"
                                name="password_confirmation"
                                class="block w-full pl-10 pr-10 py-2.5 rounded-xl border border-slate-300 text-slate-900 shadow-sm focus:border-[#2B8BD1] focus:outline-none focus:ring-2 focus:ring-[#58B7EA]/30 text-sm font-medium transition"
                                placeholder="Ulangi password"
                                required
                                oninput="checkPasswordMatch()"
                            />
                            <button
                                type="button"
                                id="toggleConfirmPasswordBtn"
                                class="absolute inset-y-0 right-0 flex items-center pr-3.5 text-slate-400 hover:text-slate-600"
                                aria-label="Toggle password confirmation visibility"
                            >
                                <svg id="confirmEyeIcon" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                <svg id="confirmEyeSlashIcon" class="h-4 w-4 hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" />
                                </svg>
                            </button>
                        </div>
                        <div id="passwordMismatch" class="hidden text-xs text-red-500 font-semibold mt-1">Password konfirmasi tidak cocok</div>
                    </div>

                    <!-- Password Rules Hint -->
                    <div class="bg-slate-50 border border-slate-200/80 p-3 rounded-xl text-xs space-y-1">
                        <div class="text-[11px] font-bold uppercase text-slate-500 tracking-wider mb-1">Ketentuan Password:</div>
                        <div id="ruleLength" class="invalid-rule">Minimal 6 karakter</div>
                        <div id="ruleMatch" class="invalid-rule">Konfirmasi password sesuai</div>
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

                    <div class="pt-2">
                        <button 
                            type="submit"
                            id="submitBtn"
                            class="w-full flex items-center justify-center py-2.5 px-4 rounded-xl shadow-lg shadow-sky-500/20 text-sm font-bold text-white bg-gradient-to-r from-[#2B8BD1] to-[#1768B5] hover:from-[#1768B5] hover:to-[#102654] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#2B8BD1] transition-all disabled:opacity-70 group"
                        >
                            <span id="btnText" class="inline-flex items-center gap-2">
                                <span>Buat Akun Sekarang</span>
                                <svg class="w-4 h-4 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                            </span>
                            <span id="btnSpinner" class="hidden items-center gap-2">
                                <svg class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <span>Mendaftarkan Akun...</span>
                            </span>
                        </button>
                    </div>
                    
                    <div class="text-center pt-3 border-t border-slate-100">
                        <span class="text-xs text-slate-500">Sudah memiliki akun Aether? </span>
                        <a
                            href="{{ url('auth/login') }}"
                            class="text-xs font-bold text-[#1768B5] hover:text-[#2B8BD1] hover:underline ml-1"
                        >
                            Masuk di Sini
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

        // Confirm Password Visibility Toggle
        const toggleConfirmPasswordBtn = document.getElementById('toggleConfirmPasswordBtn');
        const confirmPasswordInput = document.getElementById('confirmPassword');
        const confirmEyeIcon = document.getElementById('confirmEyeIcon');
        const confirmEyeSlashIcon = document.getElementById('confirmEyeSlashIcon');

        if (toggleConfirmPasswordBtn && confirmPasswordInput) {
            toggleConfirmPasswordBtn.addEventListener('click', function () {
                const isPassword = confirmPasswordInput.getAttribute('type') === 'password';
                confirmPasswordInput.setAttribute('type', isPassword ? 'text' : 'password');
                
                if (isPassword) {
                    confirmEyeIcon.classList.add('hidden');
                    confirmEyeSlashIcon.classList.remove('hidden');
                } else {
                    confirmEyeIcon.classList.remove('hidden');
                    confirmEyeSlashIcon.classList.add('hidden');
                }
                confirmPasswordInput.focus();
            });
        }

        // Live Password Validation
        function validatePassword() {
            const pwd = document.getElementById('password').value;
            const ruleLength = document.getElementById('ruleLength');

            if (pwd.length >= 6) {
                ruleLength.className = 'valid-rule';
            } else {
                ruleLength.className = 'invalid-rule';
            }

            checkPasswordMatch();
        }

        function checkPasswordMatch() {
            const pwd = document.getElementById('password').value;
            const confirm = document.getElementById('confirmPassword').value;
            const ruleMatch = document.getElementById('ruleMatch');
            const mismatchNotice = document.getElementById('passwordMismatch');

            if (confirm.length > 0 && pwd === confirm) {
                ruleMatch.className = 'valid-rule';
                mismatchNotice.classList.add('hidden');
            } else {
                ruleMatch.className = 'invalid-rule';
                if (confirm.length > 0 && pwd !== confirm) {
                    mismatchNotice.classList.remove('hidden');
                } else {
                    mismatchNotice.classList.add('hidden');
                }
            }
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
            newImg.src = '{{ captcha_src("default") }}' + '&_t=' + Date.now();
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

        // Form Submission validation & loading
        const registerForm = document.getElementById('registerForm');
        const submitBtn = document.getElementById('submitBtn');
        const btnText = document.getElementById('btnText');
        const btnSpinner = document.getElementById('btnSpinner');

        if (registerForm && submitBtn) {
            registerForm.addEventListener('submit', function (e) {
                const pwd = document.getElementById('password').value;
                const confirm = document.getElementById('confirmPassword').value;

                if (pwd !== confirm) {
                    e.preventDefault();
                    document.getElementById('confirmPassword').focus();
                    document.getElementById('passwordMismatch').classList.remove('hidden');
                    return false;
                }

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
