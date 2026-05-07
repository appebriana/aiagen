<x-aiagen-layout>
    <x-slot name="header">
        Dashboard AI Agen
    </x-slot>

    <div class="space-y-6">
        {{-- Hero Section --}}
        <div class="bg-white rounded-2xl shadow-sm border border-secondary-200 overflow-hidden">
            <div class="p-8 flex flex-col md:flex-row items-center gap-8">
                <div class="w-24 h-24 bg-primary-100 text-primary-600 rounded-3xl flex items-center justify-center flex-shrink-0 shadow-lg shadow-primary-500/20">
                    <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                </div>
                <div class="text-center md:text-left flex-1">
                    <h2 class="text-2xl font-bold text-secondary-900 mb-2">Halo, {{ Auth::user()->name }}!</h2>
                    <p class="text-secondary-500 max-w-2xl">Mulai gunakan AI Agen untuk membalas pesan pelanggan secara otomatis. Saat ini kami menyediakan integrasi khusus untuk <strong>WhatsApp</strong>.</p>
                </div>
                <div class="flex-shrink-0">
                    <div class="px-4 py-2 bg-primary-100 text-primary-700 rounded-full text-xs font-bold flex items-center gap-2">
                        Member {{ ucfirst(Auth::user()->role) }}
                    </div>
                </div>
            </div>
        </div>

        {{-- Platform Grid --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            {{-- Knowledge Base (Active for user) --}}
            <div class="bg-white rounded-2xl shadow-sm border border-secondary-200 p-6 relative overflow-hidden group hover:shadow-xl transition-all duration-300">
                <div class="absolute top-0 right-0 p-3">
                    <span class="px-2 py-1 bg-primary-500 text-white text-[10px] font-bold rounded uppercase tracking-wider">Aktif</span>
                </div>
                <div class="w-12 h-12 bg-primary-100 text-primary-600 rounded-xl flex items-center justify-center mb-4">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                </div>
                <h3 class="text-xl font-bold text-secondary-900 mb-2">Knowledge Base</h3>
                <p class="text-sm text-secondary-500 mb-6 leading-relaxed">Unggah dokumen produk atau FAQ Anda agar AI bisa menjawab pertanyaan pelanggan dengan tepat.</p>
                <div class="flex items-center justify-between">
                    <a href="{{ route('pengguna.ai-agen.knowledge.index') }}" class="text-sm font-bold text-primary-600 hover:text-primary-700">Kelola Dokumen &rarr;</a>
                </div>
            </div>

            {{-- WhatsApp (Active) --}}
            <div class="bg-white rounded-2xl shadow-sm border border-secondary-200 p-6 relative overflow-hidden group hover:shadow-xl transition-all duration-300">
                <div class="absolute top-0 right-0 p-3">
                    <span class="px-2 py-1 bg-green-500 text-white text-[10px] font-bold rounded uppercase tracking-wider">Tersedia</span>
                </div>
                <div class="w-12 h-12 bg-green-500 text-white rounded-xl flex items-center justify-center mb-4 shadow-lg shadow-green-500/30">
                    <svg class="w-7 h-7" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.414 0 .018 5.394 0 12.03a11.81 11.81 0 001.576 5.922L0 24l6.117-1.605a11.803 11.803 0 005.925 1.583h.005c6.635 0 12.032-5.393 12.035-12.029a11.79 11.79 0 00-3.526-8.508z"/></svg>
                </div>
                <h3 class="text-xl font-bold text-secondary-900 mb-2">WhatsApp</h3>
                <p class="text-sm text-secondary-500 mb-6 leading-relaxed">Gunakan AI untuk membalas pesan di nomor WhatsApp Anda secara otomatis.</p>
                <div class="flex items-center justify-between">
                    <a href="{{ route('pengguna.ai-agen.connections.index') }}" class="inline-flex items-center px-4 py-2 bg-primary-600 text-white text-xs font-bold rounded-lg hover:bg-primary-700 transition-colors">
                        Hubungkan Sekarang
                    </a>
                </div>
            </div>

            {{-- Coming Soon Platforms --}}
            @foreach(['Telegram' => 'M4.5 12l15-9-3 15-4-3-4 3 1-6', 'Instagram' => 'M7 2h10a5 5 0 015 5v10a5 5 0 01-5 5H7a5 5 0 01-5-5V7a5 5 0 015-5zm0 2a3 3 0 00-3 3v10a3 3 0 003 3h10a3 3 0 003-3V7a3 3 0 00-3-3H7zm5 3a5 5 0 110 10 5 5 0 010-10zm0 2a3 3 0 100 6 3 3 0 000-6zm5-2a1 1 0 110 2 1 1 0 010-2z'] as $platform => $path)
            <div class="bg-secondary-50/50 rounded-2xl shadow-sm border border-secondary-200 p-6 grayscale opacity-60 relative group">
                <div class="absolute top-0 right-0 p-3">
                    <span class="px-2 py-1 bg-secondary-200 text-secondary-600 text-[9px] font-bold rounded uppercase tracking-wider">Coming Soon</span>
                </div>
                <div class="w-12 h-12 bg-secondary-200 text-secondary-400 rounded-xl flex items-center justify-center mb-4">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $path }}"/></svg>
                </div>
                <h3 class="text-xl font-bold text-secondary-700 mb-2">{{ $platform }}</h3>
                <p class="text-sm text-secondary-400 mb-6 leading-relaxed">Integrasi otomatis dengan AI untuk platform {{ $platform }} akan segera hadir.</p>
            </div>
            @endforeach
        </div>

        {{-- Help Card --}}
        <div class="bg-primary-900 rounded-2xl p-8 text-white relative overflow-hidden">
            <div class="relative z-10">
                <h3 class="text-xl font-bold mb-2">Butuh bantuan?</h3>
                <p class="text-primary-300 text-sm max-w-md mb-6">Jika Anda kesulitan menghubungkan nomor WhatsApp atau ingin bertanya seputar fitur AI Agen, tim kami siap membantu.</p>
                <a href="#" class="inline-flex items-center px-6 py-3 bg-white text-primary-900 font-bold rounded-xl hover:bg-primary-50 transition-colors shadow-lg">
                    Hubungi Support
                </a>
            </div>
            <div class="absolute right-0 bottom-0 opacity-10">
                <svg class="w-64 h-64 -mb-12 -mr-12" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 17h-2v-2h2v2zm2.07-7.75l-.9.92C13.45 12.9 13 13.5 13 15h-2v-.5c0-1.1.45-2.1 1.17-2.83l1.24-1.26c.37-.36.59-.86.59-1.41 0-1.1-.9-2-2-2s-2 .9-2 2H8c0-2.21 1.79-4 4-4s4 1.79 4 4c0 .88-.36 1.68-.93 2.25z"/></svg>
            </div>
        </div>
    </div>
</x-aiagen-layout>
