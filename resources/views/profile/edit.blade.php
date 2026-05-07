<x-pengaturan-layout>
    <x-slot name="header">Profil Saya</x-slot>

    <div class="space-y-6">
        {{-- Hero Section --}}
        <div class="bg-white rounded-2xl shadow-sm border border-secondary-200 overflow-hidden">
            <div class="p-8 flex flex-col md:flex-row items-center gap-8">
                <div class="w-24 h-24 bg-primary-100 text-primary-600 rounded-3xl flex items-center justify-center flex-shrink-0 shadow-lg shadow-primary-500/20">
                    <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                </div>
                <div class="text-center md:text-left flex-1">
                    <h2 class="text-2xl font-bold text-secondary-900 mb-2">Pengaturan Profil</h2>
                    <p class="text-secondary-500 max-w-2xl">Kelola informasi pribadi Anda, perbarui kata sandi, dan amankan akun Anda untuk performa terbaik di platform <strong>AIAGEN</strong>.</p>
                </div>
                <div class="flex-shrink-0">
                    <div class="px-4 py-2 bg-primary-50 text-primary-700 rounded-full text-xs font-bold flex items-center gap-2 border border-primary-100">
                        <span class="w-2 h-2 bg-primary-500 rounded-full"></span>
                        Akun {{ ucfirst(Auth::user()->role) }}
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            {{-- Update Profile Information --}}
            <div class="bg-white shadow-sm rounded-2xl border border-secondary-200 overflow-hidden">
                <div class="p-6 sm:p-8">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            {{-- Update Password --}}
            <div class="bg-white shadow-sm rounded-2xl border border-secondary-200 overflow-hidden">
                <div class="p-6 sm:p-8">
                    @include('profile.partials.update-password-form')
                </div>
            </div>
        </div>

    </div>
</x-pengaturan-layout>

