<x-aiagen-layout>
    <x-slot name="header">
        Integrasi Connection
    </x-slot>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>

    <div class="space-y-6" x-data="connectionManager()">
        <!-- Tab Navigation -->
        <div class="bg-white rounded-2xl shadow-sm border border-secondary-200 p-2 flex gap-2 overflow-x-auto">
            @foreach(['whatsapp' => 'WhatsApp', 'telegram' => 'Telegram', 'facebook' => 'Facebook', 'instagram' => 'Instagram', 'tiktok' => 'TikTok'] as $id => $label)
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
                {{-- Admin Table View --}}
                <div class="bg-white rounded-2xl shadow-sm border border-secondary-200 overflow-hidden">
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
            @else
                {{-- User Grid View --}}
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @forelse($whatsappDevices as $device)
                        <div class="bg-white rounded-2xl shadow-sm border border-secondary-200 overflow-hidden group hover:shadow-lg transition-all relative">
                            <div class="p-6">
                                <div class="flex items-center justify-between mb-6">
                                    <div class="w-12 h-12 bg-green-100 text-green-600 rounded-2xl flex items-center justify-center">
                                        <svg class="w-7 h-7" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.414 0 .018 5.394 0 12.03a11.81 11.81 0 001.576 5.922L0 24l6.117-1.605a11.803 11.803 0 005.925 1.583h.005c6.635 0 12.032-5.393 12.035-12.029a11.79 11.79 0 00-3.526-8.508z"/></svg>
                                    </div>
                                    <div class="text-right">
                                        <span class="px-2 py-1 {{ $device->status == 'connected' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }} rounded text-[10px] font-bold uppercase tracking-wider">
                                            {{ $device->status }}
                                        </span>
                                    </div>
                                </div>
                                <h3 class="text-lg font-bold text-secondary-900 mb-1">{{ $device->name }}</h3>
                                <p class="text-xs text-secondary-500 mb-4">Departemen: <span class="font-bold text-primary-600">{{ $device->department->name }}</span></p>
                                
                                <div class="space-y-3">
                                    @if($device->status == 'disconnected')
                                        <button @click="fetchQR({{ $device->id }})" class="w-full py-2.5 bg-primary-600 text-white text-xs font-bold rounded-xl hover:bg-primary-700 transition-colors shadow-lg shadow-primary-500/20">
                                            Hubungkan (Scan QR)
                                        </button>
                                    @else
                                        <form action="{{ route('pengguna.ai-agen.connections.whatsapp.disconnect', $device) }}" method="POST" onsubmit="return confirm('Putuskan koneksi WhatsApp ini?')">
                                            @csrf
                                            <button type="submit" class="w-full py-2.5 bg-orange-500 text-white text-xs font-bold rounded-xl hover:bg-orange-600 transition-colors shadow-lg shadow-orange-500/20">
                                                Putuskan Koneksi
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                            <div class="px-6 py-3 bg-secondary-50 border-t border-secondary-100 flex items-center justify-between">
                                <span class="text-[10px] text-secondary-400 font-bold uppercase">{{ $device->phone_number ?: 'Nomor Belum Terdeteksi' }}</span>
                                <form action="{{ route('pengguna.ai-agen.connections.whatsapp.destroy', $device) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-secondary-300 hover:text-red-500 transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </form>
                            </div>
                        </div>
                    @empty
                        <div class="md:col-span-3 py-12 text-center bg-secondary-50 rounded-3xl border-2 border-dashed border-secondary-200">
                            <p class="text-secondary-400 font-medium">Belum ada perangkat WhatsApp ditambahkan.</p>
                        </div>
                    @endforelse
                </div>
            @endif
        </div>

        {{-- Other Platforms (Coming Soon) --}}
        <div x-show="tab !== 'whatsapp'" x-cloak class="min-h-[400px] flex flex-col items-center justify-center text-center p-8 bg-white rounded-3xl border border-secondary-200 shadow-sm">
            <div class="w-20 h-20 bg-secondary-100 text-secondary-400 rounded-3xl flex items-center justify-center mb-6">
                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
            </div>
            <h3 class="text-2xl font-bold text-secondary-900 mb-2">Integrasi <span x-text="tab.charAt(0).toUpperCase() + tab.slice(1)"></span></h3>
            <p class="text-secondary-500 max-w-sm mb-8">Fitur integrasi otomatis AI dengan platform ini sedang dalam tahap pengembangan dan akan segera hadir.</p>
            <div class="px-4 py-2 bg-secondary-100 text-secondary-600 rounded-full text-xs font-bold uppercase tracking-widest">Coming Soon</div>
        </div>

        {{-- QR Code Modal --}}
        <div x-show="qrModal" class="fixed inset-0 z-50 overflow-y-auto" x-cloak>
            <div class="flex items-center justify-center min-h-screen p-4 text-center">
                <div class="fixed inset-0 bg-secondary-900/60 transition-opacity" @click="closeQR()"></div>
                <div class="inline-block align-middle bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:max-w-md sm:w-full">
                    <div class="p-8 text-center">
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
        <div id="addDeviceModal" class="hidden fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen p-4">
                <div class="fixed inset-0 bg-secondary-900/60 transition-opacity" @click="closeAddModal()"></div>
                <div class="relative bg-white rounded-2xl shadow-xl max-w-md w-full overflow-hidden">
                    <form action="{{ auth()->user()->isAdmin() ? route('admin.ai-agen.connections.whatsapp.store') : route('pengguna.ai-agen.connections.whatsapp.store') }}" method="POST">
                        @csrf
                        <div class="p-6">
                            <h3 class="text-lg font-bold text-secondary-900 mb-4">Tambah Perangkat WhatsApp</h3>
                            <div class="space-y-4">
                                @if(auth()->user()->isAdmin())
                                <div>
                                    <label class="block text-xs font-bold text-secondary-700 uppercase mb-2">Pemilik Akun</label>
                                    <select name="user_id" required class="w-full bg-secondary-50 border border-secondary-200 rounded-xl px-4 py-2.5 text-sm">
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
                                        @foreach($departments as $dept)
                                            <option value="{{ $dept->id }}">[{{ $dept->user->name ?? 'System' }}] {{ $dept->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="p-6 bg-secondary-50 flex justify-end gap-3">
                            <button type="button" @click="closeAddModal()" class="text-sm font-bold text-secondary-500">Batal</button>
                            <button type="submit" class="px-6 py-2 bg-green-600 text-white text-sm font-bold rounded-xl hover:bg-green-700 shadow-lg shadow-green-500/20">Simpan</button>
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
    </script>
</x-aiagen-layout>
