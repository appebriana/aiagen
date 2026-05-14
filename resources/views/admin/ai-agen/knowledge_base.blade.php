<x-aiagen-layout>
    <x-slot name="header">Memory Otak AI (Dynamic Knowledge)</x-slot>

    <div x-data="knowledgeManager()" class="space-y-6 relative">
        
        {{-- ═══ HEADER ═══ --}}
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h2 class="text-xl font-bold text-secondary-900">Memory Otak AI</h2>
                <p class="text-sm text-secondary-500">Daftar pengetahuan yang dipelajari AI dari jawaban manual Admin.</p>
            </div>
        </div>

        {{-- ═══ FILTER SECTION ═══ --}}
        <div class="bg-white rounded-2xl shadow-sm border border-secondary-200 p-4 md:p-6">
            <form action="{{ route(Auth::user()->role . '.ai-agen.knowledge-base.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 items-end">
                @if(Auth::user()->role === 'admin')
                <div x-data='userSearchableSelect({ 
                        selectedId: @json(request('user_id')),
                        selectedName: @json(request('user_id') ? ($users->where('id', request('user_id'))->first()->name ?? "-- Pilih Akun --") : "-- Pilih Akun --"),
                        users: @json($users->map(fn($u) => ["id" => $u->id, "name" => $u->name]))
                    })' class="relative">
                    <label class="block text-[10px] font-bold text-secondary-500 uppercase tracking-widest mb-1.5">Pilih Akun Pengguna</label>
                    <input type="hidden" name="user_id" :value="selectedId">
                    <div @click="open = !open" class="w-full px-4 py-2.5 rounded-xl border border-secondary-200 bg-primary-50/30 flex items-center justify-between cursor-pointer hover:border-primary-500 transition-all shadow-sm">
                        <span class="text-sm font-bold text-primary-600 truncate" x-text="selectedName"></span>
                        <svg class="w-4 h-4 text-primary-400 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </div>
                    <div x-show="open" x-cloak style="display: none;" @click.away="open = false" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="absolute z-50 mt-2 w-full bg-white rounded-2xl shadow-2xl border border-secondary-100 overflow-hidden min-w-[240px]">
                        <div class="p-3 border-b border-secondary-50 bg-secondary-50/50">
                            <input type="text" x-model="search" placeholder="Cari nama pengguna..." class="w-full px-3 py-2 text-xs rounded-lg border-secondary-200 focus:border-primary-500 focus:ring-4 focus:ring-primary-500/10" @click.stop>
                        </div>
                        <div class="max-h-60 overflow-y-auto scrollbar-hide">
                            <template x-for="user in filteredUsers" :key="user.id">
                                <div @click="selectUser(user.id, user.name)" class="px-4 py-3 text-sm text-secondary-700 hover:bg-primary-50 hover:text-primary-600 cursor-pointer transition-colors flex items-center justify-between group">
                                    <span x-text="user.name" class="font-medium"></span>
                                    <svg x-show="selectedId == user.id" class="w-4 h-4 text-primary-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
                @endif
                <div class="lg:col-span-2">
                    <label class="block text-[10px] font-bold text-secondary-500 uppercase tracking-widest mb-1.5">Cari Pertanyaan/Jawaban</label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Ketik kata kunci..." class="w-full px-4 py-2.5 rounded-xl border-secondary-200 focus:border-primary-500 focus:ring-4 focus:ring-primary-500/10 text-sm">
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="flex-1 px-4 py-2.5 bg-secondary-900 text-white rounded-xl font-bold text-sm hover:bg-secondary-800 transition-all flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        Cari
                    </button>
                    <a href="{{ route(Auth::user()->role . '.ai-agen.knowledge-base.index') }}" class="px-4 py-2.5 bg-secondary-100 text-secondary-600 rounded-xl font-bold text-sm hover:bg-secondary-200 transition-all flex items-center justify-center">
                        Reset
                    </a>
                </div>
            </form>
        </div>

        {{-- ═══ LIST SECTION ═══ --}}
        <div class="space-y-4">
            <div class="hidden md:block bg-white rounded-2xl shadow-sm border border-secondary-200 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-indigo-50/30 border-b border-indigo-100">
                                <th class="px-6 py-4 w-10">
                                    <input type="checkbox" x-model="allSelected" @change="toggleAll()" class="rounded border-secondary-300 text-indigo-600 focus:ring-indigo-500">
                                </th>
                                <th class="px-6 py-4 text-xs font-bold text-secondary-500 uppercase tracking-wider">Pertanyaan</th>
                                <th class="px-6 py-4 text-xs font-bold text-secondary-500 uppercase tracking-wider">Jawaban Paten</th>
                                <th class="px-6 py-4 text-xs font-bold text-secondary-500 uppercase tracking-wider">Departemen</th>
                                <th class="px-6 py-4 text-xs font-bold text-secondary-500 uppercase tracking-wider text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-secondary-100">
                            @forelse($knowledge as $item)
                                <tr class="hover:bg-indigo-50/20 transition-colors" :class="selectedIds.includes({{ $item->id }}) ? 'bg-indigo-50' : ''">
                                    <td class="px-6 py-4">
                                        <input type="checkbox" value="{{ $item->id }}" x-model="selectedIds" class="rounded border-secondary-300 text-indigo-600 focus:ring-indigo-500">
                                    </td>
                                    <td class="px-6 py-4">
                                        <p class="text-sm font-bold text-secondary-900 line-clamp-2">{{ $item->question }}</p>
                                    </td>
                                    <td class="px-6 py-4">
                                        <p class="text-sm text-secondary-600 line-clamp-2">{{ $item->answer }}</p>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="px-2 py-0.5 bg-secondary-100 text-secondary-600 rounded text-[10px] font-bold uppercase tracking-wider">
                                            {{ $item->department->name }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-right space-x-2 whitespace-nowrap">
                                        <button type="button" 
                                            onclick="openEditModal({{ $item->id }}, '{{ addslashes($item->question) }}', '{{ addslashes($item->answer) }}')"
                                            class="p-2 rounded-xl text-indigo-600 hover:bg-indigo-50 transition-all"
                                            title="Edit Pengetahuan">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        </button>
                                        <form action="{{ route(Auth::user()->role . '.ai-agen.knowledge-base.destroy', $item->id) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-2 rounded-xl text-red-500 hover:bg-red-50 transition-all" onclick="return confirm('Hapus dari memori paten AI? Pengetahuan ini akan kembali menjadi riwayat biasa.')">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                @if(Auth::user()->role === 'admin' && !request('user_id'))
                                    <tr>
                                        <td colspan="5" class="px-6 py-20 text-center">
                                            <div class="flex flex-col items-center gap-3">
                                                <div class="w-16 h-16 bg-indigo-50 text-indigo-500 rounded-full flex items-center justify-center shadow-inner">
                                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                                </div>
                                                <p class="text-secondary-600 font-bold">Silakan pilih akun pengguna terlebih dahulu</p>
                                                <p class="text-xs text-secondary-400">Pilih salah satu akun pada filter di atas untuk melihat data memori AI.</p>
                                            </div>
                                        </td>
                                    </tr>
                                @else
                                    <tr>
                                        <td colspan="5" class="px-6 py-20 text-center text-secondary-400 italic">
                                            Belum ada pengetahuan yang dipatenkan.
                                        </td>
                                    </tr>
                                @endif
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            
            {{-- Mobile Cards --}}
            <div class="md:hidden space-y-4">
                @forelse($knowledge as $item)
                <div class="bg-white p-5 rounded-2xl border border-secondary-200 shadow-sm space-y-4">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <p class="text-[10px] font-bold text-indigo-600 uppercase mb-1">Q: {{ $item->department->name }}</p>
                            <h3 class="text-sm font-bold text-secondary-900 line-clamp-2">{{ $item->question }}</h3>
                        </div>
                        <input type="checkbox" value="{{ $item->id }}" x-model="selectedIds" class="rounded border-secondary-300 text-indigo-600">
                    </div>
                    <div class="bg-indigo-50/50 p-3 rounded-xl border border-indigo-100">
                        <p class="text-[10px] font-bold text-secondary-400 uppercase mb-1">A (Paten):</p>
                        <p class="text-sm text-secondary-700 line-clamp-3">{{ $item->answer }}</p>
                    </div>
                    <div class="flex items-center justify-end gap-2">
                        <button onclick="openEditModal({{ $item->id }}, '{{ addslashes($item->question) }}', '{{ addslashes($item->answer) }}')" class="px-4 py-2 bg-indigo-50 text-indigo-600 rounded-xl text-xs font-bold">Edit</button>
                        <form action="{{ route(Auth::user()->role . '.ai-agen.knowledge-base.destroy', $item->id) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="px-4 py-2 bg-red-50 text-red-500 rounded-xl text-xs font-bold" onclick="return confirm('Hapus?')">Hapus</button>
                        </form>
                    </div>
                </div>
                @empty
                    @if(Auth::user()->role === 'admin' && !request('user_id'))
                        <div class="bg-white p-12 rounded-2xl border border-secondary-200 text-center">
                            <p class="text-secondary-600 font-bold mb-2">Silakan pilih akun pengguna</p>
                            <p class="text-xs text-secondary-400">Gunakan filter di atas untuk melihat data.</p>
                        </div>
                    @else
                        <div class="bg-white p-12 rounded-2xl border border-secondary-200 text-center text-secondary-400 text-sm">
                            Belum ada pengetahuan yang dipatenkan.
                        </div>
                    @endif
                @endforelse
            </div>

            @if($knowledge->hasPages())
                <div class="px-6 py-4 bg-white rounded-2xl border border-secondary-200">
                    {{ $knowledge->links() }}
                </div>
            @endif
        </div>

        {{-- ═══ FLOATING BULK BAR ═══ --}}
        <div x-show="selectedIds.length > 0" x-cloak style="display: none;" class="fixed bottom-20 md:bottom-8 left-1/2 -translate-x-1/2 z-[90] w-[90%] md:w-auto">
            <div class="bg-indigo-900 text-white px-6 py-4 rounded-2xl shadow-2xl flex items-center gap-6 border border-white/10 backdrop-blur-md">
                <div class="flex items-center gap-3 border-r border-white/20 pr-6">
                    <span class="w-7 h-7 bg-indigo-500 rounded-full flex items-center justify-center text-xs font-bold" x-text="selectedIds.length"></span>
                    <span class="text-sm font-medium">Pengetahuan Terpilih</span>
                </div>
                <button @click="bulkRemove()" class="px-4 py-2 bg-red-500 hover:bg-red-600 text-white rounded-xl text-sm font-bold transition-all">Hapus Masal</button>
                <button @click="selectedIds = []; allSelected = false" class="text-xs text-indigo-300 hover:text-white transition-colors">Batal</button>
            </div>
        </div>
    </div>

    {{-- ═══ MODAL EDIT ═══ --}}
    <div id="editModal" class="fixed inset-0 z-[110] hidden overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="fixed inset-0 bg-secondary-900/60 backdrop-blur-sm" onclick="closeEditModal()"></div>
            <div class="relative bg-white rounded-3xl w-full max-w-xl shadow-2xl border border-secondary-200 overflow-hidden">
                <form id="editForm" method="POST">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="is_knowledge" value="1">
                    <div class="p-6 md:p-8">
                        <div class="flex items-center justify-between mb-8">
                            <h3 class="text-xl font-bold text-secondary-900">Edit Pengetahuan AI</h3>
                            <button type="button" onclick="closeEditModal()" class="text-secondary-400 hover:text-secondary-600"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
                        </div>
                        <div class="space-y-6">
                            <div>
                                <label class="block text-xs font-bold text-secondary-500 uppercase tracking-widest mb-2">Pertanyaan / Input User</label>
                                <textarea name="question" id="modalQuestion" rows="3" class="w-full px-4 py-3 rounded-2xl border-secondary-200 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 text-sm font-bold" required></textarea>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-secondary-500 uppercase tracking-widest mb-2">Jawaban Paten AI</label>
                                <textarea name="answer" id="modalAnswer" rows="6" class="w-full px-4 py-3 rounded-2xl border-secondary-200 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 text-sm" required></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="bg-secondary-50 px-8 py-4 flex flex-col sm:flex-row justify-end gap-3">
                        <button type="button" onclick="closeEditModal()" class="px-6 py-2.5 rounded-xl border border-secondary-200 bg-white text-sm font-bold text-secondary-600">Batal</button>
                        <button type="submit" class="px-8 py-2.5 rounded-xl bg-indigo-600 text-sm font-bold text-white shadow-lg shadow-indigo-500/20">Update Memori AI</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function knowledgeManager() {
            return {
                selectedIds: [],
                allSelected: false,
                knowledgeItems: @json($knowledge->items()),
                toggleAll() {
                    this.selectedIds = this.allSelected ? this.knowledgeItems.map(i => i.id) : [];
                },
                async bulkRemove() {
                    if (!confirm(`Hapus ${this.selectedIds.length} pengetahuan?`)) return;
                    try {
                        const response = await fetch('{{ route(Auth::user()->role . ".ai-agen.knowledge-base.bulk-remove") }}', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                            body: JSON.stringify({ ids: this.selectedIds })
                        });
                        const result = await response.json();
                        if (result.success) window.location.reload();
                    } catch (e) { alert('Gagal menghapus data.'); }
                }
            }
        }

        function openEditModal(id, question, answer) {
            const role = '{{ Auth::user()->role }}';
            document.getElementById('editForm').action = `/${role}/ai-agen/knowledge-base/${id}`;
            document.getElementById('modalQuestion').value = question;
            document.getElementById('modalAnswer').value = answer;
            document.getElementById('editModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeEditModal() {
            document.getElementById('editModal').classList.add('hidden');
            document.body.style.overflow = 'auto';
        }

        function userSearchableSelect(config) {
            return {
                open: false,
                search: '',
                selectedId: config.selectedId,
                selectedName: config.selectedName,
                users: config.users,
                get filteredUsers() {
                    return this.users.filter(u => u.name.toLowerCase().includes(this.search.toLowerCase()));
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
    </script>
    @endpush
</x-aiagen-layout>
