<x-aiagen-layout>
    <x-slot name="header">
        Laporan Interaksi
    </x-slot>

    <div class="space-y-6">
        {{-- Header & Filters --}}
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white p-6 rounded-2xl shadow-sm border border-secondary-200">
            <div>
                <h2 class="text-xl font-bold text-secondary-900">Statistik Interaksi AI</h2>
                <p class="text-sm text-secondary-500">Pantau performa AI dalam melayani pesan pelanggan.</p>
            </div>
            
            <div class="flex flex-wrap items-center gap-3">
                {{-- Type Tabs (Personal / Grup) --}}
                <div class="inline-flex p-1 bg-secondary-100 rounded-xl">
                    <a href="{{ route('pengguna.laporan.interaksi', ['type' => 'personal', 'range' => $range]) }}" 
                       class="px-4 py-2 text-sm font-medium rounded-lg transition-all {{ $type === 'personal' ? 'bg-white text-primary-700 shadow-sm' : 'text-secondary-600 hover:text-secondary-900' }}">
                        Personal
                    </a>
                    <a href="{{ route('pengguna.laporan.interaksi', ['type' => 'grup', 'range' => $range]) }}" 
                       class="px-4 py-2 text-sm font-medium rounded-lg transition-all {{ $type === 'grup' ? 'bg-white text-primary-700 shadow-sm' : 'text-secondary-600 hover:text-secondary-900' }}">
                        Grup
                    </a>
                </div>

                {{-- Range Selector --}}
                <div class="relative inline-block text-left" x-data="{ open: false }">
                    <button @click="open = !open" type="button" class="inline-flex justify-center items-center gap-2 px-4 py-2 bg-white border border-secondary-300 rounded-xl text-sm font-medium text-secondary-700 hover:bg-secondary-50 shadow-sm">
                        <svg class="w-4 h-4 text-secondary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        <span class="capitalize">{{ $range }}</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div x-show="open" @click.away="open = false" x-cloak class="origin-top-right absolute right-0 mt-2 w-48 rounded-xl shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-20 overflow-hidden">
                        <div class="py-1">
                            <a href="{{ route('pengguna.laporan.interaksi', ['type' => $type, 'range' => 'harian']) }}" class="block px-4 py-2 text-sm text-secondary-700 hover:bg-secondary-100 {{ $range === 'harian' ? 'bg-primary-50 text-primary-700 font-bold' : '' }}">Harian</a>
                            <a href="{{ route('pengguna.laporan.interaksi', ['type' => $type, 'range' => 'mingguan']) }}" class="block px-4 py-2 text-sm text-secondary-700 hover:bg-secondary-100 {{ $range === 'mingguan' ? 'bg-primary-50 text-primary-700 font-bold' : '' }}">Mingguan</a>
                            <a href="{{ route('pengguna.laporan.interaksi', ['type' => $type, 'range' => 'bulanan']) }}" class="block px-4 py-2 text-sm text-secondary-700 hover:bg-secondary-100 {{ $range === 'bulanan' ? 'bg-primary-50 text-primary-700 font-bold' : '' }}">Bulanan</a>
                            <a href="{{ route('pengguna.laporan.interaksi', ['type' => $type, 'range' => 'tahunan']) }}" class="block px-4 py-2 text-sm text-secondary-700 hover:bg-secondary-100 {{ $range === 'tahunan' ? 'bg-primary-50 text-primary-700 font-bold' : '' }}">Tahunan</a>
                        </div>
                    </div>
                </div>

                {{-- Export Buttons --}}
                <div class="flex items-center gap-2 ml-2 pl-4 border-l border-secondary-200">
                    <a href="{{ route('pengguna.laporan.interaksi.export.excel', ['range' => $range, 'type' => $type]) }}" 
                       class="inline-flex items-center gap-2 px-4 py-2 bg-green-600 text-white rounded-xl text-sm font-bold hover:bg-green-700 shadow-sm transition-all active:scale-95" 
                       title="Unduh Excel (.xls)">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        <span>Excel</span>
                    </a>
                    <a href="{{ route('pengguna.laporan.interaksi.export.pdf', ['range' => $range, 'type' => $type]) }}" 
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
                            <td class="px-6 py-4 text-right">
                                <button @click="showDetail('{{ $item->customer_phone }}', '{{ $item->name }}')" 
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
    </div>

    {{-- MODAL DETAIL --}}
    <div x-data="{ 
            open: false, 
            phone: '', 
            name: '', 
            logs: [], 
            loading: false,
            async fetchLogs() {
                this.loading = true;
                this.logs = [];
                try {
                    const response = await fetch(`/pengguna/laporan/interaksi/detail/${this.phone}?range={{ $range }}`);
                    const result = await response.json();
                    if (result.status === 'success') {
                        this.logs = result.data;
                    }
                } catch (error) {
                    console.error('Gagal mengambil log:', error);
                } finally {
                    this.loading = false;
                }
            }
         }" 
         x-show="open" 
         @open-detail.window="open = true; phone = $event.detail.phone; name = $event.detail.name; fetchLogs()"
         class="fixed inset-0 z-[60] overflow-y-auto" 
         x-cloak>
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="open" x-transition.opacity class="fixed inset-0 transition-opacity bg-secondary-900/75 backdrop-blur-sm"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>

            <div x-show="open" 
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 class="inline-block w-full max-w-4xl overflow-hidden text-left align-middle transition-all transform bg-white shadow-2xl rounded-3xl sm:my-8">
                
                {{-- Modal Header --}}
                <div class="px-6 py-4 border-b border-secondary-100 flex items-center justify-between bg-secondary-50">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-primary-600 rounded-full flex items-center justify-center text-white font-bold">
                            <span x-text="name.substring(0, 1).toUpperCase()"></span>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-secondary-900" x-text="name"></h3>
                            <p class="text-xs text-secondary-500" x-text="phone"></p>
                        </div>
                    </div>
                    <button @click="open = false" class="text-secondary-400 hover:text-secondary-600 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                {{-- Modal Body (Chat Logs) --}}
                <div class="p-6 max-h-[60vh] overflow-y-auto bg-secondary-50/50 space-y-4" id="chat-container">
                    <template x-if="loading">
                        <div class="flex justify-center py-12">
                            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary-600"></div>
                        </div>
                    </template>

                    <template x-if="!loading && logs.length === 0">
                        <div class="text-center py-12 text-secondary-400 italic">Tidak ada riwayat percakapan.</div>
                    </template>

                    <template x-for="log in logs" :key="log.id">
                        <div class="space-y-4">
                            {{-- User Question --}}
                            <div class="flex justify-end">
                                <div class="max-w-[80%] bg-primary-600 text-white p-4 rounded-2xl rounded-tr-none shadow-sm">
                                    <p class="text-sm" x-text="log.question"></p>
                                    <p class="text-[10px] text-primary-200 mt-2 text-right" x-text="log.formatted_date"></p>
                                </div>
                            </div>
                            {{-- AI Answer --}}
                            <div class="flex justify-start">
                                <div class="max-w-[80%] bg-white text-secondary-900 p-4 rounded-2xl rounded-tl-none shadow-sm border border-secondary-200">
                                    <p class="text-sm whitespace-pre-wrap" x-text="log.answer"></p>
                                    <div class="flex items-center gap-2 mt-2">
                                        <p class="text-[10px] text-secondary-400" x-text="log.formatted_date"></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>

                {{-- Modal Footer --}}
                <div class="px-6 py-4 bg-white border-t border-secondary-100 flex justify-end">
                    <button @click="open = false" class="px-6 py-2 bg-secondary-100 text-secondary-700 rounded-xl font-bold hover:bg-secondary-200 transition-colors">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        function showDetail(phone, name) {
            window.dispatchEvent(new CustomEvent('open-detail', { 
                detail: { phone: phone, name: name } 
            }));
        }

        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('interactionChart').getContext('2d');

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

