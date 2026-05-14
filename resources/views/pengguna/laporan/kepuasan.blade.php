<x-aiagen-layout>
    <x-slot name="header">
        Laporan Kepuasan Pelanggan (CSAT)
    </x-slot>

    @php
        $isAdmin = auth()->user()->isAdmin();
        $routePrefix = $isAdmin ? 'admin' : 'pengguna';
    @endphp

    <div class="space-y-6">
        {{-- Admin: User Selector --}}
        @if($isAdmin)
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-secondary-200">
            <div class="flex flex-col md:flex-row md:items-center gap-4">
                <div class="flex-1">
                    <h2 class="text-lg font-bold text-secondary-900 flex items-center gap-2">
                        <svg class="w-5 h-5 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        Pilih Akun Pengguna
                    </h2>
                    <p class="text-sm text-secondary-500 mt-1">Pilih pengguna untuk melihat laporan kepuasan pelanggan milik mereka.</p>
                </div>
                <form method="GET" action="{{ route($routePrefix . '.laporan.kepuasan') }}" class="flex items-center gap-3">
                    <input type="hidden" name="range" value="{{ $range }}">
                    <select name="user_id" onchange="this.form.submit()"
                            class="min-w-[250px] px-4 py-2.5 bg-white border border-secondary-300 rounded-xl text-sm font-medium text-secondary-700 focus:ring-2 focus:ring-primary-500 focus:border-primary-500 shadow-sm">
                        <option value="">— Pilih Pengguna —</option>
                        @foreach($penggunaUsers as $pUser)
                            <option value="{{ $pUser->id }}" {{ $selectedUser && $selectedUser->id == $pUser->id ? 'selected' : '' }}>
                                {{ $pUser->name }} ({{ $pUser->username }})
                            </option>
                        @endforeach
                    </select>
                </form>
            </div>
        </div>
        @endif

        @if(!$isAdmin || $selectedUser)
        {{-- Statistics Cards --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-secondary-200">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-amber-50 rounded-xl flex items-center justify-center text-amber-500 border border-amber-100">
                        <svg class="w-6 h-6 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                    </div>
                    <div>
                        <p class="text-sm text-secondary-500 font-medium uppercase tracking-wider">Skor Rata-rata</p>
                        <h3 class="text-2xl font-black text-secondary-900">{{ number_format($stats['avg'], 2) }} <span class="text-secondary-400 text-sm">/ 5.0</span></h3>
                    </div>
                </div>
            </div>
            
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-secondary-200">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-primary-50 rounded-xl flex items-center justify-center text-primary-500 border border-primary-100">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z"/></svg>
                    </div>
                    <div>
                        <p class="text-sm text-secondary-500 font-medium uppercase tracking-wider">Total Penilaian</p>
                        <h3 class="text-2xl font-black text-secondary-900">{{ number_format($stats['total']) }} <span class="text-secondary-400 text-sm">Respon</span></h3>
                    </div>
                </div>
            </div>

            <div class="bg-white p-6 rounded-2xl shadow-sm border border-secondary-200 flex flex-col justify-center">
                <div class="space-y-2">
                    @foreach([5,4,3,2,1] as $star)
                        @php $percent = $stats['total'] > 0 ? ($stats['distribution'][$star] / $stats['total']) * 100 : 0; @endphp
                        <div class="flex items-center gap-2">
                            <span class="text-[10px] font-bold text-secondary-500 w-4">{{ $star }} ★</span>
                            <div class="flex-1 h-1.5 bg-secondary-100 rounded-full overflow-hidden">
                                <div class="h-full bg-amber-400" style="width: {{ $percent }}%"></div>
                            </div>
                            <span class="text-[10px] text-secondary-400 w-8 text-right">{{ $stats['distribution'][$star] }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Filters & Exports --}}
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white p-6 rounded-2xl shadow-sm border border-secondary-200">
            <div>
                <h2 class="text-lg font-bold text-secondary-900">Daftar Penilaian Terbaru</h2>
                <p class="text-xs text-secondary-500 mt-0.5">Urutan penilaian dari yang terbaru ke terlama.</p>
            </div>
            
            <div class="flex flex-wrap items-center gap-3">
                {{-- Rating Filter --}}
                <form method="GET" action="{{ route($routePrefix . '.laporan.kepuasan') }}" class="flex items-center gap-2">
                    @if($isAdmin && $selectedUser) <input type="hidden" name="user_id" value="{{ $selectedUser->id }}"> @endif
                    <input type="hidden" name="range" value="{{ $range }}">
                    <select name="rating" onchange="this.form.submit()"
                            class="px-4 py-2 bg-white border border-secondary-300 rounded-xl text-xs font-bold text-secondary-700 shadow-sm focus:ring-primary-500">
                        <option value="">Semua Rating</option>
                        <option value="5" {{ request('rating') == 5 ? 'selected' : '' }}>5 Bintang ★★★★★</option>
                        <option value="4" {{ request('rating') == 4 ? 'selected' : '' }}>4 Bintang ★★★★</option>
                        <option value="3" {{ request('rating') == 3 ? 'selected' : '' }}>3 Bintang ★★★</option>
                        <option value="2" {{ request('rating') == 2 ? 'selected' : '' }}>2 Bintang ★★</option>
                        <option value="1" {{ request('rating') == 1 ? 'selected' : '' }}>1 Bintang ★</option>
                    </select>
                </form>

                {{-- Range Selector --}}
                <div class="relative inline-block text-left" x-data="{ isRangeOpen: false }">
                    <button @click="isRangeOpen = !isRangeOpen" type="button" class="inline-flex justify-center items-center gap-2 px-4 py-2 bg-white border border-secondary-300 rounded-xl text-xs font-bold text-secondary-700 hover:bg-secondary-50 shadow-sm">
                        <span class="capitalize">{{ $range === 'semua' ? 'Semua Waktu' : $range }}</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div x-show="isRangeOpen" @click.away="isRangeOpen = false" x-cloak class="origin-top-right absolute right-0 mt-2 w-48 rounded-xl shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-20 overflow-hidden">
                        <div class="py-1 text-xs">
                            @php $baseParams = array_merge(request()->only(['user_id', 'rating']), []); @endphp
                            <a href="{{ route($routePrefix . '.laporan.kepuasan', array_merge($baseParams, ['range' => 'semua'])) }}" class="block px-4 py-2 hover:bg-secondary-100 {{ $range === 'semua' ? 'bg-primary-50 text-primary-700 font-bold' : '' }}">Semua Waktu</a>
                            <a href="{{ route($routePrefix . '.laporan.kepuasan', array_merge($baseParams, ['range' => 'harian'])) }}" class="block px-4 py-2 hover:bg-secondary-100 {{ $range === 'harian' ? 'bg-primary-50 text-primary-700 font-bold' : '' }}">Hari Ini</a>
                            <a href="{{ route($routePrefix . '.laporan.kepuasan', array_merge($baseParams, ['range' => 'mingguan'])) }}" class="block px-4 py-2 hover:bg-secondary-100 {{ $range === 'mingguan' ? 'bg-primary-50 text-primary-700 font-bold' : '' }}">Minggu Ini</a>
                            <a href="{{ route($routePrefix . '.laporan.kepuasan', array_merge($baseParams, ['range' => 'bulanan'])) }}" class="block px-4 py-2 hover:bg-secondary-100 {{ $range === 'bulanan' ? 'bg-primary-50 text-primary-700 font-bold' : '' }}">Bulan Ini</a>
                        </div>
                    </div>
                </div>

                {{-- Export Buttons --}}
                <div class="flex items-center gap-2 ml-2 pl-4 border-l border-secondary-200">
                    <a href="{{ route($routePrefix . '.laporan.kepuasan.excel', request()->all()) }}" 
                       class="inline-flex items-center gap-2 px-4 py-2 bg-green-600 text-white rounded-xl text-xs font-bold hover:bg-green-700 shadow-sm transition-all active:scale-95">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        <span>Excel</span>
                    </a>
                    <a href="{{ route($routePrefix . '.laporan.kepuasan.pdf', request()->all()) }}" 
                       class="inline-flex items-center gap-2 px-4 py-2 bg-rose-600 text-white rounded-xl text-xs font-bold hover:bg-rose-700 shadow-sm transition-all active:scale-95">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                        <span>PDF</span>
                    </a>
                </div>
            </div>
        </div>

        {{-- Satisfaction Table --}}
        <div class="bg-white rounded-2xl shadow-sm border border-secondary-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-secondary-50 text-secondary-500 text-[10px] uppercase font-bold tracking-wider">
                        <tr>
                            <th class="px-6 py-4">Waktu & Tanggal</th>
                            <th class="px-6 py-4">Nomor</th>
                            <th class="px-6 py-4 text-center">Rating</th>
                            <th class="px-6 py-4">Pesan Terakhir (Customer)</th>
                            <th class="px-6 py-4">Jawaban (AI/Admin)</th>
                            <th class="px-6 py-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-secondary-100">
                        @forelse($logs as $log)
                        <tr class="hover:bg-secondary-50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="text-sm font-bold text-secondary-900">{{ $log->formatted_date }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-bold text-secondary-900">{{ $log->customer_name }}</div>
                                <div class="text-[10px] text-secondary-400 font-mono">{{ $log->customer_phone }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-center gap-1 bg-amber-50 px-2 py-1 rounded-lg border border-amber-100">
                                    <span class="text-sm font-black text-amber-700">{{ $log->rating }}</span>
                                    <svg class="w-3.5 h-3.5 text-amber-500 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                </div>
                            </td>
                            <td class="px-6 py-4 max-w-xs">
                                <p class="text-xs text-secondary-600 line-clamp-2">{{ $log->question }}</p>
                            </td>
                            <td class="px-6 py-4 max-w-xs">
                                <p class="text-xs text-secondary-900 font-medium line-clamp-2 italic">{{ $log->answer }}</p>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <button @click="$dispatch('open-detail', { phone: '{{ $log->customer_phone }}', name: '{{ $log->customer_name }}' })" 
                                        class="text-primary-600 hover:text-primary-900 font-bold text-xs bg-primary-50 px-3 py-1.5 rounded-lg transition-colors">
                                    Lihat Chat
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-secondary-400">
                                <div class="flex flex-col items-center gap-2">
                                    <svg class="w-12 h-12 text-secondary-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 10h4.757c1.27 0 1.906 1.535 1.007 2.434l-3.336 3.337a1 1 0 01-1.414 0L12 13.014V10zM10 14H5.243c-1.27 0-1.906-1.535-1.007-2.434l3.336-3.337a1 1 0 011.414 0L12 10.986V14z"/></svg>
                                    <p>Belum ada data penilaian kepuasan.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($logs->hasPages())
            <div class="px-6 py-4 border-t border-secondary-100 bg-secondary-50">
                {{ $logs->links() }}
            </div>
            @endif
        </div>
        @else
        {{-- Admin: No user selected prompt --}}
        <div class="flex items-center justify-center min-h-[40vh]">
            <div class="text-center max-w-md mx-auto">
                <div class="mx-auto w-20 h-20 bg-secondary-100 rounded-3xl flex items-center justify-center mb-6">
                    <svg class="w-10 h-10 text-secondary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                </div>
                <h3 class="text-lg font-bold text-secondary-900 mb-2">Pilih Pengguna</h3>
                <p class="text-sm text-secondary-500">Silakan pilih akun pengguna di atas untuk melihat laporan kepuasan pelanggan miliknya.</p>
            </div>
        </div>
        @endif
    </div>

    {{-- REUSE MODAL DETAIL FROM INTERAKSI (Via Dispatch) --}}
    @include('pengguna.laporan._modal_detail')

</x-aiagen-layout>
