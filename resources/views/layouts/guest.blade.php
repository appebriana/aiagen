<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            [x-cloak] { display: none !important; }
            body { font-family: 'Inter', sans-serif; }
            .font-outfit { font-family: 'Outfit', sans-serif; }
            .hero-gradient {
                background: radial-gradient(circle at 10% 20%, rgba(37, 99, 235, 0.05) 0%, transparent 50%),
                            radial-gradient(circle at 90% 80%, rgba(37, 99, 235, 0.05) 0%, transparent 50%);
            }
            .animate-float {
                animation: float 6s ease-in-out infinite;
            }
            @keyframes float {
                0% { transform: translateY(0px); }
                50% { transform: translateY(-15px); }
                100% { transform: translateY(0px); }
            }
        </style>
    </head>
    <body class="antialiased bg-white text-slate-900 hero-gradient min-h-screen flex items-center justify-center">
        
        <div class="w-full min-h-screen lg:grid lg:grid-cols-2">
            <!-- Left Side: Branding & Info (Hidden on Mobile) -->
            <div class="hidden lg:flex relative bg-slate-900 overflow-hidden items-center justify-center p-12">
                <!-- Background Decor -->
                <div class="absolute inset-0 opacity-20">
                    <div class="absolute top-0 -left-20 w-80 h-80 bg-primary-600 rounded-full blur-[120px]"></div>
                    <div class="absolute bottom-0 -right-20 w-80 h-80 bg-indigo-600 rounded-full blur-[120px]"></div>
                </div>

                <div class="relative z-10 max-w-lg text-center space-y-8">
                    <div class="inline-flex items-center gap-3">
                        <div class="w-12 h-12 bg-primary-600 rounded-2xl flex items-center justify-center shadow-lg shadow-primary-500/30">
                            <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z" />
                            </svg>
                        </div>
                        <span class="text-3xl font-bold font-outfit tracking-tight text-white uppercase">AI<span class="text-primary-500">AGEN</span></span>
                    </div>

                    <div class="animate-float">
                        <img src="{{ asset('aiagen_hero_illustration_1778125640694.png') }}" alt="AI Illustration" class="w-full max-w-sm mx-auto rounded-3xl shadow-2xl border border-white/10">
                    </div>

                    <div class="space-y-4">
                        <h2 class="text-3xl font-bold font-outfit text-white leading-tight">Solusi Chatbot AI <br> Terpercaya untuk Bisnis</h2>
                        <p class="text-slate-400 text-lg leading-relaxed">
                            Otomatisasi pesan WhatsApp Anda dengan kecerdasan buatan yang responsif dan cerdas.
                        </p>
                    </div>

                    <div class="pt-8 border-t border-white/10 flex items-center justify-center gap-8">
                        <div class="text-center">
                            <div class="text-2xl font-bold text-white">500+</div>
                            <div class="text-xs text-slate-500 uppercase font-bold tracking-widest">Bisnis</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-white">24/7</div>
                            <div class="text-xs text-slate-500 uppercase font-bold tracking-widest">Aktif</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-white">99%</div>
                            <div class="text-xs text-slate-500 uppercase font-bold tracking-widest">Akurasi</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Side: Form Content -->
            <div class="flex flex-col items-center justify-center p-8 lg:p-12 bg-white relative">
                <!-- Mobile Header (Only on Mobile) -->
                <div class="lg:hidden flex flex-col items-center mb-10">
                    <div class="w-14 h-14 bg-primary-600 rounded-2xl flex items-center justify-center shadow-xl shadow-primary-500/30">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                    </div>
                    <h2 class="mt-4 text-2xl font-bold font-outfit tracking-tight text-slate-900 uppercase">
                        AI<span class="text-primary-600">AGEN</span>
                    </h2>
                </div>

                <div class="w-full max-w-md">
                    <!-- Home link for desktop -->
                    <div class="hidden lg:block absolute top-12 left-12">
                        <a href="/" class="text-sm font-medium text-slate-500 hover:text-primary-600 transition-colors flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                            Beranda
                        </a>
                    </div>

                    <div class="space-y-1 text-center lg:text-left mb-10">
                        <h1 class="text-3xl font-bold font-outfit text-slate-900 tracking-tight">
                            @if(request()->routeIs('login'))
                                Selamat Datang Kembali
                            @else
                                Buat Akun Baru
                            @endif
                        </h1>
                        <p class="text-slate-500">
                            @if(request()->routeIs('login'))
                                Silakan masukkan detail Anda untuk mengakses dashboard.
                            @else
                                Daftarkan bisnis Anda dan mulai gunakan AI hari ini.
                            @endif
                        </p>
                    </div>

                    <div class="relative">
                        {{ $slot }}
                    </div>
                </div>

                <!-- Mobile Home link -->
                <div class="lg:hidden mt-10">
                    <a href="/" class="text-sm font-medium text-slate-500 hover:text-primary-600 transition-colors flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                        Beranda
                    </a>
                </div>
            </div>
        </div>
    </body>
</html>
