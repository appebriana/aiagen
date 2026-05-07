<x-pengguna-layout>
    <x-slot name="header">Analitik & Statistik</x-slot>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
        {{-- Pesan AI Hari Ini --}}
        <div class="bg-white rounded-2xl shadow-sm border border-secondary-200 p-6">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-primary-100 text-primary-600 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
                </div>
                <div>
                    <p class="text-[10px] font-bold text-secondary-400 uppercase tracking-widest">AI Terjawab Hari Ini</p>
                    <p class="text-2xl font-bold text-secondary-900">{{ number_format($messagesToday) }}</p>
                </div>
            </div>
        </div>

        {{-- Total Biaya --}}
        <div class="bg-white rounded-2xl shadow-sm border border-secondary-200 p-6">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-emerald-100 text-emerald-600 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                </div>
                <div>
                    <p class="text-[10px] font-bold text-secondary-400 uppercase tracking-widest">Total Token API</p>
                    <p class="text-2xl font-bold text-secondary-900">{{ number_format($totalTokens) }}</p>
                </div>
            </div>
        </div>

        {{-- Total Departemen --}}
        <div class="bg-white rounded-2xl shadow-sm border border-secondary-200 p-6">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-blue-100 text-blue-600 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                </div>
                <div>
                    <p class="text-[10px] font-bold text-secondary-400 uppercase tracking-widest">Total Departemen</p>
                    <p class="text-2xl font-bold text-secondary-900">{{ $totalDepartments }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        {{-- Grafik Pesan --}}
        <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-secondary-200 p-6">
            <h3 class="text-lg font-bold text-secondary-900 mb-6">Aktivitas AI Anda (7 Hari Terakhir)</h3>
            <div class="h-[300px]">
                <canvas id="messageChart"></canvas>
            </div>
        </div>

        {{-- Top 5 Pertanyaan --}}
        <div class="bg-white rounded-2xl shadow-sm border border-secondary-200 p-6">
            <h3 class="text-lg font-bold text-secondary-900 mb-6">Pertanyaan Populer</h3>
            <div class="space-y-4">
                @foreach($topQuestions as $q)
                <div class="p-3 bg-secondary-50 rounded-xl border border-secondary-100">
                    <div class="flex justify-between items-start mb-1">
                        <span class="text-xs font-bold text-primary-600 bg-primary-50 px-2 py-0.5 rounded-full">{{ $q->count }}x</span>
                    </div>
                    <p class="text-sm text-secondary-700 font-medium line-clamp-2 italic">"{{ $q->question }}"</p>
                </div>
                @endforeach
                @if($topQuestions->isEmpty())
                <p class="text-center text-secondary-400 py-8 italic">Belum ada data pertanyaan.</p>
                @endif
            </div>
        </div>
    </div>

    {{-- Biaya per Departemen --}}
    <div class="mt-8 bg-white rounded-2xl shadow-sm border border-secondary-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-secondary-100">
            <h3 class="text-lg font-bold text-secondary-900">Biaya per Departemen</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-secondary-50">
                        <th class="px-6 py-4 text-[10px] font-bold text-secondary-500 uppercase tracking-widest">Departemen</th>
                        <th class="px-6 py-4 text-[10px] font-bold text-secondary-500 uppercase tracking-widest">Total Token</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-secondary-100">
                    @foreach($tokensPerDept as $stat)
                    <tr class="hover:bg-secondary-50/50 transition-colors">
                        <td class="px-6 py-4">
                            <span class="text-sm font-semibold text-secondary-900">{{ $stat->department->name ?? 'Unknown' }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-sm font-bold text-emerald-600">{{ number_format($stat->total_tokens) }}</span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const ctx = document.getElementById('messageChart').getContext('2d');
        const chartData = @json($chartData);
        
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: chartData.map(d => d.date),
                datasets: [{
                    label: 'Jumlah Pesan Terjawab',
                    data: chartData.map(d => d.count),
                    borderColor: '#2563eb',
                    backgroundColor: 'rgba(37, 99, 235, 0.1)',
                    fill: true,
                    tension: 0.4,
                    borderWidth: 3,
                    pointBackgroundColor: '#2563eb',
                    pointRadius: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { stepSize: 1 }
                    }
                }
            }
        });
    </script>
    @endpush
</x-pengguna-layout>
