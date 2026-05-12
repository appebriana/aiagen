<x-admin-layout>
    <x-slot name="header">
        Daftar Masukan Pengguna
    </x-slot>

    <div class="space-y-6">
        @if (session('success'))
            <div x-data="{ show: true }" x-show="show" x-transition class="p-4 bg-green-50 border border-green-100 rounded-2xl flex items-center justify-between text-green-700 text-sm font-medium shadow-sm">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                        <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    </div>
                    {{ session('success') }}
                </div>
                <button @click="show = false" class="text-green-400 hover:text-green-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
        @endif

        {{-- Stats Overview --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white p-5 rounded-2xl border border-secondary-200 shadow-sm flex items-center gap-4">
                <div class="w-12 h-12 bg-primary-50 rounded-xl flex items-center justify-center text-primary-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z"/></svg>
                </div>
                <div>
                    <p class="text-xs font-bold text-secondary-500 uppercase tracking-wider">Total</p>
                    <h3 class="text-xl font-black text-secondary-900">{{ $stats['total'] }}</h3>
                </div>
            </div>
            <div class="bg-white p-5 rounded-2xl border border-secondary-200 shadow-sm flex items-center gap-4">
                <div class="w-12 h-12 bg-secondary-100 rounded-xl flex items-center justify-center text-secondary-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div>
                    <p class="text-xs font-bold text-secondary-500 uppercase tracking-wider">Draf</p>
                    <h3 class="text-xl font-black text-secondary-900">{{ $stats['draf'] }}</h3>
                </div>
            </div>
            <div class="bg-white p-5 rounded-2xl border border-secondary-200 shadow-sm flex items-center gap-4">
                <div class="w-12 h-12 bg-amber-50 rounded-xl flex items-center justify-center text-amber-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                </div>
                <div>
                    <p class="text-xs font-bold text-secondary-500 uppercase tracking-wider">Proses</p>
                    <h3 class="text-xl font-black text-secondary-900">{{ $stats['proses'] }}</h3>
                </div>
            </div>
            <div class="bg-white p-5 rounded-2xl border border-secondary-200 shadow-sm flex items-center gap-4">
                <div class="w-12 h-12 bg-green-50 rounded-xl flex items-center justify-center text-green-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div>
                    <p class="text-xs font-bold text-secondary-500 uppercase tracking-wider">Selesai</p>
                    <h3 class="text-xl font-black text-secondary-900">{{ $stats['selesai'] }}</h3>
                </div>
            </div>
        </div>

        <div class="bg-white border border-secondary-200 rounded-3xl overflow-hidden shadow-sm">
            <div class="p-6 border-b border-secondary-100 flex items-center justify-between">
                <h3 class="font-black text-secondary-900 flex items-center gap-2">
                    Semua Masukan
                    <span class="px-2 py-0.5 bg-primary-100 text-primary-700 text-[10px] font-black rounded-full">{{ $feedbacks->total() }}</span>
                </h3>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="bg-secondary-50/50 text-secondary-500 uppercase tracking-widest text-[10px] font-black border-b border-secondary-100">
                        <tr>
                            <th class="px-8 py-5">Pengguna & Tanggal</th>
                            <th class="px-8 py-5">Pesan Masukan</th>
                            <th class="px-8 py-5">Status</th>
                            <th class="px-8 py-5 text-right">Aksi Cepat</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-secondary-100">
                        @forelse ($feedbacks as $feedback)
                            <tr class="hover:bg-secondary-50/30 transition-all duration-300 group">
                                <td class="px-8 py-6 whitespace-nowrap">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 bg-primary-50 text-primary-600 rounded-full flex items-center justify-center font-black text-sm uppercase">
                                            {{ substr($feedback->user->name ?? '?', 0, 1) }}
                                        </div>
                                        <div>
                                            <p class="font-bold text-secondary-900 truncate max-w-[150px]">
                                                {{ $feedback->user->name ?? 'Pengguna Dihapus' }}
                                            </p>
                                            <p class="text-[11px] text-secondary-500 font-medium tracking-tight">
                                                {{ $feedback->created_at->translatedFormat('d M Y, H:i') }}
                                            </p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-8 py-6">
                                    <div class="max-w-md">
                                        <p class="text-secondary-700 leading-relaxed font-medium line-clamp-2 group-hover:line-clamp-none transition-all duration-500">
                                            {{ $feedback->message }}
                                        </p>
                                    </div>
                                </td>
                                <td class="px-8 py-6 whitespace-nowrap">
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-[11px] font-black tracking-wider uppercase
                                        {{ $feedback->status === 'selesai' ? 'bg-green-100 text-green-700' : 
                                          ($feedback->status === 'proses' ? 'bg-amber-100 text-amber-700' : 'bg-secondary-100 text-secondary-700') }}">
                                        <span class="w-1.5 h-1.5 rounded-full {{ $feedback->status === 'selesai' ? 'bg-green-500' : ($feedback->status === 'proses' ? 'bg-amber-500' : 'bg-secondary-500') }}"></span>
                                        {{ $feedback->status }}
                                    </span>
                                </td>
                                <td class="px-8 py-6 whitespace-nowrap text-right">
                                    <form action="{{ route('admin.feedback.updateStatus', $feedback->id) }}" method="POST" class="inline-flex items-center gap-2">
                                        @csrf
                                        @method('PUT')
                                        <select name="status" onchange="this.form.submit()" 
                                                class="text-[11px] font-bold border-secondary-200 rounded-xl focus:ring-4 focus:ring-primary-500/10 focus:border-primary-500 py-1.5 px-3 pr-8 shadow-sm transition-all bg-secondary-50/50 hover:bg-white">
                                            <option value="draf" {{ $feedback->status === 'draf' ? 'selected' : '' }}>Set Draf</option>
                                            <option value="proses" {{ $feedback->status === 'proses' ? 'selected' : '' }}>Mulai Proses</option>
                                            <option value="selesai" {{ $feedback->status === 'selesai' ? 'selected' : '' }}>Selesaikan</option>
                                        </select>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-8 py-24 text-center">
                                    <div class="flex flex-col items-center justify-center max-w-xs mx-auto">
                                        <div class="w-24 h-24 bg-secondary-50 rounded-full flex items-center justify-center mb-6">
                                            <svg class="w-10 h-10 text-secondary-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z"/></svg>
                                        </div>
                                        <h4 class="text-lg font-black text-secondary-900 mb-2">Belum Ada Masukan</h4>
                                        <p class="text-sm text-secondary-500 font-medium">Kotak masuk Anda sedang kosong. Feedback dari pengguna akan muncul di sini.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if ($feedbacks->hasPages())
                <div class="px-8 py-6 border-t border-secondary-100 bg-secondary-50/30">
                    {{ $feedbacks->links() }}
                </div>
            @endif
        </div>
    </div>
</x-admin-layout>
