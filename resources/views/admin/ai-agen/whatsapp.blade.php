<x-aiagen-layout>
    <x-slot name="header">
        Integrasi WhatsApp
    </x-slot>

    <div class="space-y-6" x-data="{ 
        qrModal: false, 
        currentQR: null, 
        loadingQR: false,
        fetchQR(deviceId) {
            this.loadingQR = true;
            this.qrModal = true;
            this.currentQR = null;
            
            // Panggil API Laravel untuk ambil status/QR dari gateway
            fetch(`/{{ auth()->user()->isAdmin() ? 'admin' : 'pengguna' }}/ai-agen/whatsapp/${deviceId}/status`)
                .then(res => res.json())
                .then(data => {
                    this.loadingQR = false;
                    if (data.qr) {
                        this.currentQR = data.qr;
                    } else if (data.status === 'connected') {
                        alert('Perangkat sudah terhubung!');
                        this.qrModal = false;
                        location.reload();
                    } else {
                        alert('QR Code belum tersedia atau gateway tidak aktif.');
                        this.qrModal = false;
                    }
                })
                .catch(err => {
                    this.loadingQR = false;
                    alert('Gagal mengambil status perangkat.');
                });
        }
    }">
        {{-- Header --}}
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h2 class="text-xl font-bold text-secondary-900">Perangkat WhatsApp</h2>
                <p class="text-sm text-secondary-500">Hubungkan nomor WhatsApp Anda ke masing-masing departemen.</p>
            </div>
            <button onclick="document.getElementById('addDeviceModal').classList.remove('hidden')" 
                    class="px-5 py-2.5 bg-green-600 text-white text-sm font-bold rounded-xl hover:bg-green-700 transition-all shadow-lg shadow-green-500/20 flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Tambah Perangkat
            </button>
        </div>

        {{-- Devices Grid --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($devices as $device)
                <div class="bg-white rounded-2xl shadow-sm border border-secondary-200 overflow-hidden group hover:shadow-lg transition-all">
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
                                <button class="w-full py-2.5 bg-secondary-100 text-secondary-600 text-xs font-bold rounded-xl hover:bg-secondary-200 transition-colors">
                                    Putuskan Koneksi
                                </button>
                            @endif
                        </div>
                    </div>
                    <div class="px-6 py-3 bg-secondary-50 border-t border-secondary-100 flex items-center justify-between">
                        <span class="text-[10px] text-secondary-400 font-bold uppercase">{{ $device->phone_number ?: 'Nomor Belum Terdeteksi' }}</span>
                        <form action="{{ auth()->user()->isAdmin() ? route('admin.ai-agen.whatsapp.destroy', $device) : route('pengguna.ai-agen.whatsapp.destroy', $device) }}" method="POST">
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

        {{-- QR Code Modal --}}
        <div x-show="qrModal" class="fixed inset-0 z-50 overflow-y-auto" x-cloak>
            <div class="flex items-center justify-center min-h-screen p-4 text-center">
                <div class="fixed inset-0 bg-secondary-900/60 transition-opacity" @click="qrModal = false"></div>
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
                                <div class="p-4 bg-white rounded-xl">
                                    {{-- Gunakan library qrcode generator di client atau sekadar tampilkan teks QR jika mau simpel --}}
                                    <div id="qrcode_area" class="w-48 h-48 flex items-center justify-center bg-secondary-100 rounded-lg">
                                        {{-- Di lingkungan nyata, kita kirim gambar base64. Untuk sekarang kita tampilkan indikator --}}
                                        <div class="text-[10px] text-center p-4 break-all text-secondary-600 font-mono" x-text="currentQR"></div>
                                    </div>
                                </div>
                            </template>
                        </div>
                        
                        <button @click="qrModal = false" class="mt-8 px-8 py-2 bg-secondary-100 text-secondary-600 text-sm font-bold rounded-xl hover:bg-secondary-200">
                            Tutup
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Add Device Modal --}}
        <div id="addDeviceModal" class="hidden fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen p-4">
                <div class="fixed inset-0 bg-secondary-900/60 transition-opacity" onclick="document.getElementById('addDeviceModal').classList.add('hidden')"></div>
                <div class="relative bg-white rounded-2xl shadow-xl max-w-md w-full overflow-hidden">
                    <form action="{{ auth()->user()->isAdmin() ? route('admin.ai-agen.whatsapp.store') : route('pengguna.ai-agen.whatsapp.store') }}" method="POST">
                        @csrf
                        <div class="p-6">
                            <h3 class="text-lg font-bold text-secondary-900 mb-4">Tambah Perangkat WhatsApp</h3>
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-xs font-bold text-secondary-700 uppercase mb-2">Nama Perangkat</label>
                                    <input type="text" name="name" required placeholder="Contoh: WhatsApp CS Utama"
                                           class="w-full bg-secondary-50 border border-secondary-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary-500">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-secondary-700 uppercase mb-2">Pilih Departemen</label>
                                    <select name="department_id" required class="w-full bg-secondary-50 border border-secondary-200 rounded-xl px-4 py-2.5 text-sm">
                                        @foreach($departments as $dept)
                                            <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="p-6 bg-secondary-50 flex justify-end gap-3">
                            <button type="button" onclick="document.getElementById('addDeviceModal').classList.add('hidden')"
                                    class="text-sm font-bold text-secondary-500">Batal</button>
                            <button type="submit" class="px-6 py-2 bg-green-600 text-white text-sm font-bold rounded-xl hover:bg-green-700 shadow-lg shadow-green-500/20">
                                Simpan Perangkat
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-aiagen-layout>
