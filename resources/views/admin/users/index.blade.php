<x-pengaturan-layout>
    <x-slot name="header">Manajemen Pengguna</x-slot>

    {{-- Alert Success --}}
    @if (session('status'))
        <div class="mb-6 p-4 bg-green-50 border-l-4 border-green-500 rounded-r-lg flex items-center justify-between shadow-sm animate-fade-in-down" x-data="{ show: true }" x-show="show">
            <div class="flex items-center">
                <svg class="w-5 h-5 text-green-500 mr-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                <span class="text-sm font-medium text-green-800">
                    @if(session('status') == 'user-created') Pengguna berhasil ditambahkan. @endif
                    @if(session('status') == 'user-updated') Informasi pengguna berhasil diperbarui. @endif
                    @if(session('status') == 'user-deleted') Pengguna berhasil dihapus. @endif
                </span>
            </div>
            <button @click="show = false" class="text-green-500 hover:text-green-700 transition-colors"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
        </div>
    @endif

    {{-- Alert Error --}}
    @if ($errors->any())
        <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 rounded-r-lg flex items-center justify-between shadow-sm" x-data="{ show: true }" x-show="show">
            <div class="flex items-center">
                <svg class="w-5 h-5 text-red-500 mr-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                <span class="text-sm font-medium text-red-800">{{ $errors->first() }}</span>
            </div>
            <button @click="show = false" class="text-red-500 hover:text-red-700 transition-colors"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
        </div>
    @endif

    <div class="bg-white rounded-2xl shadow-sm border border-secondary-200 overflow-hidden">
        <div class="p-6 border-b border-secondary-200 flex flex-col sm:flex-row justify-between items-center gap-4 bg-white">
            <div>
                <h3 class="text-lg font-bold text-secondary-900">Daftar Pengguna</h3>
                <p class="text-sm text-secondary-500">Kelola semua akun pengguna dan hak akses sistem.</p>
            </div>
            <a href="{{ route('admin.users.create') }}" class="inline-flex items-center px-4 py-2 bg-primary-600 border border-transparent rounded-xl font-bold text-xs text-white uppercase tracking-widest hover:bg-primary-700 active:bg-primary-900 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition ease-in-out duration-150">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Tambah Pengguna
            </a>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-secondary-50 text-secondary-600 text-xs uppercase tracking-wider">
                        <th class="px-6 py-4 font-bold border-b border-secondary-200">User</th>
                        <th class="px-6 py-4 font-bold border-b border-secondary-200">Kontak</th>
                        <th class="px-6 py-4 font-bold border-b border-secondary-200 text-center">Role</th>
                        <th class="px-6 py-4 font-bold border-b border-secondary-200 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-secondary-100">
                    @forelse($users as $user)
                    <tr class="hover:bg-secondary-50 transition-colors group">
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-secondary-100 rounded-full flex items-center justify-center text-secondary-600 font-bold group-hover:bg-primary-100 group-hover:text-primary-600 transition-colors">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-bold text-secondary-900">{{ $user->name }}</div>
                                    <div class="text-xs text-secondary-500">{{ $user->username }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-secondary-600">{{ $user->email }}</div>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="inline-flex px-2.5 py-1 rounded-full text-[10px] font-black uppercase tracking-widest {{ $user->isAdmin() ? 'bg-primary-100 text-primary-700' : 'bg-secondary-100 text-secondary-600' }}">
                                {{ $user->role }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <div class="flex items-center justify-center gap-2">
                                <a href="{{ route('admin.users.edit', $user) }}" class="p-2 text-secondary-400 hover:text-primary-600 hover:bg-primary-50 rounded-lg transition-all" title="Edit Pengguna">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 00-2 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </a>
                                
                                <form action="{{ route('admin.users.destroy', $user) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pengguna ini? Tindakan ini tidak dapat dibatalkan.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2 text-secondary-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-all" title="Hapus Pengguna" {{ auth()->id() === $user->id ? 'disabled' : '' }}>
                                        <svg class="w-5 h-5 {{ auth()->id() === $user->id ? 'opacity-25' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-12 text-center text-secondary-500 italic">Tidak ada data pengguna.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($users->hasPages())
        <div class="p-6 bg-secondary-50 border-t border-secondary-200">
            {{ $users->links() }}
        </div>
        @endif
    </div>
</x-pengaturan-layout>
