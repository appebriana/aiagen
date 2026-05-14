<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'AIAGEN') }} - AI Agen</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>[x-cloak] { display: none !important; }</style>
    </head>
    <body class="font-sans antialiased">
        <x-skeleton-loader />

        <div class="min-h-screen bg-secondary-200 flex" 
             x-data="{ 
                sidebarOpen: localStorage.getItem('sidebarOpen') === null ? true : localStorage.getItem('sidebarOpen') === 'true', 
                mobileSidebar: false, 
                feedbackOpen: false, 
                feedbackSuccessOpen: false, 
                feedbackMessage: '', 
                feedbackLoading: false 
             }"
             x-init="$watch('sidebarOpen', val => localStorage.setItem('sidebarOpen', val))">

            {{-- ═══ SIDEBAR (Desktop) ═══ --}}
            <aside
                :class="sidebarOpen ? 'w-64' : 'w-20'"
                class="hidden lg:flex lg:flex-col bg-primary-900 text-white transition-all duration-300 ease-in-out fixed inset-y-0 left-0 z-30">

                {{-- Logo --}}
                <div class="flex items-center justify-between h-16 px-4 border-b border-primary-800">
                    <a href="{{ auth()->user()->isAdmin() ? route('admin.dashboard') : route('pengguna.dashboard') }}" class="flex items-center gap-3">
                        <div class="w-8 h-8 bg-primary-500 rounded-lg flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20"><path d="M10.394 2.08a1 1 0 00-.788 0l-7 3a1 1 0 000 1.84L5.25 8.051a.999.999 0 01.356-.257l4-1.714a1 1 0 11.788 1.838L7.667 9.088l1.94.831a1 1 0 00.787 0l7-3a1 1 0 000-1.838l-7-3zM3.31 9.397L5 10.12v4.102a8.969 8.969 0 00-1.05-.174 1 1 0 01-.89-.89 11.115 11.115 0 01.25-3.762zM9.3 16.573A9.026 9.026 0 007 14.935v-3.957l1.818.78a3 3 0 002.364 0l5.508-2.361a11.026 11.026 0 01.25 3.762 1 1 0 01-.89.89 8.968 8.968 0 00-5.35 2.524 1 1 0 01-1.4 0z"/></svg>
                        </div>
                        <span x-show="sidebarOpen" x-cloak x-transition class="text-lg font-bold tracking-wide">AIAGEN</span>
                    </a>
                    <button @click="sidebarOpen = !sidebarOpen" class="text-primary-400 hover:text-white focus:outline-none">
                        <svg x-show="sidebarOpen" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"/></svg>
                        <svg x-show="!sidebarOpen" x-cloak class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7"/></svg>
                    </button>
                </div>

                {{-- Navigation --}}
                <nav class="flex-1 overflow-y-auto py-4 px-3 space-y-1 scrollbar-hide">
                    <p class="px-3 text-xs font-semibold text-primary-400 uppercase tracking-wider mt-6 mb-2" x-show="sidebarOpen" x-cloak>AI Agen</p>
                    
                    <a href="{{ auth()->user()->isAdmin() ? route('admin.ai-agen.index') : route('pengguna.ai-agen.index') }}"
                       class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors
                              {{ request()->routeIs('*.ai-agen.index') ? 'bg-primary-700 text-white' : 'text-primary-300 hover:bg-primary-800 hover:text-white' }}">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        <span x-show="sidebarOpen" x-cloak>Dashboard</span>
                    </a>

                    <a href="{{ auth()->user()->isAdmin() ? route('admin.ai-agen.departments.index') : route('pengguna.ai-agen.departments.index') }}"
                       class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors
                              {{ request()->routeIs('*.ai-agen.departments.index') ? 'bg-primary-700 text-white' : 'text-primary-300 hover:bg-primary-800 hover:text-white' }}">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                        <span x-show="sidebarOpen" x-cloak>Departemen</span>
                    </a>

                    <a href="{{ auth()->user()->isAdmin() ? route('admin.ai-agen.knowledge.index') : route('pengguna.ai-agen.knowledge.index') }}"
                       class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors
                              {{ request()->routeIs('*.ai-agen.knowledge.index') ? 'bg-primary-700 text-white' : 'text-primary-300 hover:bg-primary-800 hover:text-white' }}">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                        <span x-show="sidebarOpen" x-cloak>Knowledge (File)</span>
                    </a>

                    <a href="{{ auth()->user()->isAdmin() ? route('admin.ai-agen.knowledge-base.index') : route('pengguna.ai-agen.knowledge-base.index') }}"
                       class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors
                              {{ request()->routeIs('*.knowledge-base.*') ? 'bg-primary-700 text-white' : 'text-primary-300 hover:bg-primary-800 hover:text-white' }}">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg>
                        <span x-show="sidebarOpen" x-cloak>Memory Otak AI</span>
                    </a>

                    <a href="{{ auth()->user()->isAdmin() ? route('admin.ai-agen.connections.index') : route('pengguna.ai-agen.connections.index') }}"
                       class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors
                                {{ request()->routeIs('*.ai-agen.connections.index') ? 'bg-primary-700 text-white' : 'text-primary-300 hover:bg-primary-800 hover:text-white' }}">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        <span x-show="sidebarOpen" x-cloak>Connection</span>
                    </a>

                    <a href="{{ auth()->user()->isAdmin() ? route('admin.ai-agen.unanswered.index') : route('pengguna.ai-agen.unanswered.index') }}"
                       class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors
                                 {{ request()->routeIs('*.unanswered.*') ? 'bg-primary-700 text-white' : 'text-primary-300 hover:bg-primary-800 hover:text-white' }}">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                        <span x-show="sidebarOpen" x-cloak>Pertanyaan Tidak Terjawab</span>
                    </a>

                    {{-- Laporan Menu --}}
                    <div x-data="{ isLaporanMenuOpen: {{ request()->routeIs('*.laporan.*') ? 'true' : 'false' }} }">
                        <button @click="isLaporanMenuOpen = !isLaporanMenuOpen"
                                class="w-full flex items-center justify-between px-3 py-2.5 rounded-lg text-sm font-medium transition-colors
                                       {{ request()->routeIs('*.laporan.*') ? 'bg-primary-800 text-white' : 'text-primary-300 hover:bg-primary-800 hover:text-white' }}">
                            <div class="flex items-center gap-3">
                                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                <span x-show="sidebarOpen" x-cloak>Laporan</span>
                            </div>
                            <svg x-show="sidebarOpen" x-cloak :class="isLaporanMenuOpen ? 'rotate-180' : ''" class="w-4 h-4 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div x-show="isLaporanMenuOpen && sidebarOpen" x-cloak x-transition class="mt-1 ml-4 pl-4 border-l border-primary-700 space-y-0.5">
                            @php $laporanPrefix = auth()->user()->isAdmin() ? 'admin' : 'pengguna'; @endphp
                            <p class="px-3 py-1.5 text-[10px] font-bold text-primary-500 uppercase tracking-wider">Laporan Interaksi</p>
                            <a href="{{ route($laporanPrefix . '.laporan.interaksi.wa') }}"
                               class="flex items-center gap-2 px-3 py-2 rounded-md text-xs font-medium {{ request()->routeIs('*.laporan.interaksi.wa*') ? 'text-white bg-primary-700' : 'text-primary-400 hover:text-white hover:bg-primary-800' }}">
                                <svg class="w-4 h-4 flex-shrink-0" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                                WhatsApp
                            </a>
                            <a href="{{ route($laporanPrefix . '.laporan.interaksi.ig') }}"
                               class="flex items-center gap-2 px-3 py-2 rounded-md text-xs font-medium {{ request()->routeIs('*.laporan.interaksi.ig') ? 'text-white bg-primary-700' : 'text-primary-400 hover:text-white hover:bg-primary-800' }}">
                                <svg class="w-4 h-4 flex-shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"/><circle cx="12" cy="12" r="4.5"/><circle cx="17.5" cy="6.5" r="1" fill="currentColor" stroke="none"/></svg>
                                Instagram
                                <span class="ml-auto text-[9px] px-1.5 py-0.5 bg-amber-500/20 text-amber-400 rounded font-bold">Soon</span>
                            </a>
                            <a href="{{ route($laporanPrefix . '.laporan.interaksi.telegram') }}"
                               class="flex items-center gap-2 px-3 py-2 rounded-md text-xs font-medium {{ request()->routeIs('*.laporan.interaksi.telegram') ? 'text-white bg-primary-700' : 'text-primary-400 hover:text-white hover:bg-primary-800' }}">
                                <svg class="w-4 h-4 flex-shrink-0" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm4.64 6.8c-.15 1.58-.8 5.42-1.13 7.19-.14.75-.42 1-.68 1.03-.58.05-1.02-.38-1.58-.75-.88-.58-1.38-.94-2.23-1.5-.99-.65-.35-1.01.22-1.59.15-.15 2.71-2.48 2.76-2.69a.2.2 0 00-.07-.2c-.08-.06-.19-.04-.27-.02-.12.03-1.99 1.27-5.62 3.72-.53.36-1.01.54-1.44.53-.47-.01-1.38-.27-2.06-.49-.83-.27-1.49-.42-1.43-.88.03-.24.37-.49 1.02-.75 3.99-1.73 6.65-2.88 7.99-3.44 3.81-1.58 4.6-1.86 5.12-1.87.11 0 .37.03.54.17.14.12.18.28.2.46-.01.06.01.24 0 .38z"/></svg>
                                Telegram
                                <span class="ml-auto text-[9px] px-1.5 py-0.5 bg-amber-500/20 text-amber-400 rounded font-bold">Soon</span>
                            </a>
                            <p class="px-3 py-1.5 text-[10px] font-bold text-primary-500 uppercase tracking-wider mt-2">Kualitas & Kepuasan</p>
                            <a href="{{ route($laporanPrefix . '.laporan.kepuasan') }}"
                               class="flex items-center gap-2 px-3 py-2 rounded-md text-xs font-medium {{ request()->routeIs('*.laporan.kepuasan*') ? 'text-white bg-primary-700' : 'text-primary-400 hover:text-white hover:bg-primary-800' }}">
                                <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                Laporan Kepuasan
                            </a>
                        </div>
                    </div>
                </nav>

                {{-- User Info --}}
                <div class="border-t border-primary-800 p-4">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 bg-primary-600 rounded-full flex items-center justify-center flex-shrink-0">
                            <span class="text-sm font-bold text-white">{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}</span>
                        </div>
                        <div x-show="sidebarOpen" x-cloak class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-white truncate">{{ Auth::user()->name }}</p>
                            <p class="text-xs text-primary-400 truncate">{{ Auth::user()->role }}</p>
                        </div>
                        <form method="POST" action="{{ route('logout') }}" x-show="sidebarOpen" x-cloak>
                            @csrf
                            <button type="submit" class="text-primary-400 hover:text-red-400 transition-colors" title="Logout">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                            </button>
                        </form>
                    </div>
                </div>
            </aside>

            {{-- ═══ MOBILE FLOATING BOTTOM NAV ═══ --}}
            <div class="lg:hidden fixed bottom-6 left-6 right-6 z-[100] bg-white/80 backdrop-blur-2xl border border-white/50 rounded-2xl shadow-[0_15px_35px_rgba(0,0,0,0.1)] px-3 py-2">
                <div class="flex items-center justify-around">
                    @php 
                        $isAdmin = auth()->user()->isAdmin();
                        $routePrefix = $isAdmin ? 'admin' : 'pengguna';
                    @endphp
                    <a href="{{ route($routePrefix . '.dashboard') }}" 
                       class="flex flex-col items-center gap-0.5 py-1 px-2 rounded-2xl transition-all {{ request()->routeIs('*.dashboard') && !request()->is('*/ai-agen*') ? 'text-primary-600 bg-primary-50' : 'text-secondary-400' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                        <span class="text-[9px] font-bold">Beranda</span>
                    </a>

                    <a href="{{ route($routePrefix . '.ai-agen.index') }}" 
                       class="flex flex-col items-center gap-0.5 py-1 px-2 rounded-2xl transition-all {{ (request()->is('*/ai-agen*') || request()->is('*/laporan*')) && !request()->is('*/cms*') ? 'text-primary-600 bg-primary-50' : 'text-secondary-400' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        <span class="text-[9px] font-bold">AI Agen</span>
                    </a>

                    <a href="{{ route($routePrefix . '.cms.index') }}" 
                       class="flex flex-col items-center gap-0.5 py-1 px-2 rounded-2xl transition-all {{ request()->is('*/cms*') ? 'text-primary-600 bg-primary-50' : 'text-secondary-400' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
                        <span class="text-[9px] font-bold">CMS</span>
                    </a>

                    <button @click="mobileSidebar = !mobileSidebar" 
                            class="flex flex-col items-center gap-0.5 py-1 px-2 rounded-2xl transition-all"
                            :class="mobileSidebar ? 'text-primary-600 bg-primary-50' : 'text-secondary-400'">
                        <div class="relative w-5 h-5">
                            <svg class="w-5 h-5 absolute inset-0 transition-all duration-500" :class="mobileSidebar ? 'opacity-0 rotate-90 scale-75' : 'opacity-100 rotate-0 scale-100'" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/></svg>
                            <svg class="w-5 h-5 absolute inset-0 transition-all duration-300" :class="mobileSidebar ? 'opacity-100 rotate-0 scale-100' : 'opacity-0 -rotate-90 scale-75'" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                        </div>
                        <span class="text-[9px] font-bold" x-text="mobileSidebar ? 'Tutup' : 'Menu'"></span>
                    </button>

                    <a href="{{ route($routePrefix . '.pengaturan.index') }}" 
                       class="flex flex-col items-center gap-0.5 py-1 px-2 rounded-2xl transition-all {{ request()->is('*/pengaturan*') || request()->routeIs('*.profile.*') || request()->routeIs('*.users.*') ? 'text-primary-600 bg-primary-50' : 'text-secondary-400' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        <span class="text-[9px] font-bold">Setting</span>
                    </a>

                    @if(auth()->user()->isAdmin())
                        <a href="{{ route('admin.feedback.index') }}" 
                           class="flex flex-col items-center gap-0.5 py-1 px-2 rounded-2xl transition-all {{ request()->routeIs('admin.feedback.index') ? 'text-primary-600 bg-primary-50' : 'text-secondary-400' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/></svg>
                            <span class="text-[9px] font-bold">Feedback</span>
                        </a>
                    @else
                        <button @click="feedbackOpen = true" 
                           class="flex flex-col items-center gap-0.5 py-1 px-2 rounded-2xl transition-all"
                           :class="feedbackOpen ? 'text-primary-600 bg-primary-50' : 'text-secondary-400'">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/></svg>
                            <span class="text-[9px] font-bold">Feedback</span>
                        </button>
                    @endif
                </div>
            </div>

            {{-- Backdrop overlay --}}
            <div x-show="mobileSidebar" x-cloak
                 x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                 x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                 @click="mobileSidebar = false"
                 class="lg:hidden fixed inset-0 z-[90] bg-primary-950/20 backdrop-blur-sm"></div>

            {{-- Aesthetic Left Floating Sidebar --}}
            <div x-show="mobileSidebar" x-cloak
                 x-transition:enter="transition transform ease-out duration-500 cubic-bezier(0.34, 1.56, 0.64, 1)"
                 x-transition:enter-start="-translate-x-full opacity-0"
                 x-transition:enter-end="translate-x-0 opacity-100"
                 x-transition:leave="transition transform ease-in duration-300"
                 x-transition:leave-start="translate-x-0 opacity-100"
                 x-transition:leave-end="-translate-x-full opacity-0"
                 class="lg:hidden fixed top-4 bottom-24 left-4 w-[calc(100vw-2rem)] max-w-[288px] z-[95] overflow-hidden flex flex-col">
                <div class="bg-white/90 backdrop-blur-2xl rounded-2xl shadow-[10px_10px_40px_rgba(0,0,0,0.12)] border border-white/50 flex flex-col h-full">
                    
                    <div class="p-5 overflow-y-auto flex-1 scrollbar-hide">
                        {{-- Vertical Menu Items --}}
                        <div class="flex flex-col gap-2 mb-6">
                            <p class="px-3 text-[10px] font-bold text-primary-500 uppercase tracking-widest mb-1">AI Agen</p>
                            
                            <a href="{{ auth()->user()->isAdmin() ? route('admin.ai-agen.index') : route('pengguna.ai-agen.index') }}"
                               class="flex items-center gap-4 p-3 rounded-2xl transition-all duration-300 active:scale-95 {{ request()->routeIs('*.ai-agen.index') ? 'bg-primary-600 text-white shadow-lg shadow-primary-200' : 'bg-secondary-50 text-secondary-600 hover:bg-secondary-100' }}">
                                <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                <span class="font-bold text-sm">Dashboard</span>
                            </a>

                            <a href="{{ auth()->user()->isAdmin() ? route('admin.ai-agen.departments.index') : route('pengguna.ai-agen.departments.index') }}"
                               class="flex items-center gap-4 p-3 rounded-2xl transition-all duration-300 active:scale-95 {{ request()->routeIs('*.ai-agen.departments.index') ? 'bg-primary-600 text-white shadow-lg shadow-primary-200' : 'bg-secondary-50 text-secondary-600 hover:bg-secondary-100' }}">
                                <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                <span class="font-bold text-sm">Departemen</span>
                            </a>

                            <a href="{{ auth()->user()->isAdmin() ? route('admin.ai-agen.knowledge.index') : route('pengguna.ai-agen.knowledge.index') }}"
                               class="flex items-center gap-4 p-3 rounded-2xl transition-all duration-300 active:scale-95 {{ request()->routeIs('*.ai-agen.knowledge.index') ? 'bg-primary-600 text-white shadow-lg shadow-primary-200' : 'bg-secondary-50 text-secondary-600 hover:bg-secondary-100' }}">
                                <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                                <span class="font-bold text-sm">Knowledge Base</span>
                            </a>

                            <a href="{{ auth()->user()->isAdmin() ? route('admin.ai-agen.knowledge-base.index') : route('pengguna.ai-agen.knowledge-base.index') }}"
                               class="flex items-center gap-4 p-3 rounded-2xl transition-all duration-300 active:scale-95 {{ request()->routeIs('*.knowledge-base.*') ? 'bg-primary-600 text-white shadow-lg shadow-primary-200' : 'bg-secondary-50 text-secondary-600 hover:bg-secondary-100' }}">
                                <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg>
                                <span class="font-bold text-sm">Memory Otak AI</span>
                            </a>

                            <a href="{{ auth()->user()->isAdmin() ? route('admin.ai-agen.connections.index') : route('pengguna.ai-agen.connections.index') }}"
                               class="flex items-center gap-4 p-3 rounded-2xl transition-all duration-300 active:scale-95 {{ request()->routeIs('*.ai-agen.connections.index') ? 'bg-primary-600 text-white shadow-lg shadow-primary-200' : 'bg-secondary-50 text-secondary-600 hover:bg-secondary-100' }}">
                                <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                <span class="font-bold text-sm">Connection</span>
                            </a>

                            <a href="{{ auth()->user()->isAdmin() ? route('admin.ai-agen.unanswered.index') : route('pengguna.ai-agen.unanswered.index') }}"
                               class="flex items-center gap-4 p-3 rounded-2xl transition-all duration-300 active:scale-95 {{ request()->routeIs('*.unanswered.*') ? 'bg-primary-600 text-white shadow-lg shadow-primary-200' : 'bg-secondary-50 text-secondary-600 hover:bg-secondary-100' }}">
                                <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                                <span class="font-bold text-sm">Pertanyaan Tidak Terjawab</span>
                            </a>

                            <div x-data="{ isLaporanOpen: {{ request()->routeIs('*.laporan.*') ? 'true' : 'false' }} }">
                                <button @click="isLaporanOpen = !isLaporanOpen"
                                        class="w-full flex items-center justify-between p-3 rounded-2xl transition-all duration-300 active:scale-95 {{ request()->routeIs('*.laporan.*') ? 'bg-primary-600 text-white shadow-lg shadow-primary-200' : 'bg-secondary-50 text-secondary-600 hover:bg-secondary-100' }}">
                                    <div class="flex items-center gap-4">
                                        <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                        <span class="font-bold text-sm">Laporan</span>
                                    </div>
                                    <svg :class="isLaporanOpen ? 'rotate-180' : ''" class="w-4 h-4 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                </button>
                                <div x-show="isLaporanOpen" x-transition class="mt-2 ml-4 pl-4 border-l-2 border-primary-100 space-y-1">
                                    <a href="{{ route($routePrefix . '.laporan.interaksi.wa') }}"
                                       class="block py-2 text-sm font-medium {{ request()->routeIs('*.laporan.interaksi.wa') ? 'text-primary-600' : 'text-secondary-500' }}">
                                        WhatsApp
                                    </a>
                                    <a href="{{ route($routePrefix . '.laporan.interaksi.ig') }}"
                                       class="block py-2 text-sm font-medium {{ request()->routeIs('*.laporan.interaksi.ig') ? 'text-primary-600' : 'text-secondary-500' }}">
                                        Instagram <span class="text-[10px] px-1.5 py-0.5 bg-amber-100 text-amber-600 rounded-full ml-1">Soon</span>
                                    </a>
                                    <a href="{{ route($routePrefix . '.laporan.interaksi.telegram') }}"
                                       class="block py-2 text-sm font-medium {{ request()->routeIs('*.laporan.interaksi.telegram') ? 'text-primary-600' : 'text-secondary-500' }}">
                                        Telegram <span class="text-[10px] px-1.5 py-0.5 bg-amber-100 text-amber-600 rounded-full ml-1">Soon</span>
                                    </a>
                                    <a href="{{ route($routePrefix . '.laporan.kepuasan') }}"
                                       class="block py-2 text-sm font-medium {{ request()->routeIs('*.laporan.kepuasan*') ? 'text-primary-600' : 'text-secondary-500' }}">
                                        Laporan Kepuasan
                                    </a>
                                </div>
                            </div>

                        </div>

                        {{-- Premium User Card --}}
                        <div class="bg-gradient-to-br from-primary-600 via-primary-500 to-primary-700 rounded-2xl p-4 flex items-center justify-between shadow-xl shadow-primary-200/50 mt-auto">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-white/20 backdrop-blur-md rounded-xl flex items-center justify-center text-white font-bold border border-white/30 shadow-inner">
                                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                </div>
                                <div class="flex flex-col">
                                    <span class="text-sm font-bold text-white tracking-wide truncate max-w-[100px]">{{ Auth::user()->name }}</span>
                                    <span class="text-[10px] text-primary-100 font-medium uppercase tracking-widest">{{ Auth::user()->role }}</span>
                                </div>
                            </div>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="w-10 h-10 bg-white/10 hover:bg-white/20 rounded-xl flex items-center justify-center transition-all active:scale-95 text-white border border-white/20 shadow-lg">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ═══ MAIN CONTENT ═══ --}}
            <div class="flex-1 flex flex-col transition-all duration-300 min-w-0" :class="sidebarOpen ? 'lg:ml-64' : 'lg:ml-20'">

                {{-- Top Bar --}}
                <header class="sticky top-0 z-20 bg-white/80 backdrop-blur-md border-b border-secondary-300 h-16 flex items-center justify-between px-4 sm:px-6 lg:px-8 shadow-sm">
                    <div class="flex items-center gap-4">
                        @isset($header)
                            <h1 class="text-lg font-semibold text-secondary-900 uppercase tracking-tight">{{ $header }}</h1>
                        @endisset
                    </div>

                    <div class="hidden lg:flex items-center gap-2">
                        <a href="{{ auth()->user()->isAdmin() ? route('admin.dashboard') : route('pengguna.dashboard') }}" 
                           class="px-3 py-1.5 rounded-lg text-sm font-semibold transition-colors flex items-center gap-2 {{ request()->routeIs('*.dashboard') && !request()->is('*/ai-agen*') && !request()->is('*/pengaturan*') && !request()->is('*/laporan*') ? 'bg-primary-100 text-primary-700' : 'text-secondary-600 hover:bg-secondary-100' }}">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                            Dashboard
                        </a>
                        <a href="{{ auth()->user()->isAdmin() ? route('admin.ai-agen.index') : route('pengguna.ai-agen.index') }}" 
                           class="px-3 py-1.5 rounded-lg text-sm font-semibold transition-colors flex items-center gap-2 {{ (request()->is('*/ai-agen*') || request()->is('*/laporan*')) && !request()->is('*/cms*') ? 'bg-primary-100 text-primary-700' : 'text-secondary-600 hover:bg-secondary-100' }}">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                            AI Agen
                        </a>
                        <a href="{{ auth()->user()->isAdmin() ? route('admin.cms.index') : route('pengguna.cms.index') }}" 
                           class="px-3 py-1.5 rounded-lg text-sm font-semibold transition-colors flex items-center gap-2 {{ request()->is('*/cms*') ? 'bg-primary-100 text-primary-700' : 'text-secondary-600 hover:bg-secondary-100' }}">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
                            CMS
                        </a>
                        <a href="{{ auth()->user()->isAdmin() ? route('admin.pengaturan.index') : route('pengguna.pengaturan.index') }}" 
                           class="px-3 py-1.5 rounded-lg text-sm font-semibold transition-colors flex items-center gap-2 {{ request()->is('*/pengaturan*') || request()->routeIs('*.profile.*') || request()->routeIs('*.users.*') ? 'bg-primary-100 text-primary-700' : 'text-secondary-600 hover:bg-secondary-100' }}">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            Pengaturan
                        </a>
                        @if(auth()->user()->isAdmin())
                            <a href="{{ route('admin.feedback.index') }}" class="px-3 py-1.5 rounded-lg text-sm font-semibold transition-colors {{ request()->routeIs('admin.feedback.index') ? 'bg-primary-100 text-primary-700' : 'text-secondary-600 hover:bg-secondary-100' }} flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/></svg>
                                Feedback
                            </a>
                        @else
                            <button @click="feedbackOpen = true" class="px-3 py-1.5 rounded-lg text-sm font-semibold text-secondary-600 hover:bg-secondary-100 transition-colors flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/></svg>
                                Feedback
                            </button>
                        @endif
                        <div class="h-6 w-px bg-secondary-200 mx-2"></div>
                        <span class="hidden sm:inline-block text-sm text-secondary-600 font-medium">{{ Auth::user()->name }}</span>
                        <span class="hidden sm:inline-block text-xs bg-primary-100 text-primary-700 px-2 py-0.5 rounded-full font-bold capitalize">{{ Auth::user()->role }}</span>
                    </div>
                </header>

                {{-- Page Content --}}
                <main class="flex-1 p-4 sm:p-6 lg:p-8 pb-24 lg:pb-8">
                    {{ $slot }}
                </main>

                {{-- Feedback Modal --}}
                <div x-show="feedbackOpen" x-cloak 
                     class="fixed inset-0 z-[150] flex items-center justify-center p-4 sm:p-6"
                     x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
                    
                    <div @click="feedbackOpen = false" class="absolute inset-0 bg-secondary-900/60 backdrop-blur-sm"></div>
                    
                    <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-sm sm:max-w-md overflow-hidden"
                         x-transition:enter="transition ease-out duration-300 transform" x-transition:enter-start="scale-95 translate-y-4" x-transition:enter-end="scale-100 translate-y-0">
                        
                        <div class="p-6">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-lg font-bold text-secondary-900">Kirim Masukan</h3>
                                <button @click="feedbackOpen = false" class="text-secondary-400 hover:text-secondary-600">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                </button>
                            </div>

                            <form @submit.prevent="
                                feedbackLoading = true;
                                fetch('{{ route('feedback.store') }}', {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                    },
                                    body: JSON.stringify({ message: feedbackMessage })
                                })
                                .then(res => res.json())
                                .then(data => {
                                    if(data.success) {
                                        feedbackMessage = '';
                                        feedbackOpen = false;
                                        feedbackSuccessOpen = true;
                                    }
                                })
                                .catch(err => {
                                    alert('Terjadi kesalahan saat mengirim masukan. Silakan coba lagi.');
                                    console.error(err);
                                })
                                .finally(() => feedbackLoading = false);
                            ">
                                <textarea x-model="feedbackMessage" required
                                          placeholder="Jika Anda mengalami masalah atau fungsi yang kurang sempurna selama penggunaan, silakan jelaskan masalah atau kebutuhan Anda kepada kami secara detail, kami akan berusaha semaksimal mungkin untuk menyelesaikannya atau meningkatkannya untuk Anda."
                                          class="w-full h-32 rounded-xl border-secondary-200 focus:border-primary-500 focus:ring-4 focus:ring-primary-500/10 text-sm resize-none mb-4"
                                ></textarea>

                                <p class="text-[11px] text-secondary-500 mb-6 leading-relaxed italic">
                                    Kami memberikan perhatian khusus pada masukan kebutuhan Anda, dan kami melakukan peninjauan kebutuhan mingguan secara rutin. Semoga kami dapat membantu Anda dengan lebih baik.
                                </p>

                                <button type="submit" 
                                        :disabled="feedbackLoading"
                                        class="w-full bg-primary-600 hover:bg-primary-700 text-white font-bold py-2.5 rounded-xl transition-all active:scale-95 flex items-center justify-center gap-2">
                                    <span x-show="!feedbackLoading">Kirim Masukan</span>
                                    <span x-show="feedbackLoading" x-cloak class="flex items-center gap-2">
                                        <svg class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                        Mengirim...
                                    </span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            {{-- --- Success Modal --- --}}
            <div x-show="feedbackSuccessOpen" x-cloak class="fixed inset-0 z-[200] flex items-center justify-center px-4">
                <div x-show="feedbackSuccessOpen" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" class="absolute inset-0 bg-primary-950/40 backdrop-blur-sm" @click="feedbackSuccessOpen = false"></div>
                <div x-show="feedbackSuccessOpen" x-transition:enter="transition ease-out duration-300 transform" x-transition:enter-start="scale-95 translate-y-4" x-transition:enter-end="scale-100 translate-y-0" class="relative bg-white w-full max-w-sm rounded-2xl shadow-2xl overflow-hidden text-center p-8">
                    <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6">
                        <svg class="w-10 h-10 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    </div>
                    <h3 class="text-xl font-black text-secondary-900 mb-2">Terima Kasih!</h3>
                    <p class="text-secondary-600 mb-8">Terima kasih atas feedback Anda! Kami akan meninjau saran Anda segera.</p>
                    <button @click="feedbackSuccessOpen = false" class="w-full bg-primary-600 hover:bg-primary-700 text-white font-bold py-3 rounded-xl transition-all active:scale-95 shadow-lg shadow-primary-200">
                        Kembali
                    </button>
                </div>
            </div>
        </div>
        {{-- Version Update Modal --}}
        <x-version-update-modal version="1.1.0" />

        @stack('scripts')
    </body>
</html>
