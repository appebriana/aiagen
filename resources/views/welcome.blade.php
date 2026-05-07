<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>AIAGEN - Solusi AI WhatsApp Cerdas untuk Bisnis Anda</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Styles / Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            font-family: 'Inter', sans-serif;
            scroll-behavior: smooth;
        }
        .font-outfit {
            font-family: 'Outfit', sans-serif;
        }
        .hero-gradient {
            background: radial-gradient(circle at 10% 20%, rgba(37, 99, 235, 0.05) 0%, transparent 50%),
                        radial-gradient(circle at 90% 80%, rgba(37, 99, 235, 0.05) 0%, transparent 50%);
        }
        .glass-card {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }
        .animate-float {
            animation: float 6s ease-in-out infinite;
        }
        @keyframes float {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
            100% { transform: translateY(0px); }
        }
        .blob {
            position: absolute;
            width: 500px;
            height: 500px;
            background: var(--color-primary-100);
            filter: blur(100px);
            border-radius: 50%;
            z-index: -1;
            opacity: 0.5;
        }
    </style>
</head>
<body class="antialiased bg-white text-slate-900 hero-gradient overflow-x-hidden">
    
    <!-- Background Decor -->
    <div class="blob -top-40 -left-40"></div>
    <div class="blob top-1/2 -right-40" style="background: rgba(59, 130, 246, 0.1);"></div>

    <!-- Header -->
    <header class="fixed top-0 left-0 right-0 z-50 transition-all duration-300" id="main-header">
        <div class="container mx-auto px-6 py-4 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <div class="w-10 h-10 bg-primary-600 rounded-xl flex items-center justify-center shadow-lg shadow-primary-500/30">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                </div>
                <span class="text-xl md:text-2xl font-bold font-outfit tracking-tight text-slate-900 uppercase">AI<span class="text-primary-600">AGEN</span></span>
            </div>

            <nav class="hidden md:flex items-center gap-8">
                <a href="#features" class="text-sm font-medium text-slate-600 hover:text-primary-600 transition-colors">Fitur</a>
                <a href="#how-it-works" class="text-sm font-medium text-slate-600 hover:text-primary-600 transition-colors">Cara Kerja</a>
                <a href="#" class="text-sm font-medium text-slate-600 hover:text-primary-600 transition-colors">Harga</a>
            </nav>

            <div class="flex items-center gap-3">
                @if (Route::has('login'))
                    @auth
                        <a href="{{ url('/dashboard') }}" class="btn btn-primary btn-md shadow-lg shadow-primary-600/20">
                            Dashboard
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="text-sm font-semibold text-slate-700 hover:text-primary-600 px-4 py-2">
                            Masuk
                        </a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="btn btn-primary btn-md shadow-lg shadow-primary-600/20">
                                Daftar Sekarang
                            </a>
                        @endif
                    @endauth
                @endif
            </div>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="pt-32 pb-20 lg:pt-48 lg:pb-32 overflow-hidden">
        <div class="container mx-auto px-6 grid lg:grid-cols-2 gap-12 items-center">
            <div class="space-y-8 relative z-10">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-primary-50 border border-primary-100 text-primary-700 text-xs font-bold uppercase tracking-wider">
                    <span class="relative flex h-2 w-2">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-primary-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-primary-500"></span>
                    </span>
                    AI Agent Terintegrasi WhatsApp
                </div>
                <h1 class="text-4xl md:text-5xl lg:text-7xl font-bold font-outfit leading-[1.2] lg:leading-[1.1] text-slate-900 tracking-tight">
                    Otomatisasi Chat <br>
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-primary-600 to-indigo-600">Tanpa Batas</span>
                </h1>
                <p class="text-lg md:text-xl text-slate-600 leading-relaxed max-w-lg">
                    Tingkatkan efisiensi layanan pelanggan Anda dengan AI Agent cerdas yang mampu menjawab pertanyaan 24/7 langsung melalui WhatsApp.
                </p>
                <div class="flex flex-col sm:flex-row items-center gap-4">
                    <a href="{{ route('register') }}" class="btn btn-primary btn-lg w-full sm:w-auto px-8 py-4 text-lg shadow-xl shadow-primary-600/25">
                        Mulai Gratis Sekarang
                        <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                    </a>
                    <a href="#demo" class="btn btn-secondary btn-lg w-full sm:w-auto px-8 py-4 text-lg border-slate-200">
                        Lihat Demo
                    </a>
                </div>
                <div class="flex items-center gap-4 pt-4 text-sm text-slate-500">
                    <div class="flex -space-x-2">
                        <img class="w-8 h-8 rounded-full border-2 border-white" src="https://ui-avatars.com/api/?name=User+1&background=random" alt="">
                        <img class="w-8 h-8 rounded-full border-2 border-white" src="https://ui-avatars.com/api/?name=User+2&background=random" alt="">
                        <img class="w-8 h-8 rounded-full border-2 border-white" src="https://ui-avatars.com/api/?name=User+3&background=random" alt="">
                    </div>
                    <span>Bergabung dengan 500+ bisnis cerdas lainnya</span>
                </div>
            </div>
            
            <div class="relative lg:h-[600px] flex items-center justify-center">
                <div class="absolute inset-0 bg-primary-600/5 rounded-3xl -rotate-3 scale-95 blur-3xl"></div>
                <div class="relative z-10 animate-float">
                    <img src="{{ asset('aiagen_hero_illustration_1778125640694.png') }}" alt="AI Agent Illustration" class="max-w-full h-auto rounded-3xl shadow-2xl border border-white/50">
                    
                    <!-- Floating Badge -->
                    <div class="absolute -bottom-6 -left-6 glass-card p-4 rounded-2xl shadow-xl flex items-center gap-3 animate-bounce" style="animation-duration: 4s;">
                        <div class="w-12 h-12 bg-green-500 rounded-full flex items-center justify-center text-white">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                        </div>
                        <div>
                            <div class="text-xs font-bold text-slate-400 uppercase">Status</div>
                            <div class="text-sm font-bold text-slate-900">AI Menjawab...</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="py-24 bg-slate-50">
        <div class="container mx-auto px-6">
            <div class="text-center max-w-3xl mx-auto mb-16 space-y-4">
                <h2 class="text-sm font-bold text-primary-600 uppercase tracking-widest">Kenapa Memilih AIAGEN?</h2>
                <h3 class="text-3xl md:text-4xl font-bold font-outfit text-slate-900">Solusi Modern Layanan Pelanggan</h3>
                <p class="text-slate-600 text-lg">Kami mengintegrasikan teknologi AI terbaru dengan platform chat paling populer di dunia.</p>
            </div>

            <div class="grid md:grid-cols-3 gap-8 items-stretch">
                <!-- Feature 1 -->
                <div class="bg-white p-8 rounded-3xl border border-slate-100 shadow-sm hover:shadow-xl transition-all group flex flex-col h-full">
                    <div class="w-14 h-14 bg-primary-50 rounded-2xl flex items-center justify-center text-primary-600 mb-6 group-hover:bg-primary-600 group-hover:text-white transition-colors shrink-0">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    </div>
                    <h4 class="text-xl font-bold mb-3 text-slate-900">Respon Instan</h4>
                    <p class="text-slate-600 leading-relaxed flex-grow">AI kami menjawab pesan pelanggan dalam hitungan detik, memberikan kepuasan maksimal tanpa menunggu lama.</p>
                </div>

                <!-- Feature 2 -->
                <div class="bg-white p-8 rounded-3xl border border-slate-100 shadow-sm hover:shadow-xl transition-all group flex flex-col h-full">
                    <div class="w-14 h-14 bg-indigo-50 rounded-2xl flex items-center justify-center text-indigo-600 mb-6 group-hover:bg-indigo-600 group-hover:text-white transition-colors shrink-0">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    </div>
                    <h4 class="text-xl font-bold mb-3 text-slate-900">Kepribadian AI</h4>
                    <p class="text-slate-600 leading-relaxed flex-grow">Atur gaya bicara dan kepribadian AI sesuai brand Anda. Bisa formal, santai, atau teknis sesuai kebutuhan bisnis.</p>
                </div>

                <!-- Feature 3 -->
                <div class="bg-white p-8 rounded-3xl border border-slate-100 shadow-sm hover:shadow-xl transition-all group flex flex-col h-full">
                    <div class="w-14 h-14 bg-emerald-50 rounded-2xl flex items-center justify-center text-emerald-600 mb-6 group-hover:bg-emerald-600 group-hover:text-white transition-colors shrink-0">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    </div>
                    <h4 class="text-xl font-bold mb-3 text-slate-900">Aman & Terenkripsi</h4>
                    <p class="text-slate-600 leading-relaxed flex-grow">Data Anda dan pelanggan tetap aman. Kami menggunakan sistem enkripsi tingkat lanjut untuk menjaga privasi setiap chat.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="py-12 border-t border-slate-100 bg-white">
        <div class="container mx-auto px-6 flex flex-col md:flex-row items-center justify-between gap-6">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 bg-primary-600 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
                </div>
                <span class="text-xl font-bold font-outfit tracking-tight text-slate-900 uppercase">AI<span class="text-primary-600">AGEN</span></span>
            </div>
            <p class="text-slate-500 text-sm">
                &copy; 2026 AIAGEN System. All rights reserved. Built with ❤️ for Business Growth.
            </p>
            <div class="flex items-center gap-6">
                <a href="#" class="text-slate-400 hover:text-primary-600 transition-colors">
                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M24 4.557c-.883.392-1.832.656-2.828.775 1.017-.609 1.798-1.574 2.165-2.724-.951.564-2.005.974-3.127 1.195-.897-.957-2.178-1.555-3.594-1.555-3.179 0-5.515 2.966-4.797 6.045-4.091-.205-7.719-2.165-10.148-5.144-1.29 2.213-.669 5.108 1.523 6.574-.806-.026-1.566-.247-2.229-.616-.054 2.281 1.581 4.415 3.949 4.89-.693.188-1.452.232-2.224.084.626 1.956 2.444 3.379 4.6 3.419-2.07 1.623-4.678 2.348-7.29 2.04 2.179 1.397 4.768 2.212 7.548 2.212 9.142 0 14.307-7.721 13.995-14.646.962-.695 1.797-1.562 2.457-2.549z"/></svg>
                </a>
                <a href="#" class="text-slate-400 hover:text-primary-600 transition-colors">
                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M12 0c-6.627 0-12 5.373-12 12s5.373 12 12 12 12-5.373 12-12-5.373-12-12-12zm-2 16h-2v-6h2v6zm-1-6.891c-.607 0-1.1-.496-1.1-1.1s.493-1.1 1.1-1.1 1.1.496 1.1 1.1-.493 1.1-1.1 1.1zm9 6.891h-2v-3.868c0-2.12-2.52-1.957-2.52 0v3.868h-2v-6h2v1.135c.933-1.73 4.52-1.858 4.52 2.016v2.849z"/></svg>
                </a>
            </div>
        </div>
    </footer>

    <script>
        // Header transparency on scroll
        window.addEventListener('scroll', function() {
            const header = document.getElementById('main-header');
            if (window.scrollY > 50) {
                header.classList.add('glass-card', 'shadow-md', 'py-3');
                header.classList.remove('py-4');
            } else {
                header.classList.remove('glass-card', 'shadow-md', 'py-3');
                header.classList.add('py-4');
            }
        });
    </script>
</body>
</html>
