<x-aiagen-layout>
    <x-slot name="header">
        Laporan Interaksi — WhatsApp
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
                    <p class="text-sm text-secondary-500 mt-1">Pilih pengguna untuk melihat laporan interaksi WhatsApp miliknya.</p>
                </div>
                <form method="GET" action="{{ route($routePrefix . '.laporan.interaksi.wa') }}" class="flex items-center gap-3">
                    <input type="hidden" name="range" value="{{ $range }}">
                    <input type="hidden" name="type" value="{{ $type }}">
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
            @if($selectedUser)
            <div class="mt-4 pt-4 border-t border-secondary-100">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-primary-600 rounded-full flex items-center justify-center text-white font-bold text-sm">
                        {{ strtoupper(substr($selectedUser->name, 0, 1)) }}
                    </div>
                    <div>
                        <p class="font-bold text-secondary-900 text-sm">{{ $selectedUser->name }}</p>
                        <p class="text-xs text-secondary-500">{{ $selectedUser->email }}</p>
                    </div>
                    <span class="ml-auto px-3 py-1 bg-green-100 text-green-700 rounded-full text-xs font-bold">Dipilih</span>
                </div>
            </div>
            @endif
        </div>
        @endif

        {{-- Show report only if user is selected (admin) or always for pengguna --}}
        @if(!$isAdmin || $selectedUser)
        {{-- Header & Filters --}}
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white p-6 rounded-2xl shadow-sm border border-secondary-200">
            <div>
                <h2 class="text-xl font-bold text-secondary-900">Statistik Interaksi WhatsApp</h2>
                <p class="text-sm text-secondary-500">Pantau performa AI dalam melayani pesan pelanggan via WhatsApp.</p>
            </div>
            
            <div class="flex flex-wrap items-center gap-3">
                {{-- Type Tabs (Personal / Grup) --}}
                <div class="inline-flex p-1 bg-secondary-100 rounded-xl">
                    <a href="{{ route($routePrefix . '.laporan.interaksi.wa', array_merge(['type' => 'personal', 'range' => $range], $isAdmin && $selectedUser ? ['user_id' => $selectedUser->id] : [])) }}" 
                       class="px-4 py-2 text-sm font-medium rounded-lg transition-all {{ $type === 'personal' ? 'bg-white text-primary-700 shadow-sm' : 'text-secondary-600 hover:text-secondary-900' }}">
                        Personal
                    </a>
                    <a href="{{ route($routePrefix . '.laporan.interaksi.wa', array_merge(['type' => 'grup', 'range' => $range], $isAdmin && $selectedUser ? ['user_id' => $selectedUser->id] : [])) }}" 
                       class="px-4 py-2 text-sm font-medium rounded-lg transition-all {{ $type === 'grup' ? 'bg-white text-primary-700 shadow-sm' : 'text-secondary-600 hover:text-secondary-900' }}">
                        Grup
                    </a>
                </div>

                {{-- Range Selector --}}
                <div class="relative inline-block text-left" x-data="{ isRangeOpen: false }">
                    <button @click="isRangeOpen = !isRangeOpen" type="button" class="inline-flex justify-center items-center gap-2 px-4 py-2 bg-white border border-secondary-300 rounded-xl text-sm font-medium text-secondary-700 hover:bg-secondary-50 shadow-sm">
                        <svg class="w-4 h-4 text-secondary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        <span class="capitalize">{{ $range }}</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div x-show="isRangeOpen" @click.away="isRangeOpen = false" x-cloak class="origin-top-right absolute right-0 mt-2 w-48 rounded-xl shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-20 overflow-hidden">
                        <div class="py-1">
                            @php 
                                $baseParams = $isAdmin && $selectedUser ? ['user_id' => $selectedUser->id] : [];
                            @endphp
                            <a href="{{ route($routePrefix . '.laporan.interaksi.wa', array_merge(['type' => $type, 'range' => 'harian'], $baseParams)) }}" class="block px-4 py-2 text-sm text-secondary-700 hover:bg-secondary-100 {{ $range === 'harian' ? 'bg-primary-50 text-primary-700 font-bold' : '' }}">Harian</a>
                            <a href="{{ route($routePrefix . '.laporan.interaksi.wa', array_merge(['type' => $type, 'range' => 'mingguan'], $baseParams)) }}" class="block px-4 py-2 text-sm text-secondary-700 hover:bg-secondary-100 {{ $range === 'mingguan' ? 'bg-primary-50 text-primary-700 font-bold' : '' }}">Mingguan</a>
                            <a href="{{ route($routePrefix . '.laporan.interaksi.wa', array_merge(['type' => $type, 'range' => 'bulanan'], $baseParams)) }}" class="block px-4 py-2 text-sm text-secondary-700 hover:bg-secondary-100 {{ $range === 'bulanan' ? 'bg-primary-50 text-primary-700 font-bold' : '' }}">Bulanan</a>
                            <a href="{{ route($routePrefix . '.laporan.interaksi.wa', array_merge(['type' => $type, 'range' => 'tahunan'], $baseParams)) }}" class="block px-4 py-2 text-sm text-secondary-700 hover:bg-secondary-100 {{ $range === 'tahunan' ? 'bg-primary-50 text-primary-700 font-bold' : '' }}">Tahunan</a>
                        </div>
                    </div>
                </div>

                {{-- Export Buttons --}}
                <div class="flex items-center gap-2 ml-2 pl-4 border-l border-secondary-200">
                    <a href="{{ route($routePrefix . '.laporan.interaksi.wa.export.excel', array_merge(['range' => $range, 'type' => $type], $baseParams ?? [])) }}" 
                       class="inline-flex items-center gap-2 px-4 py-2 bg-green-600 text-white rounded-xl text-sm font-bold hover:bg-green-700 shadow-sm transition-all active:scale-95" 
                       title="Unduh Excel (.xls)">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        <span>Excel</span>
                    </a>
                    <a href="{{ route($routePrefix . '.laporan.interaksi.wa.export.pdf', array_merge(['range' => $range, 'type' => $type], $baseParams ?? [])) }}" 
                       class="inline-flex items-center gap-2 px-4 py-2 bg-rose-600 text-white rounded-xl text-sm font-bold hover:bg-rose-700 shadow-sm transition-all active:scale-95" 
                       title="Unduh PDF">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 9h1m1 0h1m-3 4h1m1 0h1m-3 4h1m1 0h1"/></svg>
                        <span>PDF</span>
                    </a>
                </div>
            </div>
        </div>

        {{-- Main Chart --}}
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-secondary-200">
            <div class="flex items-center justify-between mb-6">
                <h3 class="font-bold text-secondary-900 flex items-center gap-2">
                    <svg class="w-5 h-5 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"/></svg>
                    Tren Interaksi {{ ucfirst($type) }}
                </h3>
                <div class="text-xs text-secondary-400 font-medium bg-secondary-100 px-3 py-1 rounded-full">
                    @if($range === 'harian') Per Jam @elseif($range === 'mingguan') Per Hari @elseif($range === 'bulanan') Per Tanggal @else Per Bulan @endif
                </div>
            </div>
            
            <div class="relative h-[350px]">
                <canvas id="interactionChart"></canvas>
            </div>
        </div>

        {{-- Top Interactions Table --}}
        <div class="bg-white rounded-2xl shadow-sm border border-secondary-200 overflow-hidden">
            <div class="p-6 border-b border-secondary-100 flex items-center justify-between">
                <h3 class="font-bold text-secondary-900 flex items-center gap-2">
                    <svg class="w-5 h-5 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    Daftar Interaksi Terbanyak (Top 10)
                </h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-secondary-50 text-secondary-500 text-xs uppercase font-bold tracking-wider">
                        <tr>
                            <th class="px-6 py-4">Peringkat</th>
                            <th class="px-6 py-4">Nama / Nickname</th>
                            <th class="px-6 py-4">ID / Nomor WA</th>
                            <th class="px-6 py-4 text-center">Total Pesan</th>
                            <th class="px-6 py-4 text-center">Sentiment</th>
                            <th class="px-6 py-4 text-center">Terjawab?</th>
                            <th class="px-6 py-4 text-center">Kepuasan (Avg)</th>
                            <th class="px-6 py-4 text-center">Status AI</th>
                            <th class="px-6 py-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-secondary-100">
                        @forelse($topInteractions as $index => $item)
                        <tr class="hover:bg-secondary-50 transition-colors group">
                            <td class="px-6 py-4">
                                <span class="w-8 h-8 rounded-lg flex items-center justify-center font-bold {{ $index < 3 ? 'bg-primary-100 text-primary-700' : 'bg-secondary-100 text-secondary-600' }}">
                                    {{ $index + 1 }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-bold text-secondary-900">{{ $item->name }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 bg-secondary-100 rounded-md text-xs font-mono text-secondary-600">{{ $item->customer_phone }}</span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-sm font-bold">{{ number_format($item->total) }}</span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($item->sentiment_score === 'positive') 😊
                                @elseif($item->sentiment_score === 'negative') 😠
                                @else 😐 @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($item->resolved_rate >= 0.5)
                                    <span class="text-green-600 font-bold text-xs">YA</span>
                                @else
                                    <span class="text-red-600 font-bold text-xs">TIDAK</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($item->avg_rating)
                                    <div class="flex items-center justify-center gap-1">
                                        <span class="text-sm font-bold text-secondary-900">{{ number_format($item->avg_rating, 1) }}</span>
                                        <svg class="w-4 h-4 text-amber-400 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                    </div>
                                @else
                                    <span class="text-xs text-secondary-400 italic">Belum ada</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                <button onclick="toggleAiStatus('{{ $item->customer_phone }}', {{ $item->is_ai_enabled ? 0 : 1 }}, {{ $isAdmin && $selectedUser ? $selectedUser->id : auth()->id() }}, this)"
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-[10px] font-bold uppercase transition-all {{ $item->is_ai_enabled ? 'bg-green-50 text-green-700 hover:bg-green-100' : 'bg-rose-50 text-rose-700 hover:bg-rose-100 ring-2 ring-rose-200 animate-pulse' }}"
                                        title="{{ $item->is_ai_enabled ? 'AI Aktif. Klik untuk Takeover' : 'Human Takeover Aktif. AI Berhenti. Klik untuk Aktifkan AI kembali' }}">
                                    @if($item->is_ai_enabled)
                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                        Active
                                    @else
                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                                        Hold
                                    @endif
                                </button>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <button @click="$dispatch('open-detail', { phone: '{{ $item->customer_phone }}', name: '{{ $item->name }}' })" 
                                        class="text-primary-600 hover:text-primary-900 font-bold text-sm bg-primary-50 px-3 py-1.5 rounded-lg transition-colors">
                                    Lihat Detail
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-secondary-400">
                                <div class="flex flex-col items-center gap-2">
                                    <svg class="w-12 h-12 text-secondary-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-3.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293H10.414a1 1 0 01-.707-.293L7.293 13.293A1 1 0 006.586 13H4"/></svg>
                                    <p>Belum ada data interaksi yang tercatat.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @else
        {{-- Admin: No user selected prompt --}}
        <div class="flex items-center justify-center min-h-[40vh]">
            <div class="text-center max-w-md mx-auto">
                <div class="mx-auto w-20 h-20 bg-secondary-100 rounded-3xl flex items-center justify-center mb-6">
                    <svg class="w-10 h-10 text-secondary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                </div>
                <h3 class="text-lg font-bold text-secondary-900 mb-2">Pilih Pengguna</h3>
                <p class="text-sm text-secondary-500">Silakan pilih akun pengguna di atas untuk melihat laporan interaksi WhatsApp miliknya.</p>
            </div>
        </div>
        @endif
    </div>

    {{-- MODAL DETAIL --}}
    @include('pengguna.laporan._modal_detail')

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        function showDetail(phone, name) {
            window.dispatchEvent(new CustomEvent('open-detail', { 
                detail: { phone: phone, name: name } 
            }));
        }

        async function toggleAiStatus(phone, newStatus, userId, buttonEl) {
            if (!confirm(newStatus ? 'Aktifkan AI kembali untuk nomor ini?' : 'Matikan AI (Human Takeover) untuk nomor ini?')) return;

            buttonEl.disabled = true;
            buttonEl.classList.add('opacity-50');

            try {
                const prefix = '{{ $isAdmin ? '/admin' : '/pengguna' }}';
                const response = await fetch(`${prefix}/laporan/interaksi/wa/toggle-ai`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        phone: phone,
                        status: newStatus,
                        user_id: userId
                    })
                });

                const result = await response.json();
                if (result.status === 'success') {
                    // Refresh current page to reflect changes
                    window.location.reload();
                } else {
                    alert('Gagal memperbarui status: ' + result.message);
                    buttonEl.disabled = false;
                    buttonEl.classList.remove('opacity-50');
                }
            } catch (error) {
                console.error('Error toggling AI status:', error);
                alert('Terjadi kesalahan koneksi.');
                buttonEl.disabled = false;
                buttonEl.classList.remove('opacity-50');
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            const canvas = document.getElementById('interactionChart');
            if (!canvas) return;

            const ctx = canvas.getContext('2d');

            // Create Gradient
            const gradient = ctx.createLinearGradient(0, 0, 0, 400);
            gradient.addColorStop(0, 'rgba(30, 64, 175, 0.4)');
            gradient.addColorStop(1, 'rgba(30, 64, 175, 0)');

            const data = {
                labels: {!! json_encode($stats['labels']) !!},
                datasets: [{
                    label: 'Jumlah Pesan',
                    data: {!! json_encode($stats['counts']) !!},
                    borderColor: '#1e40af',
                    backgroundColor: gradient,
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: '#fff',
                    pointBorderColor: '#1e40af',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6
                }]
            };

            const config = {
                type: 'line',
                data: data,
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            mode: 'index',
                            intersect: false,
                            backgroundColor: '#1f2937',
                            titleFont: { size: 14, weight: 'bold' },
                            padding: 12,
                            cornerRadius: 8,
                            displayColors: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                borderDash: [5, 5],
                                color: '#e5e7eb'
                            },
                            ticks: {
                                color: '#9ca3af',
                                font: { size: 11 },
                                stepSize: 1
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            },
                            ticks: {
                                color: '#9ca3af',
                                font: { size: 11 }
                            }
                        }
                    },
                    interaction: {
                        intersect: false,
                        mode: 'nearest',
                    }
                }
            };

            new Chart(ctx, config);
        });
    </script>
    @endpush
</x-aiagen-layout>
