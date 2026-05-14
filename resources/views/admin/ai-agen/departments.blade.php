<x-aiagen-layout>
    <x-slot name="header">
        Manajemen Departemen AI
    </x-slot>

    <div class="space-y-6" x-data="{ 
        showCreateModal: false, 
        showEditModal: false,
        currentDept: { id: '', name: '', description: '', user_id: '', ai_name: '', ai_job_description: '', reply_to_groups: false, is_24_hours: true, open_time: '', close_time: '', is_csat_enabled: true, tone_of_voice: 'casual' },
        openEdit(dept) {
            this.currentDept = { 
                ...dept, 
                open_time: dept.open_time ? dept.open_time.substring(0, 5) : '', 
                close_time: dept.close_time ? dept.close_time.substring(0, 5) : '',
                tone_of_voice: dept.tone_of_voice || 'casual'
            };
            this.showEditModal = true;
        }
    }">
        {{-- Header & Action --}}
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h2 class="text-xl font-bold text-secondary-900">Daftar Departemen</h2>
                <p class="text-sm text-secondary-500">Kelola unit kerja dan pemisahan pengetahuan AI.</p>
            </div>
            <button @click="showCreateModal = true" 
                    class="w-full md:w-auto px-5 py-2.5 bg-primary-600 text-white text-sm font-bold rounded-xl hover:bg-primary-700 transition-all shadow-lg shadow-primary-500/20 flex items-center justify-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Tambah Departemen
            </button>
        </div>

        {{-- Filter Section (Admin Only) --}}
        @if(auth()->user()->isAdmin())
        <div class="bg-white rounded-2xl shadow-sm border border-secondary-200 p-4">
            <form action="{{ route('admin.ai-agen.departments.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-[10px] font-bold text-secondary-400 uppercase mb-1.5 ml-1">Cari Nama</label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari departemen..." 
                           class="w-full bg-secondary-50 border border-secondary-200 rounded-xl px-4 py-2 text-sm focus:ring-2 focus:ring-primary-500">
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-secondary-400 uppercase mb-1.5 ml-1">Filter Pemilik</label>
                    <select name="user_id" class="w-full bg-secondary-50 border border-secondary-200 rounded-xl px-4 py-2 text-sm focus:ring-2 focus:ring-primary-500">
                        <option value="">Semua Pemilik</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex items-end gap-2">
                    <button type="submit" class="flex-1 bg-secondary-900 text-white py-2 rounded-xl text-sm font-bold hover:bg-secondary-800 transition-colors">Filter</button>
                    <a href="{{ route('admin.ai-agen.departments.index') }}" class="px-4 py-2 bg-secondary-100 text-secondary-600 rounded-xl text-sm font-bold hover:bg-secondary-200 transition-colors text-center">Reset</a>
                </div>
            </form>
        </div>
        @endif

        {{-- Table/Card Section --}}
        <div class="space-y-4">
            {{-- Desktop Table --}}
            <div class="hidden md:block bg-white rounded-2xl shadow-sm border border-secondary-200 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-secondary-50/50 border-b border-secondary-100">
                                @if(auth()->user()->isAdmin())
                                <th class="px-6 py-4 text-xs font-bold text-secondary-500 uppercase tracking-wider">Pemilik</th>
                                @endif
                                <th class="px-6 py-4 text-xs font-bold text-secondary-500 uppercase tracking-wider">Nama Departemen</th>
                                <th class="px-6 py-4 text-xs font-bold text-secondary-500 uppercase tracking-wider text-center">Data</th>
                                <th class="px-6 py-4 text-xs font-bold text-secondary-500 uppercase tracking-wider text-center">AI Agent</th>
                                <th class="px-6 py-4 text-xs font-bold text-secondary-500 uppercase tracking-wider text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-secondary-100">
                            @forelse($departments as $dept)
                                <tr class="hover:bg-secondary-50/30 transition-colors group">
                                    @if(auth()->user()->isAdmin())
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center gap-2">
                                            <div class="w-7 h-7 rounded-full bg-primary-100 flex items-center justify-center text-primary-700 text-[10px] font-bold">
                                                {{ strtoupper(substr($dept->user->name, 0, 1)) }}
                                            </div>
                                            <span class="text-sm font-medium text-secondary-900">{{ $dept->user->name }}</span>
                                        </div>
                                    </td>
                                    @endif
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="text-sm font-bold text-secondary-900">{{ $dept->name }}</span>
                                        <p class="text-[10px] text-secondary-400">{{ $dept->description ?: 'Tanpa keterangan' }}</p>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center justify-center gap-4">
                                            <div class="text-center" title="Dokumen Knowledge">
                                                <span class="text-xs font-bold text-secondary-900">{{ $dept->knowledge_files_count }}</span>
                                                <p class="text-[8px] text-secondary-400 uppercase font-bold">Item</p>
                                            </div>
                                            <div class="text-center" title="Device WhatsApp">
                                                <span class="text-xs font-bold text-secondary-900">{{ $dept->whatsapp_devices_count }}</span>
                                                <p class="text-[8px] text-secondary-400 uppercase font-bold">Device</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <div class="flex flex-col items-center">
                                            <span class="text-[10px] font-bold text-primary-600">{{ $dept->ai_name ?: 'AI Agent' }}</span>
                                            @if($dept->reply_to_groups)
                                                <span class="px-1.5 py-0.5 bg-blue-100 text-blue-600 rounded text-[8px] font-bold uppercase mt-1">Group OK</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <div class="flex items-center justify-end gap-1">
                                            <button @click="openEdit({ 
                                                id: '{{ $dept->id }}', 
                                                name: '{{ $dept->name }}', 
                                                description: '{{ $dept->description }}', 
                                                user_id: '{{ $dept->user_id }}',
                                                ai_name: '{{ $dept->ai_name }}',
                                                ai_job_description: '{{ $dept->ai_job_description }}',
                                                reply_to_groups: {{ $dept->reply_to_groups ? 'true' : 'false' }},
                                                is_24_hours: {{ $dept->is_24_hours ? 'true' : 'false' }},
                                                open_time: '{{ $dept->open_time }}',
                                                close_time: '{{ $dept->close_time }}',
                                                is_csat_enabled: {{ $dept->is_csat_enabled ? 'true' : 'false' }},
                                                tone_of_voice: '{{ $dept->tone_of_voice }}'
                                            })" 
                                                    class="p-2 text-secondary-400 hover:text-primary-600 transition-colors">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                            </button>
                                            <form action="{{ auth()->user()->isAdmin() ? route('admin.ai-agen.departments.destroy', $dept) : route('pengguna.ai-agen.departments.destroy', $dept) }}" method="POST" onsubmit="return confirm('Hapus departemen ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="p-2 text-secondary-400 hover:text-red-500 transition-colors">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ auth()->user()->isAdmin() ? 5 : 4 }}" class="px-6 py-12 text-center text-secondary-400">
                                        Belum ada departemen yang terdaftar.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Mobile Card List --}}
            <div class="grid grid-cols-1 gap-4 md:hidden">
                @forelse($departments as $dept)
                    <div class="bg-white p-5 rounded-2xl border border-secondary-200 shadow-sm space-y-4">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <h3 class="text-base font-bold text-secondary-900">{{ $dept->name }}</h3>
                                <p class="text-xs text-secondary-500 mt-0.5 line-clamp-2">{{ $dept->description ?: 'Tanpa keterangan' }}</p>
                            </div>
                            <div class="flex items-center gap-1">
                                <button @click="openEdit({ 
                                    id: '{{ $dept->id }}', 
                                    name: '{{ $dept->name }}', 
                                    description: '{{ $dept->description }}', 
                                    user_id: '{{ $dept->user_id }}',
                                    ai_name: '{{ $dept->ai_name }}',
                                    ai_job_description: '{{ $dept->ai_job_description }}',
                                    reply_to_groups: {{ $dept->reply_to_groups ? 'true' : 'false' }},
                                    is_24_hours: {{ $dept->is_24_hours ? 'true' : 'false' }},
                                    open_time: '{{ $dept->open_time }}',
                                    close_time: '{{ $dept->close_time }}',
                                    is_csat_enabled: {{ $dept->is_csat_enabled ? 'true' : 'false' }},
                                    tone_of_voice: '{{ $dept->tone_of_voice }}'
                                })" 
                                        class="p-2 bg-primary-50 text-primary-600 rounded-lg">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                </button>
                                <form action="{{ auth()->user()->isAdmin() ? route('admin.ai-agen.departments.destroy', $dept) : route('pengguna.ai-agen.departments.destroy', $dept) }}" method="POST" onsubmit="return confirm('Hapus departemen ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2 bg-red-50 text-red-500 rounded-lg">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </form>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-3 pt-3 border-t border-secondary-100">
                            <div class="bg-secondary-50 p-2.5 rounded-xl text-center">
                                <p class="text-[9px] font-bold text-secondary-400 uppercase tracking-wider mb-1">Knowledge</p>
                                <span class="text-sm font-black text-secondary-900">{{ $dept->knowledge_files_count }} <span class="text-[10px] font-medium text-secondary-500">Item</span></span>
                            </div>
                            <div class="bg-secondary-50 p-2.5 rounded-xl text-center">
                                <p class="text-[9px] font-bold text-secondary-400 uppercase tracking-wider mb-1">WhatsApp</p>
                                <span class="text-sm font-black text-secondary-900">{{ $dept->whatsapp_devices_count }} <span class="text-[10px] font-medium text-secondary-500">Device</span></span>
                            </div>
                        </div>

                        <div class="flex items-center justify-between pt-1">
                            <div class="flex items-center gap-2">
                                <div class="w-6 h-6 rounded-full bg-primary-600 flex items-center justify-center text-[10px] font-bold text-white shadow-sm shadow-primary-600/30">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                </div>
                                <span class="text-xs font-bold text-primary-700">{{ $dept->ai_name ?: 'AI Agent' }}</span>
                            </div>
                            @if(auth()->user()->isAdmin())
                                <div class="flex items-center gap-1.5 bg-secondary-100 px-2.5 py-1 rounded-lg">
                                    <span class="text-[10px] font-medium text-secondary-600">Owner: <span class="font-bold">{{ $dept->user->name }}</span></span>
                                </div>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="bg-white p-12 rounded-2xl border border-secondary-200 text-center text-secondary-400 text-sm">
                        Belum ada departemen yang terdaftar.
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Create Modal --}}
        <div x-show="showCreateModal" class="fixed inset-0 z-[110] overflow-y-auto" x-cloak>
            <div class="flex items-center justify-center min-h-screen p-4 text-center">
                <div class="fixed inset-0 bg-secondary-900/60 transition-opacity" @click="showCreateModal = false"></div>
                <div class="inline-block align-middle bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:max-w-xl sm:w-full">
                    <form action="{{ auth()->user()->isAdmin() ? route('admin.ai-agen.departments.store') : route('pengguna.ai-agen.departments.store') }}" method="POST">
                        @csrf
                        <div class="px-5 py-5 sm:px-6 sm:py-6">
                            <h3 class="text-base sm:text-lg font-bold text-secondary-900 mb-4">Tambah Departemen Baru</h3>
                            <div class="space-y-4">
                                @if(auth()->user()->isAdmin())
                                <div>
                                    <label class="block text-[10px] font-bold text-secondary-700 uppercase mb-1.5 ml-1">Pemilik Departemen <span class="text-red-500">*</span></label>
                                    <select name="user_id" required class="w-full bg-secondary-50 border border-secondary-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary-500">
                                        <option value="">-- Pilih User --</option>
                                        @foreach($users as $user)
                                            <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->role }})</option>
                                        @endforeach
                                    </select>
                                </div>
                                @endif
                                <div>
                                    <label class="block text-[10px] font-bold text-secondary-700 uppercase mb-1.5 ml-1">Nama Departemen <span class="text-red-500">*</span></label>
                                    <input type="text" name="name" required placeholder="Contoh: Sales Team"
                                           class="w-full bg-secondary-50 border border-secondary-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary-500">
                                </div>
                                <div>
                                    <label class="block text-[10px] font-bold text-secondary-700 uppercase mb-1.5 ml-1">Keterangan</label>
                                    <textarea name="description" rows="2" placeholder="Jelaskan tugas departemen ini..."
                                              class="w-full bg-secondary-50 border border-secondary-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary-500"></textarea>
                                </div>
                                
                                {{-- Tone of Voice Selection --}}
                                <div class="space-y-3">
                                    <label class="block text-[10px] font-bold text-secondary-700 uppercase mb-1.5 ml-1">Gaya Bicara (Tone of Voice)</label>
                                    <div class="grid grid-cols-1 gap-2">
                                        <label class="relative flex flex-col p-3 border-2 rounded-2xl cursor-pointer transition-all" 
                                               :class="currentDept.tone_of_voice === 'casual' ? 'border-primary-500 bg-primary-50' : 'border-secondary-100 hover:border-secondary-200 bg-white'">
                                            <input type="radio" name="tone_of_voice" value="casual" x-model="currentDept.tone_of_voice" class="sr-only">
                                            <div class="flex items-center justify-between mb-1">
                                                <span class="text-xs font-bold text-secondary-900">😊 Casual (Santai & Akrab)</span>
                                                <svg x-show="currentDept.tone_of_voice === 'casual'" class="w-4 h-4 text-primary-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                            </div>
                                            <p class="text-[10px] text-secondary-500 italic bg-white/50 p-2 rounded-lg border border-secondary-100">
                                                "Halo Kak! 😊 Kabar gembira nih, stoknya masih ada! Mau langsung di-order? ✨"
                                            </p>
                                        </label>

                                        <label class="relative flex flex-col p-3 border-2 rounded-2xl cursor-pointer transition-all" 
                                               :class="currentDept.tone_of_voice === 'formal' ? 'border-primary-500 bg-primary-50' : 'border-secondary-100 hover:border-secondary-200 bg-white'">
                                            <input type="radio" name="tone_of_voice" value="formal" x-model="currentDept.tone_of_voice" class="sr-only">
                                            <div class="flex items-center justify-between mb-1">
                                                <span class="text-xs font-bold text-secondary-900">👔 Formal (Resmi & Sopan)</span>
                                                <svg x-show="currentDept.tone_of_voice === 'formal'" class="w-4 h-4 text-primary-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                            </div>
                                            <p class="text-[10px] text-secondary-500 italic bg-white/50 p-2 rounded-lg border border-secondary-100">
                                                "Selamat siang Bapak/Ibu. Terkait pertanyaan Anda, kami informasikan produk tersebut tersedia."
                                            </p>
                                        </label>

                                        <label class="relative flex flex-col p-3 border-2 rounded-2xl cursor-pointer transition-all" 
                                               :class="currentDept.tone_of_voice === 'technical' ? 'border-primary-500 bg-primary-50' : 'border-secondary-100 hover:border-secondary-200 bg-white'">
                                            <input type="radio" name="tone_of_voice" value="technical" x-model="currentDept.tone_of_voice" class="sr-only">
                                            <div class="flex items-center justify-between mb-1">
                                                <span class="text-xs font-bold text-secondary-900">⚙️ Teknis (Padat & Data)</span>
                                                <svg x-show="currentDept.tone_of_voice === 'technical'" class="w-4 h-4 text-primary-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                            </div>
                                            <p class="text-[10px] text-secondary-500 italic bg-white/50 p-2 rounded-lg border border-secondary-100">
                                                "Status Stok: Tersedia (12 unit). Kode SKU: SKU-992. Instruksi: Klik menu beli untuk memproses."
                                            </p>
                                        </label>
                                    </div>
                                </div>

                                <div class="flex items-center justify-between p-3 bg-indigo-50/50 rounded-xl border border-indigo-100">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 bg-indigo-100 text-indigo-600 rounded-lg flex items-center justify-center">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg>
                                        </div>
                                        <div>
                                            <p class="text-xs font-bold text-secondary-900">Aktifkan Analisis & Feedback</p>
                                            <p class="text-[10px] text-secondary-500">AI akan menganalisis sentimen & meminta rating.</p>
                                        </div>
                                    </div>
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" name="is_csat_enabled" value="1" checked class="sr-only peer">
                                        <div class="w-11 h-6 bg-secondary-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-secondary-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="bg-secondary-50 px-6 py-4 pb-10 sm:pb-4 flex justify-end gap-3">
                            <button type="button" @click="showCreateModal = false" class="text-sm font-bold text-secondary-500">Batal</button>
                            <button type="submit" class="px-6 py-2 bg-primary-600 text-white text-sm font-bold rounded-xl hover:bg-primary-700">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Edit & Settings Modal --}}
        <div x-show="showEditModal" class="fixed inset-0 z-[110] overflow-y-auto" x-cloak>
            <div class="flex items-center justify-center min-h-screen p-4 text-center">
                <div class="fixed inset-0 bg-secondary-900/60 transition-opacity" @click="showEditModal = false"></div>
                <div class="inline-block align-middle bg-white rounded-2xl text-left shadow-xl transform transition-all sm:max-w-xl sm:w-full">
                    <form :action="('{{ auth()->user()->isAdmin() ? route('admin.ai-agen.departments.update', ':id') : route('pengguna.ai-agen.departments.update', ':id') }}').replace(':id', currentDept.id)" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="max-h-[85vh] overflow-y-auto">
                            <div class="px-5 py-5 sm:px-6 sm:py-6">
                                <h3 class="text-base sm:text-lg font-bold text-secondary-900 mb-6 flex items-center gap-2">
                                    <div class="p-2 bg-primary-100 rounded-lg">
                                        <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                    </div>
                                    <span>Pengaturan Departemen & AI</span>
                                </h3>
                                
                                <div class="space-y-6">
                                    {{-- Basic Info Section --}}
                                    <div class="space-y-4">
                                        <h4 class="text-[10px] font-bold text-secondary-400 uppercase tracking-widest border-b border-secondary-100 pb-1">Informasi Dasar</h4>
                                        @if(auth()->user()->isAdmin())
                                        <div>
                                            <label class="block text-xs font-bold text-secondary-700 uppercase mb-2">Pemilik <span class="text-red-500">*</span></label>
                                            <select name="user_id" x-model="currentDept.user_id" required class="w-full bg-secondary-50 border border-secondary-200 rounded-xl px-4 py-2 text-sm">
                                                @foreach($users as $user)
                                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        @endif
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            <div>
                                                <label class="block text-xs font-bold text-secondary-700 uppercase mb-2">Nama Departemen <span class="text-red-500">*</span></label>
                                                <input type="text" name="name" x-model="currentDept.name" required
                                                       class="w-full bg-secondary-50 border border-secondary-200 rounded-xl px-4 py-2 text-sm">
                                            </div>
                                            <div>
                                                <label class="block text-xs font-bold text-secondary-700 uppercase mb-2">Keterangan</label>
                                                <input type="text" name="description" x-model="currentDept.description"
                                                       class="w-full bg-secondary-50 border border-secondary-200 rounded-xl px-4 py-2 text-sm">
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Tone of Voice Selection Section --}}
                                    <div class="space-y-4 p-4 bg-indigo-50/30 rounded-2xl border border-indigo-100">
                                        <h4 class="text-[10px] font-bold text-indigo-600 uppercase tracking-widest border-b border-indigo-100 pb-1">Gaya Bicara (Tone of Voice)</h4>
                                        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                                            <label class="relative flex flex-col p-3 border-2 rounded-2xl cursor-pointer transition-all" 
                                                   :class="currentDept.tone_of_voice === 'casual' ? 'border-indigo-500 bg-white shadow-md' : 'border-secondary-100 hover:border-secondary-200 bg-white/50'">
                                                <input type="radio" name="tone_of_voice" value="casual" x-model="currentDept.tone_of_voice" class="sr-only">
                                                <div class="flex items-center justify-between mb-2">
                                                    <span class="text-xs font-black text-secondary-900">😊 Casual</span>
                                                    <svg x-show="currentDept.tone_of_voice === 'casual'" class="w-4 h-4 text-indigo-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                                </div>
                                                <p class="text-[9px] text-secondary-500 italic bg-secondary-50 p-2 rounded-lg border border-secondary-100 leading-relaxed">
                                                    "Halo Kak! 😊 Stok masih ready nih. Mau langsung di-order? ✨"
                                                </p>
                                            </label>

                                            <label class="relative flex flex-col p-3 border-2 rounded-2xl cursor-pointer transition-all" 
                                                   :class="currentDept.tone_of_voice === 'formal' ? 'border-indigo-500 bg-white shadow-md' : 'border-secondary-100 hover:border-secondary-200 bg-white/50'">
                                                <input type="radio" name="tone_of_voice" value="formal" x-model="currentDept.tone_of_voice" class="sr-only">
                                                <div class="flex items-center justify-between mb-2">
                                                    <span class="text-xs font-black text-secondary-900">👔 Formal</span>
                                                    <svg x-show="currentDept.tone_of_voice === 'formal'" class="w-4 h-4 text-indigo-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                                </div>
                                                <p class="text-[9px] text-secondary-500 italic bg-secondary-50 p-2 rounded-lg border border-secondary-100 leading-relaxed">
                                                    "Selamat siang Bapak/Ibu. Produk tersebut saat ini tersedia."
                                                </p>
                                            </label>

                                            <label class="relative flex flex-col p-3 border-2 rounded-2xl cursor-pointer transition-all" 
                                                   :class="currentDept.tone_of_voice === 'technical' ? 'border-indigo-500 bg-white shadow-md' : 'border-secondary-100 hover:border-secondary-200 bg-white/50'">
                                                <input type="radio" name="tone_of_voice" value="technical" x-model="currentDept.tone_of_voice" class="sr-only">
                                                <div class="flex items-center justify-between mb-2">
                                                    <span class="text-xs font-black text-secondary-900">⚙️ Teknis</span>
                                                    <svg x-show="currentDept.tone_of_voice === 'technical'" class="w-4 h-4 text-indigo-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                                </div>
                                                <p class="text-[9px] text-secondary-500 italic bg-secondary-50 p-2 rounded-lg border border-secondary-100 leading-relaxed">
                                                    "Status: Tersedia (12 unit). SKU: 992. Klik beli untuk proses."
                                                </p>
                                            </label>
                                        </div>
                                    </div>

                                    {{-- AI Configuration Section --}}
                                    <div class="space-y-4 p-4 bg-primary-50/50 rounded-2xl border border-primary-100">
                                        <h4 class="text-[10px] font-bold text-primary-600 uppercase tracking-widest border-b border-primary-100 pb-1">Konfigurasi AI Agent</h4>
                                        <div>
                                            <label class="block text-xs font-bold text-secondary-700 uppercase mb-2">Nama AI Agent</label>
                                            <input type="text" name="ai_name" x-model="currentDept.ai_name" placeholder="Contoh: Budi (Sales Assistant)"
                                                   class="w-full bg-white border border-secondary-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary-500">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-bold text-secondary-700 uppercase mb-2">Deskripsi Pekerjaan AI (System Prompt)</label>
                                            <textarea name="ai_job_description" x-model="currentDept.ai_job_description" rows="4" 
                                                      placeholder="Contoh: Anda adalah asisten penjualan yang ramah. Tugas Anda adalah membantu pelanggan memilih produk..."
                                                      class="w-full bg-white border border-secondary-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary-500"></textarea>
                                        </div>
                                        <div class="flex items-center justify-between p-3 bg-white rounded-xl border border-secondary-200">
                                            <div class="flex items-center gap-3">
                                                <div class="w-8 h-8 bg-blue-100 text-blue-600 rounded-lg flex items-center justify-center">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                                </div>
                                                <div>
                                                    <p class="text-xs font-bold text-secondary-900">Izinkan AI Balas di Grup</p>
                                                    <p class="text-[10px] text-secondary-500">AI hanya akan membalas jika dimention / nama dipanggil.</p>
                                                </div>
                                            </div>
                                            <label class="relative inline-flex items-center cursor-pointer">
                                                <input type="checkbox" name="reply_to_groups" x-model="currentDept.reply_to_groups" value="1" class="sr-only peer">
                                                <div class="w-11 h-6 bg-secondary-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-secondary-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary-600"></div>
                                            </label>
                                        </div>
                                    </div>

                                    {{-- Operational Hours Section --}}
                                    <div class="space-y-4 p-4 bg-orange-50/50 rounded-2xl border border-orange-100">
                                        <h4 class="text-[10px] font-bold text-orange-600 uppercase tracking-widest border-b border-orange-100 pb-1">Jam Operasional AI</h4>
                                        
                                        <div class="flex items-center justify-between p-3 bg-white rounded-xl border border-secondary-200">
                                            <div class="flex items-center gap-3">
                                                <div class="w-8 h-8 bg-orange-100 text-orange-600 rounded-lg flex items-center justify-center">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                </div>
                                                <div>
                                                    <p class="text-xs font-bold text-secondary-900">Aktif 24 Jam</p>
                                                    <p class="text-[10px] text-secondary-500">AI akan membalas kapan saja tanpa henti.</p>
                                                </div>
                                            </div>
                                            <label class="relative inline-flex items-center cursor-pointer">
                                                <input type="checkbox" name="is_24_hours" x-model="currentDept.is_24_hours" value="1" class="sr-only peer">
                                                <div class="w-11 h-6 bg-secondary-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left._auth:bg-white after:border-secondary-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-orange-500"></div>
                                            </label>
                                        </div>

                                        <div x-show="!currentDept.is_24_hours" x-transition class="grid grid-cols-2 gap-4">
                                            <div>
                                                <label class="block text-[10px] font-bold text-secondary-500 uppercase mb-2 ml-1">Jam Buka <span class="text-red-500">*</span></label>
                                                <input type="time" name="open_time" x-model="currentDept.open_time" 
                                                       class="w-full bg-white border border-secondary-200 rounded-xl px-4 py-2 text-sm focus:ring-2 focus:ring-orange-500">
                                            </div>
                                            <div>
                                                <label class="block text-[10px] font-bold text-secondary-500 uppercase mb-2 ml-1">Jam Tutup <span class="text-red-500">*</span></label>
                                                <input type="time" name="close_time" x-model="currentDept.close_time" 
                                                       class="w-full bg-white border border-secondary-200 rounded-xl px-4 py-2 text-sm focus:ring-2 focus:ring-orange-500">
                                            </div>
                                            <p class="col-span-2 text-[10px] text-orange-600 italic">* Di luar jam ini, AI tidak akan memberikan jawaban otomatis.</p>
                                        </div>
                                    </div>

                                    {{-- CSAT Configuration Section --}}
                                    <div class="space-y-4 p-4 bg-indigo-50/50 rounded-2xl border border-indigo-100">
                                        <h4 class="text-[10px] font-bold text-indigo-600 uppercase tracking-widest border-b border-indigo-100 pb-1">Analisis & Feedback</h4>
                                        
                                        <div class="flex items-center justify-between p-3 bg-white rounded-xl border border-secondary-200">
                                            <div class="flex items-center gap-3">
                                                <div class="w-8 h-8 bg-indigo-100 text-indigo-600 rounded-lg flex items-center justify-center">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
                                                </div>
                                                <div>
                                                    <p class="text-xs font-bold text-secondary-900">Aktifkan CSAT & Sentiment</p>
                                                    <p class="text-[10px] text-secondary-500">AI akan meminta konfirmasi terjawab & rating 1-5.</p>
                                                </div>
                                            </div>
                                            <label class="relative inline-flex items-center cursor-pointer">
                                                <input type="checkbox" name="is_csat_enabled" x-model="currentDept.is_csat_enabled" value="1" class="sr-only peer">
                                                <div class="w-11 h-6 bg-secondary-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-secondary-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="bg-secondary-50 px-6 py-4 pb-10 sm:pb-4 flex justify-end gap-3">
                            <button type="button" @click="showEditModal = false" class="text-sm font-bold text-secondary-500">Batal</button>
                            <button type="submit" class="px-6 py-2 bg-primary-600 text-white text-sm font-bold rounded-xl hover:bg-primary-700 shadow-lg shadow-primary-500/20">Simpan Perubahan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-aiagen-layout>
