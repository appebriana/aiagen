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
                    <h2 class="text-2xl font-bold text-secondary-900 mb-2">Pusat Kendali AI Agen</h2>
                    <p class="text-secondary-500 max-w-2xl">Kelola kecerdasan buatan Anda untuk berinteraksi dengan pelanggan secara otomatis. Saat ini sistem difokuskan pada integrasi <strong>WhatsApp</strong>.</p>
                </div>
                <div class="flex-shrink-0">
                    <div class="px-4 py-2 bg-green-100 text-green-700 rounded-full text-xs font-bold flex items-center gap-2">
                        <span class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></span>
                        Sistem Aktif (OpenAI)
                    </div>
                </div>
            </div>
        </div>

        {{-- Platform Grid --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            {{-- Knowledge Base (Active) --}}
            <div class="bg-white rounded-2xl shadow-sm border border-secondary-200 p-6 relative overflow-hidden group hover:shadow-xl transition-all duration-300">
                <div class="absolute top-0 right-0 p-3">
                    <span class="px-2 py-1 bg-green-500 text-white text-[10px] font-bold rounded uppercase tracking-wider">Aktif</span>
                </div>
                <div class="w-12 h-12 bg-green-500 text-white rounded-xl flex items-center justify-center mb-4 shadow-lg shadow-green-500/30">
                    <svg class="w-7 h-7" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.414 0 .018 5.394 0 12.03a11.81 11.81 0 001.576 5.922L0 24l6.117-1.605a11.803 11.803 0 005.925 1.583h.005c6.635 0 12.032-5.393 12.035-12.029a11.79 11.79 0 00-3.526-8.508z"/></svg>
                </div>
                <h3 class="text-xl font-bold text-secondary-900 mb-2">Knowledge Base</h3>
                <p class="text-sm text-secondary-500 mb-6 leading-relaxed">Kelola dokumen (PDF/TXT) yang akan dipelajari oleh AI untuk menjawab pertanyaan pelanggan.</p>
                <div class="flex items-center justify-between">
                    <a href="{{ route('admin.ai-agen.knowledge.index') }}" class="text-sm font-bold text-primary-600 hover:text-primary-700">Kelola Dokumen &rarr;</a>
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
                    <a href="{{ route('admin.ai-agen.connections.index') }}" class="inline-flex items-center px-4 py-2 bg-primary-600 text-white text-xs font-bold rounded-lg hover:bg-primary-700 transition-colors">
                        Hubungkan Sekarang
                    </a>
                </div>
            </div>

            {{-- Live Chat (Active) --}}
            <div class="bg-white rounded-2xl shadow-sm border border-secondary-200 p-6 relative overflow-hidden group hover:shadow-xl transition-all duration-300">
                <div class="absolute top-0 right-0 p-3">
                    <span class="px-2 py-1 bg-green-500 text-white text-[10px] font-bold rounded uppercase tracking-wider">Tersedia</span>
                </div>
                <div class="w-12 h-12 bg-indigo-600 text-white rounded-xl flex items-center justify-center mb-4 shadow-lg shadow-indigo-600/30">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                </div>
                <h3 class="text-xl font-bold text-secondary-900 mb-2">Live Chat Website</h3>
                <p class="text-sm text-secondary-500 mb-6 leading-relaxed">Pasang widget live chat di website Anda agar terhubung dengan RAG AI Agent.</p>
                <div class="flex items-center justify-between">
                    <a href="{{ route('admin.ai-agen.connections.index', ['tab' => 'livechat']) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white text-xs font-bold rounded-lg hover:bg-indigo-700 transition-colors">
                        Konfigurasi &rarr;
                    </a>
                </div>
            </div>

            {{-- Coming Soon Platforms --}}
            @foreach(['Telegram' => 'M4.5 12l15-9-3 15-4-3-4 3 1-6', 'Instagram' => 'M7 2h10a5 5 0 015 5v10a5 5 0 01-5 5H7a5 5 0 01-5-5V7a5 5 0 015-5zm0 2a3 3 0 00-3 3v10a3 3 0 003 3h10a3 3 0 003-3V7a3 3 0 00-3-3H7zm5 3a5 5 0 110 10 5 5 0 010-10zm0 2a3 3 0 100 6 3 3 0 000-6zm5-2a1 1 0 110 2 1 1 0 010-2z', 'Facebook' => 'M18 2h-3a5 5 0 00-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 011-1h3V2z', 'TikTok' => 'M13.8 3.335V1.2h-2.1c-.001.001-.001 0-.001 0v1.2c0 .01.01.01.01.01z M9 12a1 1 0 112 0 1 1 0 01-2 0z'] as $platform => $path)
            <div class="bg-secondary-50/50 rounded-2xl shadow-sm border border-secondary-200 p-6 grayscale opacity-60 relative group transition-all duration-300">
                <div class="absolute top-0 right-0 p-3">
                    <span class="px-2 py-1 bg-secondary-200 text-secondary-600 text-[9px] font-bold rounded uppercase tracking-wider">Coming Soon</span>
                </div>
                <div class="w-12 h-12 bg-secondary-200 text-secondary-400 rounded-xl flex items-center justify-center mb-4">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $path }}"/></svg>
                </div>
                <h3 class="text-xl font-bold text-secondary-700 mb-2">{{ $platform }}</h3>
                <p class="text-sm text-secondary-400 mb-6 leading-relaxed">Integrasi otomatis dengan AI untuk platform {{ $platform }} akan segera hadir.</p>
                <div class="flex items-center justify-between border-t border-secondary-100 pt-4">
                    <span class="text-xs font-medium text-secondary-400 italic">Segera Hadir</span>
                </div>
            </div>
            @endforeach
        </div>

        {{-- Configuration Note --}}
        <div class="bg-secondary-900 rounded-2xl p-6 text-white overflow-hidden relative">
            <div class="relative z-10 flex flex-col md:flex-row items-center justify-between gap-6">
                <div class="flex items-center gap-4 text-center md:text-left">
                    <div class="w-12 h-12 bg-primary-500/20 rounded-xl flex items-center justify-center text-primary-400 flex-shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    </div>
                    <div>
                        <h4 class="font-bold text-lg">Konfigurasi Keamanan</h4>
                        <p class="text-sm text-secondary-400">OpenAI API Key dikelola secara aman melalui sistem environment variables (<code>.env</code>).</p>
                    </div>
                </div>
                <div class="px-6 py-2 bg-white/10 rounded-xl border border-white/10 text-xs font-medium text-secondary-300">
                    Konfigurasi Selesai
                </div>
            </div>
            {{-- Decorative elements --}}
            <div class="absolute top-0 right-0 -mr-8 -mt-8 w-32 h-32 bg-primary-500/10 rounded-full blur-3xl"></div>
            <div class="absolute bottom-0 left-0 -ml-8 -mb-8 w-32 h-32 bg-primary-500/10 rounded-full blur-3xl"></div>
        </div>
    </div>
</x-aiagen-layout>
