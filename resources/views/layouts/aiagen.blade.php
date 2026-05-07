<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'AIAGEN') }} — AI Agen</title>

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

            {{-- ═══ SIDEBAR ═══ --}}
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
                <nav class="flex-1 overflow-y-auto py-4 px-3 space-y-1">
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
                        <span x-show="sidebarOpen" x-cloak>Knowledge Base</span>
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
                        <span x-show="sidebarOpen" x-cloak>Pesan Tidak Terjawab</span>
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
            <div x-show="mobileSidebar" x-cloak class="fixed inset-0 z-40 lg:hidden" @click="mobileSidebar = false">
                <div class="fixed inset-0 bg-black/50" x-transition.opacity></div>
            </div>
            <aside x-show="mobileSidebar" x-cloak
                   x-transition:enter="transition ease-out duration-300" x-transition:enter-start="-translate-x-full" x-transition:enter-end="translate-x-0"
                   x-transition:leave="transition ease-in duration-200" x-transition:leave-start="translate-x-0" x-transition:leave-end="-translate-x-full"
                   class="fixed inset-y-0 left-0 z-50 w-64 bg-primary-900 text-white lg:hidden">
                <div class="flex items-center justify-between h-16 px-4 border-b border-primary-800">
                    <a href="#" class="flex items-center gap-3">
                        <div class="w-8 h-8 bg-primary-500 rounded-lg flex items-center justify-center"><svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20"><path d="M10.394 2.08a1 1 0 00-.788 0l-7 3a1 1 0 000 1.84L5.25 8.051a.999.999 0 01.356-.257l4-1.714a1 1 0 11.788 1.838L7.667 9.088l1.94.831a1 1 0 00.787 0l7-3a1 1 0 000-1.838l-7-3z"/></svg></div>
                        <span class="text-lg font-bold">AIAGEN</span>
                    </a>
                    <button @click="mobileSidebar = false" class="text-primary-400 hover:text-white">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <nav class="flex-1 overflow-y-auto py-4 px-3 space-y-1">
                    <a href="{{ auth()->user()->isAdmin() ? route('admin.ai-agen.index') : route('pengguna.ai-agen.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-primary-300 hover:bg-primary-800 hover:text-white">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        <span>Dashboard</span>
                    </a>
                    <a href="{{ auth()->user()->isAdmin() ? route('admin.ai-agen.departments.index') : route('pengguna.ai-agen.departments.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-primary-300 hover:bg-primary-800 hover:text-white">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                        <span>Departemen</span>
                    </a>
                    <a href="{{ auth()->user()->isAdmin() ? route('admin.ai-agen.knowledge.index') : route('pengguna.ai-agen.knowledge.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-primary-300 hover:bg-primary-800 hover:text-white">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                        <span>Knowledge Base</span>
                    </a>
                    <a href="{{ auth()->user()->isAdmin() ? route('admin.ai-agen.connections.index') : route('pengguna.ai-agen.connections.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-primary-300 hover:bg-primary-800 hover:text-white">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        <span>Connection</span>
                    </a>
                    <a href="{{ auth()->user()->isAdmin() ? route('admin.ai-agen.unanswered.index') : route('pengguna.ai-agen.unanswered.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium {{ request()->routeIs('*.unanswered.index') ? 'bg-primary-700 text-white' : 'text-primary-300 hover:bg-primary-800 hover:text-white' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                        <span>Pesan Tidak Terjawab</span>
                    </a>
                </nav>
            </aside>

            {{-- ═══ MAIN CONTENT ═══ --}}
            <div class="flex-1 flex flex-col transition-all duration-300" :class="sidebarOpen ? 'lg:ml-64' : 'lg:ml-20'">

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

                    <div class="flex items-center gap-2">
                        <a href="{{ auth()->user()->isAdmin() ? route('admin.dashboard') : route('pengguna.dashboard') }}" 
                           class="px-3 py-1.5 rounded-lg text-sm font-semibold transition-colors {{ request()->routeIs('*.dashboard') && !request()->is('*/ai-agen*') && !request()->is('*/pengaturan*') ? 'bg-primary-100 text-primary-700' : 'text-secondary-600 hover:bg-secondary-100' }}">
                            Dashboard
                        </a>
                        <a href="{{ auth()->user()->isAdmin() ? route('admin.ai-agen.index') : route('pengguna.ai-agen.index') }}" 
                           class="px-3 py-1.5 rounded-lg text-sm font-semibold transition-colors {{ request()->is('*/ai-agen*') ? 'bg-primary-100 text-primary-700' : 'text-secondary-600 hover:bg-secondary-100' }}">
                            AI Agen
                        </a>
                        <a href="{{ auth()->user()->isAdmin() ? route('admin.pengaturan.index') : route('pengguna.pengaturan.index') }}" 
                           class="px-3 py-1.5 rounded-lg text-sm font-semibold transition-colors {{ request()->is('*/pengaturan*') || request()->routeIs('*.profile.*') || request()->routeIs('*.users.*') ? 'bg-primary-100 text-primary-700' : 'text-secondary-600 hover:bg-secondary-100' }}">
                            Pengaturan
                        </a>
                        <div class="h-6 w-px bg-secondary-200 mx-2"></div>
                        <span class="hidden sm:inline-block text-sm text-secondary-600 font-medium">{{ Auth::user()->name }}</span>
                        <span class="hidden sm:inline-block text-xs bg-primary-100 text-primary-700 px-2 py-0.5 rounded-full font-bold capitalize">{{ Auth::user()->role }}</span>
                    </div>
                </header>

                {{-- Page Content --}}
                <main class="flex-1 p-4 sm:p-6 lg:p-8">
                    {{ $slot }}
                </main>
            </div>
        </div>
        @stack('scripts')
    </body>
</html>
