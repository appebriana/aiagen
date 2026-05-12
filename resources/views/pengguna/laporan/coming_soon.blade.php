<x-aiagen-layout>
    <x-slot name="header">
        Laporan Interaksi — {{ $platform }}
    </x-slot>

    <div class="flex items-center justify-center min-h-[60vh]">
        <div class="text-center max-w-lg mx-auto">
            {{-- Icon --}}
            <div class="mx-auto w-24 h-24 bg-gradient-to-br from-primary-100 to-primary-200 rounded-3xl flex items-center justify-center mb-8 shadow-lg shadow-primary-100/50">
                @if($platform === 'Instagram')
                <svg class="w-12 h-12 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <rect x="2" y="2" width="20" height="20" rx="5" ry="5" stroke-width="2"/>
                    <circle cx="12" cy="12" r="4.5" stroke-width="2"/>
                    <circle cx="17.5" cy="6.5" r="1.5" fill="currentColor" stroke="none"/>
                </svg>
                @elseif($platform === 'Telegram')
                <svg class="w-12 h-12 text-primary-600" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm4.64 6.8c-.15 1.58-.8 5.42-1.13 7.19-.14.75-.42 1-.68 1.03-.58.05-1.02-.38-1.58-.75-.88-.58-1.38-.94-2.23-1.5-.99-.65-.35-1.01.22-1.59.15-.15 2.71-2.48 2.76-2.69.01-.03.01-.14-.07-.2-.08-.06-.19-.04-.27-.02-.12.03-1.99 1.27-5.62 3.72-.53.36-1.01.54-1.44.53-.47-.01-1.38-.27-2.06-.49-.83-.27-1.49-.42-1.43-.88.03-.24.37-.49 1.02-.75 3.99-1.73 6.65-2.88 7.99-3.44 3.81-1.58 4.6-1.86 5.12-1.87.11 0 .37.03.54.17.14.12.18.28.2.46-.01.06.01.24 0 .38z"/>
                </svg>
                @else
                <svg class="w-12 h-12 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                </svg>
                @endif
            </div>

            {{-- Text --}}
            <h2 class="text-2xl font-bold text-secondary-900 mb-3">Segera Hadir!</h2>
            <p class="text-secondary-500 text-sm leading-relaxed mb-8">
                Laporan Interaksi untuk <span class="font-bold text-primary-600">{{ $platform }}</span> sedang dalam pengembangan. 
                Fitur ini akan tersedia dalam pembaruan mendatang.
            </p>

            {{-- Badge --}}
            <div class="inline-flex items-center gap-2 px-4 py-2 bg-amber-50 border border-amber-200 rounded-xl">
                <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span class="text-xs font-bold text-amber-700">Coming Soon</span>
            </div>

            {{-- Back Button --}}
            <div class="mt-8">
                @php
                    $waRoute = auth()->user()->isAdmin() ? route('admin.laporan.interaksi.wa') : route('pengguna.laporan.interaksi.wa');
                @endphp
                <a href="{{ $waRoute }}" class="inline-flex items-center gap-2 px-6 py-2.5 bg-primary-600 text-white rounded-xl text-sm font-bold hover:bg-primary-700 shadow-sm transition-all active:scale-95">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    Kembali ke WhatsApp
                </a>
            </div>
        </div>
    </div>
</x-aiagen-layout>
