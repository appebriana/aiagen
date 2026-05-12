<x-admin-layout>
    <x-slot name="header">
        Daftar Masukan Pengguna
    </x-slot>

    <div class="space-y-6">
        @if (session('success'))
            <div class="p-4 bg-green-50 border border-green-200 rounded-2xl text-green-700 text-sm font-medium">
                {{ session('success') }}
            </div>
        @endif

        <div class="bg-white border border-secondary-200 rounded-2xl overflow-hidden shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="bg-secondary-50 border-b border-secondary-200 text-secondary-500 uppercase tracking-wider text-xs font-bold">
                        <tr>
                            <th class="px-6 py-4">Tanggal</th>
                            <th class="px-6 py-4">Nama Pengguna</th>
                            <th class="px-6 py-4">Masukan</th>
                            <th class="px-6 py-4">Status</th>
                            <th class="px-6 py-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-secondary-100">
                        @forelse ($feedbacks as $feedback)
                            <tr class="hover:bg-secondary-50/50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap text-secondary-600">
                                    {{ $feedback->created_at->format('d M Y, H:i') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap font-medium text-secondary-900">
                                    {{ $feedback->user->name ?? 'Pengguna Dihapus' }}
                                </td>
                                <td class="px-6 py-4 text-secondary-700 max-w-md truncate" title="{{ $feedback->message }}">
                                    {{ $feedback->message }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold
                                        {{ $feedback->status === 'selesai' ? 'bg-green-100 text-green-700' : 
                                          ($feedback->status === 'proses' ? 'bg-amber-100 text-amber-700' : 'bg-secondary-100 text-secondary-700') }}">
                                        {{ ucfirst($feedback->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <form action="{{ route('admin.feedback.updateStatus', $feedback->id) }}" method="POST" class="inline-flex gap-2">
                                        @csrf
                                        @method('PUT')
                                        <select name="status" onchange="this.form.submit()" class="text-sm border-secondary-300 rounded-xl focus:ring-primary-500 focus:border-primary-500">
                                            <option value="draf" {{ $feedback->status === 'draf' ? 'selected' : '' }}>Draf</option>
                                            <option value="proses" {{ $feedback->status === 'proses' ? 'selected' : '' }}>Proses</option>
                                            <option value="selesai" {{ $feedback->status === 'selesai' ? 'selected' : '' }}>Selesai</option>
                                        </select>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center text-secondary-500">
                                    <div class="flex flex-col items-center justify-center">
                                        <svg class="w-12 h-12 text-secondary-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" /></svg>
                                        <p>Belum ada masukan dari pengguna.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if ($feedbacks->hasPages())
                <div class="px-6 py-4 border-t border-secondary-200">
                    {{ $feedbacks->links() }}
                </div>
            @endif
        </div>
    </div>
</x-admin-layout>
