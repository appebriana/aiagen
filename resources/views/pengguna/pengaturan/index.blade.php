<x-pengaturan-layout>
    <x-slot name="header">Dashboard Pengaturan</x-slot>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        {{-- Profile Card --}}
        <a href="{{ route('pengguna.profile.edit') }}" class="group bg-white p-6 rounded-2xl shadow-sm border border-secondary-200 hover:border-primary-500 hover:shadow-md transition-all">
            <div class="w-12 h-12 bg-primary-100 text-primary-600 rounded-xl flex items-center justify-center mb-4 group-hover:bg-primary-600 group-hover:text-white transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
            </div>
            <h3 class="text-lg font-bold text-secondary-900 mb-1">Profil Saya</h3>
            <p class="text-sm text-secondary-500">Perbarui informasi pribadi, email, dan keamanan akun Anda.</p>
        </a>

        {{-- Notifications (Placeholder) --}}
        <div class="group bg-secondary-50 p-6 rounded-2xl border border-dashed border-secondary-300 opacity-75">
            <div class="w-12 h-12 bg-secondary-200 text-secondary-400 rounded-xl flex items-center justify-center mb-4">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
            </div>
            <h3 class="text-lg font-bold text-secondary-400 mb-1">Notifikasi</h3>
            <p class="text-sm text-secondary-400">Segera hadir: Atur preferensi pemberitahuan akun Anda.</p>
        </div>
    </div>
</x-pengaturan-layout>
