<x-aiagen-layout>
    <x-slot name="header">Riwayat Pertanyaan WhatsApp</x-slot>

    <div x-data="unansweredManager()" class="space-y-6 relative">
        
        {{-- ═══ FILTER SECTION ═══ --}}
        <div class="bg-white rounded-2xl shadow-sm border border-secondary-200 p-4 md:p-6">
            <form action="{{ route(Auth::user()->role . '.ai-agen.unanswered.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-4 items-end">
                @if(Auth::user()->role === 'admin')
                <div x-data='userSearchableSelect({ 
                        selectedId: @json(request('user_id')),
                        selectedName: @json(request('user_id') ? ($users->where('id', request('user_id'))->first()->name ?? "-- Pilih Akun --") : "-- Pilih Akun --"),
                        users: @json($users->map(fn($u) => ["id" => $u->id, "name" => $u->name]))
                    })' class="relative">
                    <label class="block text-[10px] font-bold text-secondary-500 uppercase tracking-widest mb-1.5">Pilih Akun Pengguna</label>
                    
                    {{-- Hidden Input for form submission --}}
                    <input type="hidden" name="user_id" :value="selectedId">

                    {{-- Searchable Trigger --}}
                    <div @click="open = !open" 
                         class="w-full px-4 py-2.5 rounded-xl border border-secondary-200 bg-primary-50/30 flex items-center justify-between cursor-pointer hover:border-primary-500 transition-all shadow-sm">
                        <span class="text-sm font-bold text-primary-600 truncate" x-text="selectedName"></span>
                        <svg class="w-4 h-4 text-primary-400 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </div>

                    {{-- Dropdown Panel --}}
                    <div x-show="open" 
                         x-cloak
                         style="display: none;"
                         @click.away="open = false"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 translate-y-2"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         class="absolute z-50 mt-2 w-full bg-white rounded-2xl shadow-2xl border border-secondary-100 overflow-hidden min-w-[240px]">
                        
                        <div class="p-3 border-b border-secondary-50 bg-secondary-50/50">
                            <input type="text" 
                                   x-model="search" 
                                   placeholder="Cari nama pengguna..." 
                                   class="w-full px-3 py-2 text-xs rounded-lg border-secondary-200 focus:border-primary-500 focus:ring-4 focus:ring-primary-500/10"
                                   @click.stop>
                        </div>

                        <div class="max-h-60 overflow-y-auto scrollbar-hide">
                            <template x-for="user in filteredUsers" :key="user.id">
                                <div @click="selectUser(user.id, user.name)" 
                                     class="px-4 py-3 text-sm text-secondary-700 hover:bg-primary-50 hover:text-primary-600 cursor-pointer transition-colors flex items-center justify-between group">
                                    <span x-text="user.name" class="font-medium"></span>
                                    <svg x-show="selectedId == user.id" class="w-4 h-4 text-primary-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                </div>
                            </template>
                            <div x-show="filteredUsers.length === 0" class="px-4 py-8 text-center text-xs text-secondary-400 italic">
                                Pengguna tidak ditemukan
                            </div>
                        </div>
                    </div>
                </div>
                @endif
                <div>
                    <label class="block text-[10px] font-bold text-secondary-500 uppercase tracking-widest mb-1.5">Cari Pertanyaan/HP</label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Ketik..." 
                        class="w-full px-4 py-2.5 rounded-xl border-secondary-200 focus:border-primary-500 focus:ring-4 focus:ring-primary-500/10 text-sm">
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-secondary-500 uppercase tracking-widest mb-1.5">Mulai Tanggal</label>
                    <input type="date" name="date_start" value="{{ request('date_start') }}" 
                        class="w-full px-4 py-2.5 rounded-xl border-secondary-200 focus:border-primary-500 focus:ring-4 focus:ring-primary-500/10 text-sm">
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-secondary-500 uppercase tracking-widest mb-1.5">Sampai Tanggal</label>
                    <input type="date" name="date_end" value="{{ request('date_end') }}" 
                        class="w-full px-4 py-2.5 rounded-xl border-secondary-200 focus:border-primary-500 focus:ring-4 focus:ring-primary-500/10 text-sm">
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-secondary-500 uppercase tracking-widest mb-1.5">Status</label>
                    <select name="status" class="w-full px-4 py-2.5 rounded-xl border-secondary-200 focus:border-primary-500 focus:ring-4 focus:ring-primary-500/10 text-sm">
                        <option value="">Semua Status</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Menunggu</option>
                        <option value="answered" {{ request('status') == 'answered' ? 'selected' : '' }}>Terjawab</option>
                    </select>
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="flex-1 px-4 py-2.5 bg-primary-600 text-white rounded-xl font-bold text-sm hover:bg-primary-700 transition-all flex items-center justify-center gap-2 shadow-lg shadow-primary-500/20">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        Filter
                    </button>
                    <a href="{{ route(Auth::user()->role . '.ai-agen.unanswered.export-pdf', request()->all()) }}" 
                       class="px-4 py-2.5 bg-red-50 text-red-600 border border-red-100 rounded-xl font-bold text-sm hover:bg-red-100 transition-all flex items-center justify-center gap-2"
                       title="Export ke PDF sesuai filter">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                        PDF
                    </a>
                </div>
            </form>
        </div>

        {{-- ═══ LIST SECTION ═══ --}}
        <div class="space-y-4">
            {{-- Desktop Table --}}
            <div class="hidden md:block bg-white rounded-2xl shadow-sm border border-secondary-200 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-secondary-50 border-b border-secondary-200">
                                <th class="px-6 py-4 w-10">
                                    <input type="checkbox" x-model="allSelected" @change="toggleAll()" class="rounded border-secondary-300 text-primary-600 focus:ring-primary-500">
                                </th>
                                <th class="px-6 py-4 text-xs font-bold text-secondary-500 uppercase tracking-wider">Tanggal</th>
                                <th class="px-6 py-4 text-xs font-bold text-secondary-500 uppercase tracking-wider">Pengirim</th>
                                <th class="px-6 py-4 text-xs font-bold text-secondary-500 uppercase tracking-wider">Departemen</th>
                                <th class="px-6 py-4 text-xs font-bold text-secondary-500 uppercase tracking-wider">Pertanyaan</th>
                                <th class="px-6 py-4 text-xs font-bold text-secondary-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-4 text-xs font-bold text-secondary-500 uppercase tracking-wider text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-secondary-200">
                            @forelse($questions as $item)
                                <tr class="hover:bg-secondary-50/50 transition-colors" :class="selectedIds.includes({{ $item->id }}) ? 'bg-primary-50/50' : ''">
                                    <td class="px-6 py-4">
                                        <input type="checkbox" value="{{ $item->id }}" x-model="selectedIds" class="rounded border-secondary-300 text-primary-600 focus:ring-primary-500">
                                    </td>
                                    <td class="px-6 py-4 text-sm text-secondary-600 whitespace-nowrap">{{ $item->created_at->format('d/m/Y H:i') }}</td>
                                    <td class="px-6 py-4 text-sm font-medium text-secondary-900">
                                        @if($item->customer)
                                            <div class="flex flex-col">
                                                <span class="font-bold">{{ $item->customer->nickname ?: $item->customer->name }}</span>
                                                <span class="text-[10px] text-secondary-400">{{ $item->sender }}</span>
                                            </div>
                                        @else
                                            {{ $item->sender ?? 'Anonim' }}
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-sm text-secondary-600">{{ $item->department->name }}</td>
                                    <td class="px-6 py-4 text-sm text-secondary-600 max-w-xs truncate" title="{{ $item->question }}">{{ $item->question }}</td>
                                    <td class="px-6 py-4">
                                        @if($item->is_answered)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                Terjawab
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800">
                                                Menunggu
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-right space-x-2 whitespace-nowrap">
                                        <button type="button" 
                                            onclick="openAnswerModal({{ $item->id }}, '{{ addslashes($item->question) }}', '{{ addslashes($item->answer) }}')"
                                            class="p-2 rounded-xl text-primary-600 hover:bg-primary-50 transition-all"
                                            title="Beri Jawaban">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        </button>
                                        <form action="{{ route(Auth::user()->role . '.ai-agen.unanswered.destroy', $item->id) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-2 rounded-xl text-red-500 hover:bg-red-50 transition-all" onclick="return confirm('Hapus pertanyaan ini?')">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                @if(Auth::user()->role === 'admin' && !request('user_id'))
                                    <tr>
                                        <td colspan="7" class="px-6 py-20 text-center">
                                            <div class="flex flex-col items-center gap-3">
                                                <div class="w-16 h-16 bg-primary-50 text-primary-500 rounded-full flex items-center justify-center shadow-inner">
                                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                                </div>
                                                <p class="text-secondary-600 font-bold">Silakan pilih akun pengguna terlebih dahulu</p>
                                                <p class="text-xs text-secondary-400">Pilih salah satu akun pada filter di atas untuk melihat data.</p>
                                            </div>
                                        </td>
                                    </tr>
                                @else
                                    <tr>
                                        <td colspan="7" class="px-6 py-12 text-center text-secondary-400 italic">Data tidak ditemukan.</td>
                                    </tr>
                                @endif
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Mobile Card List --}}
            <div class="md:hidden space-y-4">
                @forelse($questions as $item)
                    <div class="bg-white rounded-2xl shadow-sm border border-secondary-200 p-5 relative transition-all"
                         :class="selectedIds.includes({{ $item->id }}) ? 'border-primary-500 ring-1 ring-primary-500' : ''">
                        <div class="flex items-start justify-between mb-4">
                            <div class="flex items-center gap-3">
                                <input type="checkbox" value="{{ $item->id }}" x-model="selectedIds" class="rounded border-secondary-300 text-primary-600 focus:ring-primary-500">
                                <div>
                                    <p class="text-[10px] text-secondary-400 font-bold uppercase tracking-widest">{{ $item->created_at->format('d M Y, H:i') }}</p>
                                    <h4 class="text-sm font-bold text-secondary-900">
                                        @if($item->customer)
                                            {{ $item->customer->nickname ?: $item->customer->name }}
                                        @else
                                            {{ $item->sender ?? 'Anonim' }}
                                        @endif
                                    </h4>
                                </div>
                            </div>
                            @if($item->is_answered)
                                <span class="px-2 py-1 bg-green-100 text-green-700 rounded text-[9px] font-bold uppercase">Terjawab</span>
                            @else
                                <span class="px-2 py-1 bg-amber-100 text-amber-700 rounded text-[9px] font-bold uppercase tracking-wider">Menunggu</span>
                            @endif
                        </div>

                        <div class="bg-secondary-50 rounded-xl p-3 mb-4">
                            <p class="text-xs text-secondary-500 font-bold uppercase tracking-widest mb-1">Pertanyaan</p>
                            <p class="text-sm text-secondary-700 line-clamp-3 italic">"{{ $item->question }}"</p>
                        </div>

                        <div class="flex items-center justify-between">
                            <span class="px-2 py-1 bg-secondary-100 text-secondary-600 rounded text-[10px] font-bold uppercase">
                                {{ $item->department->name }}
                            </span>
                            <div class="flex items-center gap-1">
                                <button type="button" 
                                    onclick="openAnswerModal({{ $item->id }}, '{{ addslashes($item->question) }}', '{{ addslashes($item->answer) }}')"
                                    class="p-2 bg-primary-50 text-primary-600 rounded-xl">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </button>
                                <form action="{{ route(Auth::user()->role . '.ai-agen.unanswered.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Hapus pertanyaan ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2 bg-red-50 text-red-500 rounded-xl">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @empty
                @if(Auth::user()->role === 'admin' && !request('user_id'))
                    <div class="bg-white p-12 rounded-2xl border border-secondary-200 text-center">
                        <p class="text-secondary-600 font-bold mb-2">Silakan pilih akun pengguna</p>
                        <p class="text-xs text-secondary-400">Gunakan filter di atas untuk memilih akun.</p>
                    </div>
                @else
                    <div class="bg-white p-12 rounded-2xl border border-secondary-200 text-center text-secondary-400 text-sm">
                        Data tidak ditemukan.
                    </div>
                @endif
                @endforelse
            </div>
            
            @if($questions->hasPages())
                <div class="px-6 py-4 bg-white rounded-2xl border border-secondary-200">
                    {{ $questions->links() }}
                </div>
            @endif
        </div>

        {{-- ═══ FLOATING BULK BAR ═══ --}}
        <div x-show="selectedIds.length > 0" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="translate-y-20 opacity-0"
             x-transition:enter-end="translate-y-0 opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="translate-y-0 opacity-100"
             x-transition:leave-end="translate-y-20 opacity-0"
             style="display: none;"
             class="fixed bottom-20 md:bottom-8 left-1/2 -translate-x-1/2 z-[90] w-[90%] md:w-auto">
            <div class="bg-secondary-900/90 text-white px-4 md:px-6 py-4 rounded-2xl shadow-2xl flex items-center justify-between md:justify-start gap-4 md:gap-6 border border-white/10 backdrop-blur-md">
                <div class="flex items-center gap-3 md:border-r md:border-white/20 md:pr-6">
                    <span class="w-7 h-7 bg-primary-500 rounded-full flex items-center justify-center text-xs font-bold shadow-lg shadow-primary-500/40" x-text="selectedIds.length"></span>
                    <span class="text-xs md:text-sm font-medium">Terpilih</span>
                </div>
                <div class="flex items-center gap-2 md:gap-3">
                    <button @click="bulkDelete()" class="px-3 md:px-4 py-2 bg-red-500 hover:bg-red-600 text-white rounded-xl text-[11px] md:text-sm font-bold transition-all flex items-center gap-2">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        <span class="hidden sm:inline">Hapus Masal</span>
                        <span class="sm:hidden">Hapus</span>
                    </button>
                    <button @click="selectedIds = []; allSelected = false" class="text-xs text-secondary-400 hover:text-white transition-colors">Batal</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Answer -->
    <div id="answerModal" class="fixed inset-0 z-[110] hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen p-4 text-center">
            <div class="fixed inset-0 bg-secondary-900/60 backdrop-blur-sm transition-opacity" aria-hidden="true" onclick="closeAnswerModal()"></div>
            
            <div class="inline-block align-middle bg-white rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:max-w-lg sm:w-full border border-secondary-200">
                <form id="answerForm" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="bg-white px-6 pt-6 pb-6">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-xl font-bold text-secondary-900" id="modal-title">Beri Jawaban Manual</h3>
                            <button type="button" onclick="closeAnswerModal()" class="text-secondary-400 hover:text-secondary-600 transition-colors">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>
                        <div class="space-y-5">
                            <div>
                                <label class="block text-sm font-bold text-secondary-700 mb-2">Pertanyaan dari WhatsApp:</label>
                                <div class="bg-primary-50 text-primary-900 px-4 py-3 rounded-2xl border border-primary-100 text-sm italic relative">
                                    <svg class="w-8 h-8 text-primary-200 absolute -top-2 -left-2 opacity-50" fill="currentColor" viewBox="0 0 24 24"><path d="M14.017 21L14.017 18C14.017 16.8954 14.9124 16 16.017 16H19.017C19.5693 16 20.017 15.5523 20.017 15V9C20.017 8.44772 19.5693 8 19.017 8H16.017C14.9124 8 14.017 7.10457 14.017 6V3L21.017 3V15C21.017 18.3137 18.3307 21 15.017 21H14.017ZM3 21L3 18C3 16.8954 3.89543 16 5 16H8C8.55228 16 9 15.5523 9 15V9C9 8.44772 8.55228 8 8 8H5C3.89543 8 3 7.10457 3 6V3L10 3V15C10 18.3137 7.31371 21 4 21H3Z"/></svg>
                                    <span id="modalQuestion"></span>
                                </div>
                            </div>
                            <div>
                                <label for="answer" class="block text-sm font-bold text-secondary-700 mb-2">Jawaban AI (Akan digunakan ke depan):</label>
                                <textarea name="answer" id="modalAnswer" rows="5" 
                                    class="w-full px-4 py-3 rounded-2xl border-secondary-200 focus:border-primary-500 focus:ring-4 focus:ring-primary-500/10 transition-all text-sm"
                                    placeholder="Ketik jawaban yang akan diberikan AI untuk pertanyaan ini..." required></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="bg-secondary-50 px-6 py-4 pb-12 sm:pb-4 flex flex-col-reverse sm:flex-row sm:justify-end gap-3">
                        <button type="button" onclick="closeAnswerModal()" class="w-full sm:w-auto px-6 py-2.5 rounded-xl border border-secondary-300 bg-white text-sm font-bold text-secondary-700 hover:bg-secondary-50 transition-colors">Batal</button>
                        <button type="submit" class="w-full sm:w-auto px-6 py-2.5 rounded-xl bg-primary-600 text-sm font-bold text-white hover:bg-primary-700 shadow-lg shadow-primary-600/20 transition-all">Simpan Jawaban</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function userSearchableSelect(config) {
            return {
                open: false,
                search: '',
                selectedId: config.selectedId,
                selectedName: config.selectedName,
                users: config.users,
                get filteredUsers() {
                    if (!this.search) return this.users;
                    return this.users.filter(u => 
                        u.name && u.name.toLowerCase().includes(this.search.toLowerCase())
                    );
                },
                selectUser(id, name) {
                    this.selectedId = id;
                    this.selectedName = name;
                    this.open = false;
                    const url = new URL(window.location.href);
                    url.searchParams.set('user_id', id);
                    window.location.href = url.toString();
                }
            }
        }

        function unansweredManager() {
            return {
                selectedIds: [],
                allSelected: false,
                questions: @json($questions->items()),
                toggleAll() {
                    if (this.allSelected) {
                        this.selectedIds = this.questions.map(q => q.id);
                    } else {
                        this.selectedIds = [];
                    }
                },
                async bulkDelete() {
                    if (!this.selectedIds.length) return;
                    if (!confirm(`Hapus ${this.selectedIds.length} pertanyaan terpilih?`)) return;
                    
                    try {
                        const response = await fetch('{{ route(Auth::user()->role . ".ai-agen.unanswered.bulk-delete") }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({ ids: this.selectedIds })
                        });
                        const result = await response.json();
                        if (result.success) {
                            window.location.reload();
                        } else {
                            alert(result.message);
                        }
                    } catch (error) {
                        alert('Terjadi kesalahan saat menghapus data.');
                    }
                }
            }
        }

        function openAnswerModal(id, question, answer) {
            const modal = document.getElementById('answerModal');
            const form = document.getElementById('answerForm');
            const qText = document.getElementById('modalQuestion');
            const aInput = document.getElementById('modalAnswer');
            
            const role = '{{ Auth::user()->role }}';
            form.action = `/${role}/ai-agen/unanswered/${id}`;
            
            qText.innerText = question;
            aInput.value = (answer && answer !== 'null') ? answer : '';
            
            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeAnswerModal() {
            document.getElementById('answerModal').classList.add('hidden');
            document.body.style.overflow = 'auto';
        }
    </script>
    @endpush
</x-aiagen-layout>
