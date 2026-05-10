<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'AIAGEN') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>[x-cloak] { display: none !important; }</style>
    </head>
    <body class="font-sans antialiased">
        <x-skeleton-loader />

        <div class="min-h-screen bg-secondary-200 flex" x-data="{ sidebarOpen: true, mobileSidebar: false }">

            {{-- ═══ SIDEBAR (Desktop) ═══ --}}
            <aside
                :class="sidebarOpen ? 'w-64' : 'w-20'"
                class="hidden lg:flex lg:flex-col bg-primary-900 text-white transition-all duration-300 ease-in-out fixed inset-y-0 left-0 z-30">

                {{-- Logo --}}
                <div class="flex items-center justify-between h-16 px-4 border-b border-primary-800">
                    <a href="{{ route('pengguna.dashboard') }}" class="flex items-center gap-3">
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
                <nav class="flex-1 overflow-y-auto py-4 px-3 space-y-1">
                    <a href="{{ route('pengguna.dashboard') }}"
                       class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors
                              {{ request()->routeIs('pengguna.dashboard') ? 'bg-primary-700 text-white' : 'text-primary-300 hover:bg-primary-800 hover:text-white' }}">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                        <span x-show="sidebarOpen" x-cloak>Dashboard</span>
                    </a>
                    <a href="{{ route('pengguna.ai-agen.index') }}"
                       class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors
                               {{ request()->routeIs('pengguna.ai-agen.index') || request()->is('*/ai-agen*') || request()->is('*/laporan*') ? 'bg-primary-700 text-white' : 'text-primary-300 hover:bg-primary-800 hover:text-white' }}">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        <span x-show="sidebarOpen" x-cloak>AI Agen</span>
                    </a>
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

            {{-- ═══ MOBILE SIDEBAR OVERLAY ═══ --}}
            <div x-show="mobileSidebar" x-cloak class="fixed inset-0 z-[60] lg:hidden" @click="mobileSidebar = false">
                <div class="fixed inset-0 bg-black/50" x-transition.opacity></div>
            </div>
            
            {{-- ═══ MOBILE SIDEBAR DRAWER ═══ --}}
            <aside x-show="mobileSidebar" x-cloak
                   x-transition:enter="transition ease-out duration-300" x-transition:enter-start="-translate-x-full" x-transition:enter-end="translate-x-0"
                   x-transition:leave="transition ease-in duration-200" x-transition:leave-start="translate-x-0" x-transition:leave-end="-translate-x-full"
                   class="fixed inset-y-0 left-0 z-[70] w-64 bg-primary-900 text-white lg:hidden flex flex-col">
                <div class="flex items-center justify-between h-16 px-4 border-b border-primary-800">
                    <a href="{{ route('pengguna.dashboard') }}" class="flex items-center gap-3">
                        <div class="w-8 h-8 bg-primary-500 rounded-lg flex items-center justify-center"><svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20"><path d="M10.394 2.08a1 1 0 00-.788 0l-7 3a1 1 0 000 1.84L5.25 8.051a.999.999 0 01.356-.257l4-1.714a1 1 0 11.788 1.838L7.667 9.088l1.94.831a1 1 0 00.787 0l7-3a1 1 0 000-1.838l-7-3z"/></svg></div>
                        <span class="text-lg font-bold">AIAGEN</span>
                    </a>
                    <button @click="mobileSidebar = false" class="text-primary-400 hover:text-white">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <nav class="flex-1 overflow-y-auto py-4 px-3 space-y-1">
                    <a href="{{ route('pengguna.dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium {{ request()->routeIs('pengguna.dashboard') ? 'bg-primary-700 text-white' : 'text-primary-300 hover:bg-primary-800 hover:text-white' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                        <span>Dashboard</span>
                    </a>
                    <a href="{{ route('pengguna.ai-agen.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium {{ request()->routeIs('pengguna.ai-agen.index') ? 'bg-primary-700 text-white' : 'text-primary-300 hover:bg-primary-800 hover:text-white' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        <span>AI Agen</span>
                    </a>
                    <a href="{{ route('pengguna.profile.edit') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium {{ request()->routeIs('pengguna.profile.edit') ? 'bg-primary-700 text-white' : 'text-primary-300 hover:bg-primary-800 hover:text-white' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        <span>Profil</span>
                    </a>
                </nav>
            </aside>

            {{-- ═══ MOBILE BOTTOM NAV (Floating Pill) ═══ --}}
            <div class="lg:hidden fixed bottom-4 left-4 right-4 z-[100] bg-white/90 backdrop-blur-xl border border-white/50 px-6 py-2.5 flex items-center justify-around shadow-[0_8px_30px_rgb(0,0,0,0.12)] rounded-3xl transition-all duration-500">
                <a href="{{ route('pengguna.dashboard') }}" 
                   class="relative flex flex-col items-center gap-1 transition-all duration-300 group {{ request()->routeIs('pengguna.dashboard') && !request()->is('*/ai-agen*') && !request()->is('*/pengaturan*') ? 'text-primary-600 scale-110 -translate-y-1' : 'text-secondary-400 hover:text-primary-500 hover:scale-105' }}">
                    <div class="p-1.5 rounded-2xl transition-all duration-300 {{ request()->routeIs('pengguna.dashboard') && !request()->is('*/ai-agen*') && !request()->is('*/pengaturan*') ? 'bg-primary-100/50' : 'group-hover:bg-secondary-50' }}">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                    </div>
                    <span class="text-[9px] font-bold tracking-wider opacity-90">BERANDA</span>
                </a>
                
                <button @click="mobileSidebar = !mobileSidebar" 
                        class="relative flex flex-col items-center gap-1 transition-all duration-300 group {{ $mobileSidebar ?? false ? 'text-primary-600 scale-110 -translate-y-1' : 'text-secondary-400 hover:text-primary-500 hover:scale-105' }}">
                    <div class="p-1.5 rounded-2xl transition-all duration-300 group-hover:bg-secondary-50" :class="mobileSidebar ? 'bg-primary-100/50' : ''">
                        <svg class="w-6 h-6 transition-transform duration-300" :class="mobileSidebar ? 'rotate-90' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    </div>
                    <span class="text-[9px] font-bold tracking-wider opacity-90">MENU</span>
                </button>

                <a href="{{ route('pengguna.ai-agen.index') }}" 
                   class="relative flex flex-col items-center gap-1 transition-all duration-300 group {{ request()->is('*/ai-agen*') || request()->is('*/laporan*') ? 'text-primary-600 scale-110 -translate-y-1' : 'text-secondary-400 hover:text-primary-500 hover:scale-105' }}">
                    <div class="p-1.5 rounded-2xl transition-all duration-300 {{ request()->is('*/ai-agen*') || request()->is('*/laporan*') ? 'bg-primary-100/50' : 'group-hover:bg-secondary-50' }}">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    </div>
                    <span class="text-[9px] font-bold tracking-wider opacity-90">AI AGEN</span>
                </a>
                
                <a href="{{ route('pengguna.pengaturan.index') }}" 
                   class="relative flex flex-col items-center gap-1 transition-all duration-300 group {{ request()->is('*/pengaturan*') || request()->routeIs('pengguna.profile.*') ? 'text-primary-600 scale-110 -translate-y-1' : 'text-secondary-400 hover:text-primary-500 hover:scale-105' }}">
                    <div class="p-1.5 rounded-2xl transition-all duration-300 {{ request()->is('*/pengaturan*') || request()->routeIs('pengguna.profile.*') ? 'bg-primary-100/50' : 'group-hover:bg-secondary-50' }}">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    </div>
                    <span class="text-[9px] font-bold tracking-wider opacity-90">SETTING</span>
                </a>
            </div>

            {{-- ═══ MAIN CONTENT ═══ --}}
            <div class="flex-1 flex flex-col transition-all duration-300 min-w-0 overflow-hidden" :class="sidebarOpen ? 'lg:ml-64' : 'lg:ml-20'">

                {{-- Top Bar --}}
                <header class="sticky top-0 z-20 bg-white border-b border-secondary-300 h-16 flex items-center justify-between px-4 sm:px-6 lg:px-8">
                    <div class="flex items-center gap-4">
                        <button @click="mobileSidebar = true" class="lg:hidden text-secondary-600 hover:text-secondary-900">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                        </button>
                        @isset($header)
                            <h1 class="text-lg font-semibold text-secondary-900">{{ $header }}</h1>
                        @endisset
                    </div>

                    <div class="hidden lg:flex items-center gap-2">
                        <a href="{{ route('pengguna.dashboard') }}" class="px-3 py-1.5 rounded-lg text-sm font-semibold transition-colors {{ request()->routeIs('pengguna.dashboard') && !request()->is('*/ai-agen*') && !request()->is('*/pengaturan*') && !request()->is('*/laporan*') ? 'bg-primary-100 text-primary-700' : 'text-secondary-600 hover:bg-secondary-100' }}">
                            Dashboard
                        </a>
                        <a href="{{ route('pengguna.ai-agen.index') }}" class="px-3 py-1.5 rounded-lg text-sm font-semibold transition-colors {{ request()->is('*/ai-agen*') || request()->is('*/laporan*') ? 'bg-primary-100 text-primary-700' : 'text-secondary-600 hover:bg-secondary-100' }}">
                            AI Agen
                        </a>
                        <a href="{{ route('pengguna.pengaturan.index') }}" class="px-3 py-1.5 rounded-lg text-sm font-semibold transition-colors {{ request()->is('*/pengaturan*') || request()->routeIs('pengguna.profile.*') ? 'bg-primary-100 text-primary-700' : 'text-secondary-600 hover:bg-secondary-100' }}">
                            Pengaturan
                        </a>
                        <div class="h-6 w-px bg-secondary-200 mx-2"></div>
                        <span class="hidden sm:inline-block text-sm text-secondary-600 font-medium">{{ Auth::user()->name }}</span>
                        <span class="hidden sm:inline-block text-xs bg-primary-100 text-primary-700 px-2 py-0.5 rounded-full font-bold capitalize">{{ Auth::user()->role }}</span>
                    </div>
                </header>

                {{-- Page Content --}}
                <main class="flex-1 p-4 sm:p-6 lg:p-8 pb-24 lg:pb-8">
                    {{ $slot }}
                </main>
            </div>
        </div>
        @stack('scripts')
    </body>
</html>
