<x-aiagen-layout>
    <x-slot name="header">
        Knowledge Base - Departemen
    </x-slot>

    <div class="space-y-6">
        {{-- Upload Section --}}
        <div class="bg-white rounded-2xl shadow-sm border border-secondary-200 overflow-hidden">
            <div class="p-6 border-b border-secondary-200">
                <h3 class="text-lg font-bold text-secondary-900">Unggah Dokumen Pengetahuan</h3>
                <p class="text-sm text-secondary-500">Pilih departemen agar AI menjawab sesuai dengan lingkup pengetahuan tersebut.</p>
            </div>
            <div class="p-6">
                @if($departments->isEmpty())
                    <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 flex items-center gap-3">
                        <svg class="w-5 h-5 text-amber-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                        <p class="text-sm text-amber-700">Anda belum memiliki Departemen. Silakan <a href="{{ route(auth()->user()->isAdmin() ? 'admin.ai-agen.departments.index' : 'pengguna.ai-agen.departments.index') }}" class="font-bold underline">buat departemen</a> terlebih dahulu.</p>
                    </div>
                @else
                    <form action="{{ auth()->user()->isAdmin() ? route('admin.ai-agen.knowledge.store') : route('pengguna.ai-agen.knowledge.store') }}" method="POST" enctype="multipart/form-data" class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
                        @csrf
                        <div class="md:col-span-1">
                            <label class="block text-xs font-bold text-secondary-700 uppercase tracking-wider mb-2">Pilih Departemen</label>
                            <select name="department_id" required class="w-full bg-secondary-50 border border-secondary-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary-500">
                                <option value="">-- Pilih Departemen --</option>
                                @foreach($departments as $dept)
                                    <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="md:col-span-1">
                            <label class="block text-xs font-bold text-secondary-700 uppercase tracking-wider mb-2">Pilih File (PDF/TXT)</label>
                            <input type="file" name="file" accept=".pdf,.txt" required
                                   class="w-full text-sm text-secondary-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-primary-50 file:text-primary-700 border border-secondary-200 rounded-xl p-1">
                        </div>
                        <div class="md:col-span-1">
                            <button type="submit" class="w-full px-6 py-2.5 bg-primary-600 text-white text-sm font-bold rounded-xl hover:bg-primary-700 transition-colors shadow-lg shadow-primary-500/20">
                                Unggah ke Departemen
                            </button>
                        </div>
                    </form>
                @endif
            </div>
        </div>

        {{-- Files List --}}
        <div class="bg-white rounded-2xl shadow-sm border border-secondary-200 overflow-hidden">
            <div class="p-6 border-b border-secondary-200">
                <h3 class="text-lg font-bold text-secondary-900">Arsip Pengetahuan</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-secondary-50/50">
                            <th class="px-6 py-4 text-xs font-bold text-secondary-500 uppercase tracking-wider">Departemen</th>
                            <th class="px-6 py-4 text-xs font-bold text-secondary-500 uppercase tracking-wider">Nama File</th>
                            <th class="px-6 py-4 text-xs font-bold text-secondary-500 uppercase tracking-wider">Tipe</th>
                            <th class="px-6 py-4 text-xs font-bold text-secondary-500 uppercase tracking-wider text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-secondary-100">
                        @forelse($files as $file)
                            <tr class="hover:bg-secondary-50/30 transition-colors">
                                <td class="px-6 py-4">
                                    <span class="px-3 py-1 bg-primary-50 text-primary-700 rounded-full text-[10px] font-bold uppercase border border-primary-100">
                                        {{ $file->department->name }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm font-bold text-secondary-900">
                                    {{ $file->original_name }}
                                </td>
                                <td class="px-6 py-4">
                                    <span class="text-xs text-secondary-500 uppercase">{{ $file->file_type }}</span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <form action="{{ auth()->user()->isAdmin() ? route('admin.ai-agen.knowledge.destroy', $file) : route('pengguna.ai-agen.knowledge.destroy', $file) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-500 hover:text-red-700 transition-colors">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="px-6 py-12 text-center text-secondary-400">Belum ada dokumen pengetahun.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-aiagen-layout>
