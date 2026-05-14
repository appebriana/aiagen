<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'AIAGEN') }} - CMS Omnichannel</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>[x-cloak] { display: none !important; }</style>
    </head>
    <body class="font-sans antialiased">
        <x-skeleton-loader />

        <div class="h-screen bg-secondary-200 flex overflow-hidden" x-data="{ sidebarOpen: true, mobileSidebar: false, feedbackOpen: false, feedbackSuccessOpen: false, feedbackMessage: '', feedbackLoading: false }">

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

                {{-- Navigation (Departments for CMS) --}}
                <nav class="flex-1 overflow-y-auto py-4 px-3 space-y-1 scrollbar-hide" x-data="{ searchDept: '' }">
                    @if(auth()->user()->isAdmin())
                        <div class="px-3 mb-6" x-show="sidebarOpen" x-cloak>
                            <label class="text-[10px] font-bold text-primary-400 uppercase tracking-widest block mb-2">Pilih Pengguna</label>
                            <select onchange="window.location.href='?user_id=' + this.value" 
                                    class="w-full bg-primary-800 border-none rounded-lg text-xs text-white focus:ring-1 focus:ring-primary-500 py-2 transition-all">
                                <option value="">-- Semua Pengguna --</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ ($selectedUserId == $user->id) ? 'selected' : '' }}>
                                        {{ $user->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @endif

                    <p class="px-3 text-xs font-semibold text-primary-400 uppercase tracking-wider mt-6 mb-2" x-show="sidebarOpen" x-cloak>Departemen CMS</p>
                    
                    {{-- Search Department --}}
                    <div class="px-3 mb-4" x-show="sidebarOpen" x-cloak>
                        <div class="relative">
                            <input type="text" x-model="searchDept" placeholder="Cari departemen..." 
                                   class="w-full pl-8 pr-3 py-1.5 bg-primary-800 border-none rounded-lg text-xs text-white placeholder-primary-400 focus:ring-1 focus:ring-primary-500 transition-all">
                            <svg class="w-3.5 h-3.5 text-primary-400 absolute left-2.5 top-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        </div>
                    </div>

                    <div class="space-y-1">
                        @forelse($departments as $dept)
                            <a href="?dept={{ $dept->id }}&user_id={{ $selectedUserId }}"
                               x-show="searchDept === '' || '{{ strtolower($dept->name) }}'.includes(searchDept.toLowerCase())"
                               class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors
                                      {{ ($activeDepartment && $activeDepartment->id == $dept->id) ? 'bg-primary-700 text-white shadow-lg shadow-primary-900/50' : 'text-primary-300 hover:bg-primary-800 hover:text-white' }}">
                                <div class="w-2 h-2 rounded-full {{ $dept->is_active ? 'bg-green-400' : 'bg-secondary-500' }}"></div>
                                <span x-show="sidebarOpen" x-cloak class="truncate">{{ $dept->name }}</span>
                            </a>
                        @empty
                            @if(auth()->user()->isAdmin() && !$selectedUserId)
                                <div class="px-3 py-4 bg-primary-800/50 rounded-xl border border-primary-700 text-center" x-show="sidebarOpen" x-cloak>
                                    <p class="text-[10px] text-primary-300 italic">Silakan pilih pengguna terlebih dahulu untuk melihat departemen.</p>
                                </div>
                            @else
                                <div class="px-3 py-4 text-center" x-show="sidebarOpen" x-cloak>
                                    <p class="text-[10px] text-primary-400">Tidak ada departemen.</p>
                                </div>
                            @endif
                        @endforelse
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

            {{-- ═══ MAIN CONTENT ═══ --}}
            <div class="flex-1 flex flex-col transition-all duration-300 min-w-0 h-screen overflow-hidden" :class="sidebarOpen ? 'lg:ml-64' : 'lg:ml-20'">

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
                        <div class="h-6 w-px bg-secondary-200 mx-2"></div>
                        <span class="hidden sm:inline-block text-sm text-secondary-600 font-medium">{{ Auth::user()->name }}</span>
                        <span class="hidden sm:inline-block text-xs bg-primary-100 text-primary-700 px-2 py-0.5 rounded-full font-bold capitalize">{{ Auth::user()->role }}</span>
                    </div>
                </header>

                {{-- Page Content --}}
                <main class="flex-1 flex flex-col overflow-hidden">
                    {{ $slot }}
                </main>
            </div>
        </div>

        <x-version-update-modal version="1.1.0" />

        @stack('scripts')
    </body>
</html>
