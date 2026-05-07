<x-pengaturan-layout>
    <x-slot name="header">Dashboard Pengaturan</x-slot>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        {{-- User Management Card --}}
        <a href="{{ route('admin.users.index') }}" class="group bg-white p-6 rounded-2xl shadow-sm border border-secondary-200 hover:border-primary-500 hover:shadow-md transition-all">
            <div class="w-12 h-12 bg-primary-100 text-primary-600 rounded-xl flex items-center justify-center mb-4 group-hover:bg-primary-600 group-hover:text-white transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
            </div>
            <h3 class="text-lg font-bold text-secondary-900 mb-1">Manajemen Pengguna</h3>
            <p class="text-sm text-secondary-500">Kelola daftar pengguna, role, dan hak akses sistem.</p>
        </a>

        {{-- Profile Card --}}
        <a href="{{ route('admin.profile.edit') }}" class="group bg-white p-6 rounded-2xl shadow-sm border border-secondary-200 hover:border-primary-500 hover:shadow-md transition-all">
            <div class="w-12 h-12 bg-secondary-100 text-secondary-600 rounded-xl flex items-center justify-center mb-4 group-hover:bg-primary-600 group-hover:text-white transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
            </div>
            <h3 class="text-lg font-bold text-secondary-900 mb-1">Profil Saya</h3>
            <p class="text-sm text-secondary-500">Perbarui informasi pribadi, email, dan keamanan akun.</p>
        </a>

        {{-- Log Activity (Placeholder) --}}
        <div class="group bg-secondary-50 p-6 rounded-2xl border border-dashed border-secondary-300 opacity-75">
            <div class="w-12 h-12 bg-secondary-200 text-secondary-400 rounded-xl flex items-center justify-center mb-4">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <h3 class="text-lg font-bold text-secondary-400 mb-1">Log Aktivitas</h3>
            <p class="text-sm text-secondary-400">Segera hadir: Pantau riwayat aktivitas admin di sistem.</p>
        </div>
    </div>
</x-pengaturan-layout>
