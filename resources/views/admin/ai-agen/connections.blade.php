<x-aiagen-layout>
    <x-slot name="header">
        Integrasi Connection
    </x-slot>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>

    <div class="space-y-6" x-data="connectionManager()">
        <!-- Admin Filters -->
        @if(auth()->user()->isAdmin())
        <div class="bg-white rounded-2xl shadow-sm border border-secondary-200 p-4 md:p-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
                <!-- User Filter -->
                <div x-data='userSearchableSelect({ 
                        selectedId: @json(request('user_id')),
                        selectedName: @json(request('user_id') ? ($users->where('id', request('user_id'))->first()->name ?? "-- Semua Pengguna --") : "-- Semua Pengguna --"),
                        users: {!! json_encode($users->map(fn($u) => ["id" => $u->id, "name" => $u->name])) !!}
                    })' class="relative">
                    <label class="block text-[10px] font-bold text-secondary-500 uppercase tracking-widest mb-1.5">Pilih Akun Pengguna</label>
                    <div @click="open = !open" 
                         class="w-full px-4 py-2.5 rounded-xl border border-secondary-200 bg-primary-50/30 flex items-center justify-between cursor-pointer hover:border-primary-500 transition-all shadow-sm">
                        <span class="text-sm font-bold text-primary-600 truncate" x-text="selectedName"></span>
                        <svg class="w-4 h-4 text-primary-400 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </div>

                    <div x-show="open" 
                         x-cloak
                         style="display: none;"
                         @click.away="open = false"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 translate-y-2"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         class="absolute z-[105] mt-2 w-full bg-white rounded-2xl shadow-2xl border border-secondary-100 overflow-hidden min-w-[240px]">
                        
                        <div class="p-3 border-b border-secondary-50 bg-secondary-50/50">
                            <input type="text" 
                                   x-model="search" 
                                   placeholder="Cari nama pengguna..." 
                                   class="w-full px-3 py-2 text-xs rounded-lg border-secondary-200 focus:border-primary-500 focus:ring-4 focus:ring-primary-500/10"
                                   @click.stop>
                        </div>

                        <div class="max-h-60 overflow-y-auto scrollbar-hide">
                            <div @click="selectUser('', '-- Semua Pengguna --')" 
                                 class="px-4 py-3 text-sm text-secondary-500 hover:bg-primary-50 hover:text-primary-600 cursor-pointer transition-colors flex items-center justify-between group">
                                <span class="font-medium">-- Semua Pengguna --</span>
                            </div>
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

                <!-- Department Filter -->
                <div x-data='deptSearchableSelect({ 
                        selectedId: @json(request('department_id')),
                        selectedName: @json(request('department_id') ? ($filterDepartments->where('id', request('department_id'))->first()->name ?? "-- Semua Departemen --") : "-- Semua Departemen --"),
                        depts: {!! json_encode($filterDepartments->map(fn($d) => ["id" => $d->id, "name" => $d->name])) !!}
                    })' class="relative">
                    <label class="block text-[10px] font-bold text-secondary-500 uppercase tracking-widest mb-1.5">Pilih Departemen</label>
                    <div @click="open = !open" 
                         class="w-full px-4 py-2.5 rounded-xl border border-secondary-200 bg-primary-50/30 flex items-center justify-between cursor-pointer hover:border-primary-500 transition-all shadow-sm">
                        <span class="text-sm font-bold text-primary-600 truncate" x-text="selectedName"></span>
                        <svg class="w-4 h-4 text-primary-400 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </div>

                    <div x-show="open" 
                         x-cloak
                         style="display: none;"
                         @click.away="open = false"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 translate-y-2"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         class="absolute z-[105] mt-2 w-full bg-white rounded-2xl shadow-2xl border border-secondary-100 overflow-hidden min-w-[240px]">
                        
                        <div class="p-3 border-b border-secondary-50 bg-secondary-50/50">
                            <input type="text" 
                                   x-model="search" 
                                   placeholder="Cari departemen..." 
                                   class="w-full px-3 py-2 text-xs rounded-lg border-secondary-200 focus:border-primary-500 focus:ring-4 focus:ring-primary-500/10"
                                   @click.stop>
                        </div>

                        <div class="max-h-60 overflow-y-auto scrollbar-hide">
                            <div @click="selectDept('', '-- Semua Departemen --')" 
                                 class="px-4 py-3 text-sm text-secondary-500 hover:bg-primary-50 hover:text-primary-600 cursor-pointer transition-colors flex items-center justify-between group">
                                <span class="font-medium">-- Semua Departemen --</span>
                            </div>
                            <template x-for="d in filteredDepts" :key="d.id">
                                <div @click="selectDept(d.id, d.name)" 
                                     class="px-4 py-3 text-sm text-secondary-700 hover:bg-primary-50 hover:text-primary-600 cursor-pointer transition-colors flex items-center justify-between group">
                                    <span x-text="d.name" class="font-medium"></span>
                                    <svg x-show="selectedId == d.id" class="w-4 h-4 text-primary-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                </div>
                            </template>
                            <div x-show="filteredDepts.length === 0" class="px-4 py-8 text-center text-xs text-secondary-400 italic">
                                Departemen tidak ditemukan
                            </div>
                        </div>
                    </div>
                </div>
                
                @if(request('user_id') || request('department_id'))
                <div>
                    <a href="{{ route(auth()->user()->role . '.ai-agen.connections.index', ['tab' => request('tab', 'whatsapp')]) }}" 
                       class="px-5 py-2.5 bg-red-100 hover:bg-red-200 text-red-700 text-xs font-bold rounded-xl transition-all shadow-sm inline-flex items-center gap-2">
                        Reset Filter
                    </a>
                </div>
                @endif
            </div>
        </div>
        @endif

        <!-- Tab Navigation -->
        <div class="bg-white rounded-2xl shadow-sm border border-secondary-200 p-2 flex gap-2 overflow-x-auto [scrollbar-width:none] [-ms-overflow-style:none] [&::-webkit-scrollbar]:display-none">
            @foreach(['whatsapp' => 'WhatsApp', 'livechat' => 'Live Chat', 'telegram' => 'Telegram', 'facebook' => 'Facebook', 'instagram' => 'Instagram', 'tiktok' => 'TikTok'] as $id => $label)
                <button @click="tab = '{{ $id }}'" 
                        :class="tab === '{{ $id }}' ? 'bg-primary-600 text-white shadow-lg shadow-primary-500/20' : 'text-secondary-500 hover:bg-secondary-50'"
                        class="px-6 py-2.5 rounded-xl text-sm font-bold transition-all whitespace-nowrap">
                    {{ $label }}
                </button>
            @endforeach
        </div>

        <!-- WhatsApp Content -->
        <div x-show="tab === 'whatsapp'" x-cloak class="space-y-6">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h2 class="text-xl font-bold text-secondary-900">Perangkat WhatsApp</h2>
                    <p class="text-sm text-secondary-500">Kelola koneksi nomor WhatsApp ke masing-masing departemen.</p>
                </div>
                <button @click="openAddModal()" 
                        class="px-5 py-2.5 bg-green-600 text-white text-sm font-bold rounded-xl hover:bg-green-700 transition-all shadow-lg shadow-green-500/20 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Tambah Perangkat
                </button>
            </div>

            @if(auth()->user()->isAdmin())
                {{-- Admin View Section --}}
                <div class="space-y-4">
                    {{-- Desktop Table --}}
                    <div class="hidden md:block bg-white rounded-2xl shadow-sm border border-secondary-200 overflow-hidden">
                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse">
                                <thead>
                                    <tr class="bg-secondary-50/50 border-b border-secondary-100">
                                        <th class="px-6 py-4 text-xs font-bold text-secondary-500 uppercase tracking-wider">Pemilik</th>
                                        <th class="px-6 py-4 text-xs font-bold text-secondary-500 uppercase tracking-wider">Nama Perangkat</th>
                                        <th class="px-6 py-4 text-xs font-bold text-secondary-500 uppercase tracking-wider">Departemen</th>
                                        <th class="px-6 py-4 text-xs font-bold text-secondary-500 uppercase tracking-wider">Nomor</th>
                                        <th class="px-6 py-4 text-xs font-bold text-secondary-500 uppercase tracking-wider text-center">Status</th>
                                        <th class="px-6 py-4 text-xs font-bold text-secondary-500 uppercase tracking-wider text-right">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-secondary-100">
                                    @forelse($whatsappDevices as $device)
                                        <tr class="hover:bg-secondary-50/30 transition-colors group">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="flex items-center gap-2">
                                                    <div class="w-7 h-7 rounded-full bg-primary-100 flex items-center justify-center text-primary-700 text-[10px] font-bold">
                                                        {{ strtoupper(substr($device->user->name, 0, 1)) }}
                                                    </div>
                                                    <span class="text-sm font-medium text-secondary-900">{{ $device->user->name }}</span>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="text-sm font-bold text-secondary-900">{{ $device->name }}</span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="px-2 py-1 bg-secondary-100 text-secondary-600 rounded text-[10px] font-bold uppercase">
                                                    {{ $device->department->name }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="text-xs text-secondary-500 font-mono">{{ $device->phone_number ?: '-' }}</span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                                <span class="px-2 py-1 {{ $device->status == 'connected' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }} rounded text-[10px] font-bold uppercase tracking-wider">
                                                    {{ $device->status }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 text-right">
                                                <div class="flex items-center justify-end gap-2">
                                                    @if($device->status == 'disconnected')
                                                        <button @click="fetchQR({{ $device->id }})" class="p-1.5 bg-primary-50 text-primary-600 rounded-lg hover:bg-primary-100 transition-colors" title="Hubungkan">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
                                                        </button>
                                                    @else
                                                        <form action="{{ route('admin.ai-agen.connections.whatsapp.disconnect', $device) }}" method="POST" onsubmit="return confirm('Putuskan koneksi WhatsApp ini?')">
                                                            @csrf
                                                            <button type="submit" class="p-1.5 bg-orange-50 text-orange-600 rounded-lg hover:bg-orange-100 transition-colors" title="Putuskan Koneksi">
                                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                                                            </button>
                                                        </form>
                                                    @endif
                                                    <form action="{{ route('admin.ai-agen.connections.whatsapp.destroy', $device) }}" method="POST" onsubmit="return confirm('Hapus perangkat ini?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="p-1.5 text-secondary-400 hover:text-red-500 transition-colors">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="px-6 py-12 text-center text-secondary-400">Belum ada perangkat WhatsApp.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- Mobile Card List --}}
                    <div class="grid grid-cols-1 gap-4 md:hidden">
                        @forelse($whatsappDevices as $device)
                            <div class="bg-white p-5 rounded-2xl border border-secondary-200 shadow-sm space-y-4">
                                <div class="flex items-start justify-between">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-xl bg-green-50 text-green-600 flex items-center justify-center">
                                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.414 0 .018 5.394 0 12.03a11.81 11.81 0 001.576 5.922L0 24l6.117-1.605a11.803 11.803 0 005.925 1.583h.005c6.635 0 12.032-5.393 12.035-12.029a11.79 11.79 0 00-3.526-8.508z"/></svg>
                                        </div>
                                        <div>
                                            <h4 class="text-sm font-bold text-secondary-900">{{ $device->name }}</h4>
                                            <p class="text-[10px] text-secondary-500 font-mono">{{ $device->phone_number ?: 'Nomor belum terdeteksi' }}</p>
                                        </div>
                                    </div>
                                    <span class="px-2 py-1 {{ $device->status == 'connected' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }} rounded text-[9px] font-bold uppercase tracking-wider">
                                        {{ $device->status }}
                                    </span>
                                </div>

                                <div class="grid grid-cols-2 gap-3 pt-3 border-t border-secondary-100">
                                    <div class="bg-secondary-50 p-2.5 rounded-xl">
                                        <p class="text-[8px] font-bold text-secondary-400 uppercase tracking-widest mb-0.5">Departemen</p>
                                        <p class="text-xs font-bold text-secondary-900 truncate">{{ $device->department->name }}</p>
                                    </div>
                                    <div class="bg-secondary-50 p-2.5 rounded-xl">
                                        <p class="text-[8px] font-bold text-secondary-400 uppercase tracking-widest mb-0.5">Pemilik</p>
                                        <p class="text-xs font-bold text-secondary-900 truncate">{{ $device->user->name }}</p>
                                    </div>
                                </div>

                                <div class="flex items-center justify-end gap-2 pt-1">
                                    @if($device->status == 'disconnected')
                                        <button @click="fetchQR({{ $device->id }})" class="flex-1 py-2 bg-primary-600 text-white text-[11px] font-bold rounded-xl hover:bg-primary-700 transition-all flex items-center justify-center gap-2">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
                                            Hubungkan
                                        </button>
                                    @else
                                        <form action="{{ route('admin.ai-agen.connections.whatsapp.disconnect', $device) }}" method="POST" onsubmit="return confirm('Putuskan koneksi WhatsApp ini?')" class="flex-1">
                                            @csrf
                                            <button type="submit" class="w-full py-2 bg-orange-100 text-orange-600 text-[11px] font-bold rounded-xl hover:bg-orange-200 transition-all flex items-center justify-center gap-2">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                                                Putus Koneksi
                                            </button>
                                        </form>
                                    @endif
                                    <form action="{{ route('admin.ai-agen.connections.whatsapp.destroy', $device) }}" method="POST" onsubmit="return confirm('Hapus perangkat ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-2 bg-red-50 text-red-500 rounded-xl hover:bg-red-100 transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @empty
                            <div class="bg-white p-12 rounded-2xl border border-secondary-200 text-center text-secondary-400 text-sm">
                                Belum ada perangkat WhatsApp ditambahkan.
                            </div>
                        @endforelse
                    </div>
                </div>
            @else
                {{-- User View Section (Optimized) --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 md:gap-6">
                    @forelse($whatsappDevices as $device)
                        <div class="bg-white rounded-2xl shadow-sm border border-secondary-200 overflow-hidden group hover:shadow-lg transition-all relative">
                            <div class="p-5 md:p-6">
                                <div class="flex items-center justify-between mb-4 md:mb-6">
                                    <div class="w-10 h-10 md:w-12 md:h-12 bg-green-100 text-green-600 rounded-xl md:rounded-2xl flex items-center justify-center">
                                        <svg class="w-6 h-6 md:w-7 md:h-7" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.414 0 .018 5.394 0 12.03a11.81 11.81 0 001.576 5.922L0 24l6.117-1.605a11.803 11.803 0 005.925 1.583h.005c6.635 0 12.032-5.393 12.035-12.029a11.79 11.79 0 00-3.526-8.508z"/></svg>
                                    </div>
                                    <div class="text-right">
                                        <span class="px-2.5 py-1 {{ $device->status == 'connected' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }} rounded-lg text-[10px] font-bold uppercase tracking-wider">
                                            {{ $device->status }}
                                        </span>
                                    </div>
                                </div>
                                <h3 class="text-base md:text-lg font-bold text-secondary-900 mb-1 truncate">{{ $device->name }}</h3>
                                <p class="text-xs text-secondary-500 mb-4">Departemen: <span class="font-bold text-primary-600">{{ $device->department->name }}</span></p>
                                
                                <div class="space-y-3">
                                    @if($device->status == 'disconnected')
                                        <button @click="fetchQR({{ $device->id }})" class="w-full py-2.5 bg-primary-600 text-white text-xs font-bold rounded-xl hover:bg-primary-700 transition-all shadow-lg shadow-primary-500/20 flex items-center justify-center gap-2">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                            Hubungkan (Scan QR)
                                        </button>
                                    @else
                                        <form action="{{ route('pengguna.ai-agen.connections.whatsapp.disconnect', $device) }}" method="POST" onsubmit="return confirm('Putuskan koneksi WhatsApp ini?')">
                                            @csrf
                                            <button type="submit" class="w-full py-2.5 bg-orange-500 text-white text-xs font-bold rounded-xl hover:bg-orange-600 transition-all shadow-lg shadow-orange-500/20 flex items-center justify-center gap-2">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                                                Putuskan Koneksi
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                            <div class="px-5 md:px-6 py-3 bg-secondary-50 border-t border-secondary-100 flex items-center justify-between">
                                <span class="text-[10px] text-secondary-400 font-bold uppercase tracking-wider">{{ $device->phone_number ?: 'Nomor Belum Terdeteksi' }}</span>
                                <form action="{{ route('pengguna.ai-agen.connections.whatsapp.destroy', $device) }}" method="POST" onsubmit="return confirm('Hapus perangkat ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-secondary-300 hover:text-red-500 transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </form>
                            </div>
                        </div>
                    @empty
                        <div class="sm:col-span-2 lg:col-span-3 py-16 text-center bg-white rounded-3xl border-2 border-dashed border-secondary-100">
                            <div class="w-16 h-16 bg-secondary-50 text-secondary-300 rounded-2xl flex items-center justify-center mx-auto mb-4">
                                <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 24 24"><path d="M12.05 0C5.414 0 .018 5.394 0 12.03a11.81 11.81 0 001.576 5.922L0 24l6.117-1.605a11.803 11.803 0 005.925 1.583h.005c6.635 0 12.032-5.393 12.035-12.029a11.79 11.79 0 00-3.526-8.508A11.815 11.815 0 0012.05 0z"/></svg>
                            </div>
                            <p class="text-secondary-400 font-bold uppercase tracking-widest text-xs">Belum ada perangkat WhatsApp ditambahkan.</p>
                        </div>
                    @endforelse
                </div>
            @endif
        </div>

        <!-- Live Chat Content -->
        <div x-show="tab === 'livechat'" x-cloak class="space-y-6">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h2 class="text-xl font-bold text-secondary-900">Widget Live Chat Website</h2>
                    <p class="text-sm text-secondary-500">Pasang widget chat di website Anda agar pengunjung dapat berkomunikasi langsung dengan AI Agent.</p>
                </div>
                <button @click="openAddLivechatModal()" 
                        class="px-5 py-2.5 bg-indigo-600 text-white text-sm font-bold rounded-xl hover:bg-indigo-700 transition-all shadow-lg shadow-indigo-500/20 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Tambah Widget Live Chat
                </button>
            </div>

            <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                @forelse($livechatWidgets as $widget)
                    <div class="bg-white rounded-2xl shadow-sm border border-secondary-200 overflow-hidden flex flex-col">
                        <div class="p-6 flex-1 space-y-4">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-indigo-50 text-indigo-600 rounded-xl flex items-center justify-center">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                                    </div>
                                    <div>
                                        <h3 class="font-bold text-secondary-900">{{ $widget->name }}</h3>
                                        <p class="text-xs text-secondary-500">Departemen: <span class="font-bold text-primary-600">{{ $widget->department->name }}</span></p>
                                        <div class="mt-1 flex items-center gap-1.5">
                                            @if($widget->target_domain)
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-green-50 text-green-700 text-[10px] font-medium rounded-full border border-green-200">
                                                    <svg class="w-3 h-3 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
                                                    Tersambung ke: {{ parse_url($widget->target_domain, PHP_URL_HOST) ?? $widget->target_domain }}
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-yellow-50 text-yellow-700 text-[10px] font-medium rounded-full border border-yellow-200">
                                                    <svg class="w-3 h-3 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                                    Dapat digunakan di semua website
                                                </span>
                                            @endif
                                        </div>
                                        <p class="text-[10px] text-secondary-400 mt-1">Pemilik: {{ $widget->user->name ?? 'System' }}</p>
                                    </div>
                                </div>
                                <span class="px-2.5 py-1 {{ $widget->is_active ? 'bg-green-100 text-green-700' : 'bg-secondary-100 text-secondary-500' }} rounded-lg text-[10px] font-bold uppercase">
                                    {{ $widget->is_active ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </div>

                            <form action="{{ route(auth()->user()->role . '.ai-agen.connections.livechat.update', $widget) }}" method="POST" class="space-y-4 pt-2 border-t border-secondary-100">
                                @csrf
                                @method('PUT')
                                
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-[10px] font-bold text-secondary-700 uppercase mb-1.5">Nama Widget</label>
                                        <input type="text" name="name" value="{{ $widget->name }}" required class="w-full bg-secondary-50 border border-secondary-200 rounded-xl px-3 py-2 text-xs">
                                    </div>
                                    <div>
                                        <label class="block text-[10px] font-bold text-secondary-700 uppercase mb-1.5">Status Widget</label>
                                        <select name="livechat_active" class="w-full bg-secondary-50 border border-secondary-200 rounded-xl px-3 py-2.5 text-xs">
                                            <option value="1" {{ $widget->is_active ? 'selected' : '' }}>Aktif</option>
                                            <option value="0" {{ !$widget->is_active ? 'selected' : '' }}>Nonaktif</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 gap-4">
                                    <div>
                                        <label class="block text-[10px] font-bold text-secondary-700 uppercase mb-1.5">Domain Website yang Diizinkan (Wajib)</label>
                                        <input type="text" name="target_domain" value="{{ $widget->target_domain }}" required placeholder="Contoh: sekolah.sch.id atau https://sekolah.sch.id" class="w-full bg-secondary-50 border border-secondary-200 rounded-xl px-3 py-2 text-xs">
                                        <span class="text-[9px] text-secondary-400 mt-1 block">Wajib diisi agar widget tidak disalahgunakan di website lain.</span>
                                    </div>
                                </div>

                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-[10px] font-bold text-secondary-700 uppercase mb-1.5">Warna Utama</label>
                                        <div class="flex gap-2">
                                            <input type="color" name="livechat_primary_color" value="{{ $widget->primary_color ?: '#4f46e5' }}" class="w-10 h-10 rounded-xl border-0 p-0 cursor-pointer">
                                            <input type="text" value="{{ $widget->primary_color ?: '#4f46e5' }}" disabled class="flex-1 bg-secondary-50 border border-secondary-200 rounded-xl px-3 py-2 text-xs font-mono">
                                        </div>
                                    </div>
                                    <div class="flex items-end justify-end">
                                        <button type="submit" class="px-5 py-2.5 bg-indigo-600 text-white text-xs font-bold rounded-xl hover:bg-indigo-700 transition-all shadow-sm">
                                            Simpan Pengaturan
                                        </button>
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-[10px] font-bold text-secondary-700 uppercase mb-1.5">Pesan Pembuka</label>
                                    <textarea name="livechat_welcome_message" rows="2" placeholder="Pesan selamat datang otomatis saat pengunjung membuka chat..."
                                              class="w-full bg-secondary-50 border border-secondary-200 rounded-xl px-3 py-2 text-xs resize-none">{{ $widget->welcome_message }}</textarea>
                                </div>
                            </form>

                            <div class="pt-4 border-t border-secondary-100 flex items-center justify-between">
                                <label class="block text-[10px] font-bold text-secondary-700 uppercase">Informasi Widget</label>
                                <form action="{{ route(auth()->user()->role . '.ai-agen.connections.livechat.destroy', $widget) }}" method="POST" onsubmit="return confirm('Hapus widget Live Chat ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1.5 text-secondary-400 hover:text-red-500 transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </form>
                            </div>
                            @if($widget->token)
                                <div class="space-y-3">
                                    {{-- Widget ID --}}
                                    <div>
                                        <label class="block text-[10px] font-bold text-secondary-500 uppercase tracking-widest mb-1">Widget ID</label>
                                        <div class="flex items-center gap-2">
                                            <input type="text" value="{{ $widget->token }}" readonly 
                                                   id="widget-id-{{ $widget->id }}"
                                                   class="flex-1 bg-secondary-900 text-secondary-100 px-3 py-2 rounded-lg text-xs font-mono select-all border-0 focus:ring-0">
                                            <button type="button" onclick="copyToClipboard('widget-id-{{ $widget->id }}', this)" 
                                                    class="px-3 py-2 bg-secondary-100 hover:bg-secondary-200 text-secondary-600 rounded-lg text-[10px] font-bold uppercase tracking-wider transition-all flex items-center gap-1.5 whitespace-nowrap">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                                                Copy
                                            </button>
                                        </div>
                                    </div>

                                    {{-- URL Domain --}}
                                    <div>
                                        <label class="block text-[10px] font-bold text-secondary-500 uppercase tracking-widest mb-1">URL Domain AIagen</label>
                                        <div class="flex items-center gap-2">
                                            <input type="text" value="{{ url('/') }}" readonly 
                                                   id="widget-url-{{ $widget->id }}"
                                                   class="flex-1 bg-secondary-900 text-secondary-100 px-3 py-2 rounded-lg text-xs font-mono select-all border-0 focus:ring-0">
                                            <button type="button" onclick="copyToClipboard('widget-url-{{ $widget->id }}', this)" 
                                                    class="px-3 py-2 bg-secondary-100 hover:bg-secondary-200 text-secondary-600 rounded-lg text-[10px] font-bold uppercase tracking-wider transition-all flex items-center gap-1.5 whitespace-nowrap">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                                                Copy
                                            </button>
                                        </div>
                                    </div>

                                    <p class="text-[10px] text-secondary-400 italic">Gunakan Widget ID dan URL Domain di atas untuk memasang widget Live Chat di website Anda.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="lg:col-span-2 py-16 text-center bg-white rounded-3xl border border-secondary-200">
                        <div class="w-16 h-16 bg-secondary-50 text-secondary-300 rounded-2xl flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                        </div>
                        <p class="text-secondary-400 font-bold uppercase tracking-widest text-xs">Belum ada widget Live Chat ditambahkan.</p>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Other Platforms (Coming Soon) --}}
        <div x-show="tab !== 'whatsapp' && tab !== 'livechat'" x-cloak class="min-h-[400px] flex flex-col items-center justify-center text-center p-8 bg-white rounded-3xl border border-secondary-200 shadow-sm">
            <div class="w-20 h-20 bg-secondary-100 text-secondary-400 rounded-3xl flex items-center justify-center mb-6">
                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
            </div>
            <h3 class="text-2xl font-bold text-secondary-900 mb-2">Integrasi <span x-text="tab.charAt(0).toUpperCase() + tab.slice(1)"></span></h3>
            <p class="text-secondary-500 max-w-sm mb-8">Fitur integrasi otomatis AI dengan platform ini sedang dalam tahap pengembangan dan akan segera hadir.</p>
            <div class="px-4 py-2 bg-secondary-100 text-secondary-600 rounded-full text-xs font-bold uppercase tracking-widest">Coming Soon</div>
        </div>

        {{-- QR Code Modal --}}
        <div x-show="qrModal" class="fixed inset-0 z-[110] overflow-y-auto" x-cloak>
            <div class="flex items-center justify-center min-h-screen p-4 text-center">
                <div class="fixed inset-0 bg-secondary-900/60 transition-opacity" @click="closeQR()"></div>
                <div class="inline-block align-middle bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:max-w-md sm:w-full">
                        <div class="p-8 pb-12 sm:pb-8 text-center">
                        <h3 class="text-xl font-bold text-secondary-900 mb-2">Scan QR Code</h3>
                        <p class="text-sm text-secondary-500 mb-8">Buka WhatsApp di ponsel Anda > Perangkat Tertaut > Tautkan Perangkat.</p>
                        <div class="relative w-64 h-64 mx-auto bg-secondary-50 rounded-2xl border-2 border-secondary-100 flex items-center justify-center overflow-hidden">
                            <template x-if="loadingQR">
                                <div class="flex flex-col items-center gap-3">
                                    <svg class="animate-spin h-8 w-8 text-primary-600" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                    <span class="text-xs text-secondary-400 font-medium">Mengambil QR...</span>
                                </div>
                            </template>
                            <template x-if="currentQR">
                                <div class="p-4 bg-white rounded-xl flex items-center justify-center">
                                    <div id="qrcode_area" class="border-4 border-white shadow-sm"></div>
                                </div>
                            </template>
                        </div>
                        <button @click="closeQR()" class="mt-8 px-8 py-2 bg-secondary-100 text-secondary-600 text-sm font-bold rounded-xl hover:bg-secondary-200">Tutup</button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Add WA Device Modal --}}
        <div id="addDeviceModal" class="hidden fixed inset-0 z-[110] overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen p-4">
                <div class="fixed inset-0 bg-secondary-900/60 transition-opacity" @click="closeAddModal()"></div>
                <div class="relative bg-white rounded-2xl shadow-xl max-w-md w-full overflow-hidden">
                    <form action="{{ auth()->user()->isAdmin() ? route('admin.ai-agen.connections.whatsapp.store') : route('pengguna.ai-agen.connections.whatsapp.store') }}" method="POST"
                          x-data="{
                              selectedUser: '',
                              allDepts: {!! json_encode($departments->map(fn($d) => ['id' => $d->id, 'name' => $d->name, 'user_id' => $d->user_id, 'user_name' => $d->user->name ?? 'System'])) !!},
                              get filteredDepts() {
                                  var self = this;
                                  if (!self.selectedUser) return self.allDepts;
                                  return self.allDepts.filter(function(d) { return d.user_id == self.selectedUser; });
                              }
                          }">
                        @csrf
                        <div class="p-6">
                            <h3 class="text-lg font-bold text-secondary-900 mb-4">Tambah Perangkat WhatsApp</h3>
                            <div class="space-y-4">
                                @if(auth()->user()->isAdmin())
                                <div>
                                    <label class="block text-xs font-bold text-secondary-700 uppercase mb-2">Pemilik Akun</label>
                                    <select name="user_id" required x-model="selectedUser" class="w-full bg-secondary-50 border border-secondary-200 rounded-xl px-4 py-2.5 text-sm">
                                        <option value="">-- Pilih User --</option>
                                        @foreach($users as $user)
                                            <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->role }})</option>
                                        @endforeach
                                    </select>
                                </div>
                                @endif
                                <div>
                                    <label class="block text-xs font-bold text-secondary-700 uppercase mb-2">Nama Perangkat</label>
                                    <input type="text" name="name" required placeholder="Contoh: WhatsApp Sales"
                                           class="w-full bg-secondary-50 border border-secondary-200 rounded-xl px-4 py-2.5 text-sm">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-secondary-700 uppercase mb-2">Pilih Departemen</label>
                                    <select name="department_id" required class="w-full bg-secondary-50 border border-secondary-200 rounded-xl px-4 py-2.5 text-sm">
                                        <option value="">-- Pilih Departemen --</option>
                                        @foreach($allDepartments as $dept)
                                            <option value="{{ $dept->id }}" 
                                                    :hidden="selectedUser && selectedUser != '{{ $dept->user_id }}'">
                                                [{{ $dept->user->name ?? 'System' }}] {{ $dept->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="p-6 bg-secondary-50 pb-12 sm:pb-6 flex justify-end gap-3">
                            <button type="button" @click="closeAddModal()" class="text-sm font-bold text-secondary-500">Batal</button>
                            <button type="submit" class="px-6 py-2 bg-green-600 text-white text-sm font-bold rounded-xl hover:bg-green-700 shadow-lg shadow-green-500/20">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Add Live Chat Modal --}}
        <div id="addLivechatModal" class="hidden fixed inset-0 z-[110] overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen p-4">
                <div class="fixed inset-0 bg-secondary-900/60 transition-opacity" @click="closeAddLivechatModal()"></div>
                <div class="relative bg-white rounded-2xl shadow-xl max-w-md w-full overflow-hidden">
                    <form action="{{ auth()->user()->isAdmin() ? route('admin.ai-agen.connections.livechat.store') : route('pengguna.ai-agen.connections.livechat.store') }}" 
                          method="POST"
                          x-data="{
                              selectedUser: '',
                              allDepts: {!! json_encode($departments->map(fn($d) => ['id' => $d->id, 'name' => $d->name, 'user_id' => $d->user_id, 'user_name' => $d->user->name ?? 'System'])) !!},
                              get filteredDepts() {
                                  var self = this;
                                  if (!self.selectedUser) return self.allDepts;
                                  return self.allDepts.filter(function(d) { return d.user_id == self.selectedUser; });
                              }
                          }">
                        @csrf
                        <div class="p-6 space-y-4">
                            <h3 class="text-lg font-bold text-secondary-900 mb-4">Tambah Widget Live Chat</h3>
                            
                            @if(auth()->user()->isAdmin())
                            <div>
                                <label class="block text-xs font-bold text-secondary-700 uppercase mb-2">Pemilik Akun</label>
                                <select name="user_id" required x-model="selectedUser" class="w-full bg-secondary-50 border border-secondary-200 rounded-xl px-4 py-2.5 text-sm">
                                    <option value="">-- Pilih User --</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->role }})</option>
                                    @endforeach
                                </select>
                            </div>
                            @endif

                            <div>
                                <label class="block text-xs font-bold text-secondary-700 uppercase mb-2">Nama Widget</label>
                                <input type="text" name="name" required placeholder="Contoh: Widget Live Chat Toko A"
                                       class="w-full bg-secondary-50 border border-secondary-200 rounded-xl px-4 py-2.5 text-sm">
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-secondary-700 uppercase mb-2">Pilih Departemen</label>
                                <select name="department_id" required class="w-full bg-secondary-50 border border-secondary-200 rounded-xl px-4 py-2.5 text-sm">
                                    <option value="">-- Pilih Departemen --</option>
                                    @foreach($allDepartments as $dept)
                                        <option value="{{ $dept->id }}" 
                                                :hidden="selectedUser && selectedUser != '{{ $dept->user_id }}'">
                                            [{{ $dept->user->name ?? 'System' }}] {{ $dept->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-secondary-700 uppercase mb-2">Domain Website yang Diizinkan (Wajib)</label>
                                <input type="text" name="target_domain" required placeholder="Contoh: sekolah.sch.id atau https://sekolah.sch.id"
                                       class="w-full bg-secondary-50 border border-secondary-200 rounded-xl px-4 py-2.5 text-sm">
                                <span class="text-[10px] text-secondary-400 mt-1 block">Wajib diisi agar widget tidak disalahgunakan di website lain.</span>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-bold text-secondary-700 uppercase mb-2">Status Widget</label>
                                    <select name="livechat_active" class="w-full bg-secondary-50 border border-secondary-200 rounded-xl px-4 py-2.5 text-sm">
                                        <option value="1">Aktif</option>
                                        <option value="0">Nonaktif</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-secondary-700 uppercase mb-2">Warna Utama</label>
                                    <div class="flex gap-2">
                                        <input type="color" name="livechat_primary_color" value="#4f46e5" class="w-10 h-10 rounded-xl border-0 p-0 cursor-pointer">
                                    </div>
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-secondary-700 uppercase mb-2">Pesan Pembuka</label>
                                <textarea name="livechat_welcome_message" rows="2" placeholder="Pesan selamat datang otomatis..."
                                          class="w-full bg-secondary-50 border border-secondary-200 rounded-xl px-4 py-2.5 text-sm resize-none"></textarea>
                            </div>
                        </div>
                        <div class="p-6 bg-secondary-50 pb-12 sm:pb-6 flex justify-end gap-3">
                            <button type="button" @click="closeAddLivechatModal()" class="text-sm font-bold text-secondary-500">Batal</button>
                            <button type="submit" class="px-6 py-2 bg-indigo-600 text-white text-sm font-bold rounded-xl hover:bg-indigo-700 shadow-lg shadow-indigo-500/20">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function connectionManager() {
            return {
                tab: '{{ $tab }}',
                qrModal: false,
                currentQR: null,
                loadingQR: false,
                pollingInterval: null,
                activeDeviceId: null,
                bgPollingInterval: null,
                isAddingDevice: false,
                isAddingLivechat: false,
                init() {
                    this.$watch('currentQR', value => {
                        if (value) {
                            this.$nextTick(() => {
                                const area = document.getElementById('qrcode_area');
                                if (area) {
                                    area.innerHTML = '';
                                    new QRCode(area, {
                                        text: value,
                                        width: 200,
                                        height: 200,
                                        colorDark: "#000000",
                                        colorLight: "#ffffff",
                                        correctLevel: QRCode.CorrectLevel.H
                                    });
                                }
                            });
                        }
                    });
                    
                    // Auto-polling: cek status semua device setiap 5 detik
                    this.startBackgroundPolling();
                },
                openAddModal() {
                    this.isAddingDevice = true;
                    document.getElementById('addDeviceModal').classList.remove('hidden');
                },
                closeAddModal() {
                    this.isAddingDevice = false;
                    document.getElementById('addDeviceModal').classList.add('hidden');
                },
                openAddLivechatModal() {
                    this.isAddingLivechat = true;
                    document.getElementById('addLivechatModal').classList.remove('hidden');
                },
                closeAddLivechatModal() {
                    this.isAddingLivechat = false;
                    document.getElementById('addLivechatModal').classList.add('hidden');
                },
                startBackgroundPolling() {
                    const prefix = '{{ auth()->user()->isAdmin() ? "admin" : "pengguna" }}';
                    const deviceIds = @json($whatsappDevices->pluck('id'));
                    
                    if (deviceIds.length === 0) return;

                    this.bgPollingInterval = setInterval(() => {
                        // JANGAN REFRESH/POLLING jika sedang ngetik di modal tambah perangkat
                        if (this.isAddingDevice) return;

                        deviceIds.forEach(id => {
                            fetch(`/${prefix}/ai-agen/connections/whatsapp/${id}/status`)
                                .then(res => res.json())
                                .then(data => {
                                    if (data.status === 'connected') {
                                        // Cek apakah status di UI saat ini berbeda (perlu reload)
                                        // Untuk simplifikasi, kita reload hanya jika modal QR tidak terbuka
                                        if (!this.qrModal) {
                                            // location.reload(); // Dimatikan agar tidak mengganggu
                                        }
                                    }
                                    // Jika ada QR dan modal belum terbuka, buka otomatis
                                    if (data.qr && !this.qrModal && !this.isAddingDevice) {
                                        this.activeDeviceId = id;
                                        this.currentQR = data.qr;
                                        this.loadingQR = false;
                                        this.qrModal = true;
                                    }
                                })
                                .catch(() => {});
                        });
                    }, 5000);
                },
                fetchQR(deviceId) {
                    this.activeDeviceId = deviceId;
                    this.loadingQR = true;
                    this.qrModal = true;
                    this.currentQR = null;
                    
                    const prefix = '{{ auth()->user()->isAdmin() ? "admin" : "pengguna" }}';
                    
                    // Panggil init di server agar gateway memunculkan QR
                    fetch(`/${prefix}/ai-agen/connections/whatsapp/${deviceId}/init`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        }
                    })
                    .then(() => {
                        // Mulai Polling cepat untuk device ini
                        this.startPolling();
                    });
                },
                startPolling() {
                    this.checkStatus();
                    this.pollingInterval = setInterval(() => {
                        this.checkStatus();
                    }, 3000);
                },
                checkStatus() {
                    const prefix = '{{ auth()->user()->isAdmin() ? "admin" : "pengguna" }}';
                    fetch(`/${prefix}/ai-agen/connections/whatsapp/${this.activeDeviceId}/status`)
                        .then(res => res.json())
                        .then(data => {
                            this.loadingQR = false;
                            if (data.status === 'connected') {
                                this.stopPolling();
                                this.qrModal = false;
                                location.reload();
                            } else if (data.qr) {
                                this.currentQR = data.qr;
                            }
                        })
                        .catch(err => {
                            console.error('Polling error:', err);
                        });
                },
                stopPolling() {
                    if (this.pollingInterval) {
                        clearInterval(this.pollingInterval);
                        this.pollingInterval = null;
                    }
                },
                closeQR() {
                    this.qrModal = false;
                    this.stopPolling();
                }
            }
        }

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
                    if (id) {
                        url.searchParams.set('user_id', id);
                    } else {
                        url.searchParams.delete('user_id');
                    }
                    url.searchParams.delete('department_id'); // Reset department filter if user changes
                    window.location.href = url.toString();
                }
            }
        }

        function deptSearchableSelect(config) {
            return {
                open: false,
                search: '',
                selectedId: config.selectedId,
                selectedName: config.selectedName,
                depts: config.depts,
                get filteredDepts() {
                    if (!this.search) return this.depts;
                    return this.depts.filter(d => 
                        d.name && d.name.toLowerCase().includes(this.search.toLowerCase())
                    );
                },
                selectDept(id, name) {
                    this.selectedId = id;
                    this.selectedName = name;
                    this.open = false;
                    const url = new URL(window.location.href);
                    if (id) {
                        url.searchParams.set('department_id', id);
                    } else {
                        url.searchParams.delete('department_id');
                    }
                    window.location.href = url.toString();
                }
            }
        }
        function copyToClipboard(inputId, btn) {
            const input = document.getElementById(inputId);
            input.select();
            navigator.clipboard.writeText(input.value).then(function() {
                const originalText = btn.innerHTML;
                btn.innerHTML = '<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> Copied!';
                btn.classList.remove('bg-secondary-100', 'text-secondary-600');
                btn.classList.add('bg-green-100', 'text-green-700');
                setTimeout(function() {
                    btn.innerHTML = originalText;
                    btn.classList.remove('bg-green-100', 'text-green-700');
                    btn.classList.add('bg-secondary-100', 'text-secondary-600');
                }, 2000);
            });
        }
    </script>
</x-aiagen-layout>
