<x-aiagen-layout>
    <x-slot name="header">
        Knowledge Base - Departemen
    </x-slot>

    <div class="space-y-6" x-data="{ 
        showUploadModal: false, 
        uploadType: 'file' 
    }">
        {{-- Header & Action --}}
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h2 class="text-xl font-bold text-secondary-900">Arsip Pengetahuan</h2>
                <p class="text-sm text-secondary-500">Kelola dokumen dan link sumber pengetahuan AI per departemen.</p>
            </div>
            <button @click="showUploadModal = true" 
                    class="w-full md:w-auto px-5 py-2.5 bg-primary-600 text-white text-sm font-bold rounded-xl hover:bg-primary-700 transition-all shadow-lg shadow-primary-500/20 flex items-center justify-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Tambah Knowledge
            </button>
        </div>

        {{-- Files List --}}
        <div class="space-y-4">
            {{-- Desktop Table --}}
            <div class="hidden md:block bg-white rounded-2xl shadow-sm border border-secondary-200 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-secondary-50/50 border-b border-secondary-100">
                                <th class="px-6 py-4 text-xs font-bold text-secondary-500 uppercase tracking-wider">Departemen</th>
                                <th class="px-6 py-4 text-xs font-bold text-secondary-500 uppercase tracking-wider">Sumber Pengetahuan</th>
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
                                    <td class="px-6 py-4">
                                        @if($file->type === 'file')
                                            <div class="flex items-center gap-2">
                                                <svg class="w-4 h-4 text-secondary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                                                <span class="text-sm font-bold text-secondary-900">{{ $file->original_name }}</span>
                                            </div>
                                        @else
                                            <div class="flex flex-col">
                                                <div class="flex items-center gap-2">
                                                    <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                                    <span class="text-sm font-bold text-secondary-900">Google Spreadsheet</span>
                                                </div>
                                                <p class="text-[10px] text-secondary-400 mt-1 max-w-xs truncate">{{ $file->url }}</p>
                                            </div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($file->type === 'file')
                                            <span class="text-xs text-secondary-500 uppercase">{{ $file->file_type }}</span>
                                        @else
                                            <span class="text-xs text-green-600 font-bold uppercase">External Link</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <div class="flex items-center justify-end gap-1">
                                            @if($file->type === 'file')
                                                <a href="{{ auth()->user()->isAdmin() ? route('admin.ai-agen.knowledge.download', $file) : route('pengguna.ai-agen.knowledge.download', $file) }}" 
                                                   class="p-2 text-secondary-400 hover:text-primary-600 transition-colors" title="Unduh File">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a2 2 0 002 2h12a2 2 0 002-2v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                                </a>
                                            @else
                                                <a href="{{ $file->url }}" target="_blank" class="p-2 text-secondary-400 hover:text-green-600 transition-colors" title="Buka Spreadsheet">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                                </a>
                                            @endif
                                            <form action="{{ auth()->user()->isAdmin() ? route('admin.ai-agen.knowledge.destroy', $file) : route('pengguna.ai-agen.knowledge.destroy', $file) }}" method="POST" onsubmit="return confirm('Hapus sumber pengetahuan ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="p-2 text-secondary-400 hover:text-red-500 transition-colors">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="px-6 py-12 text-center text-secondary-400">Belum ada dokumen pengetahun.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Mobile Card List --}}
            <div class="grid grid-cols-1 gap-4 md:hidden">
                @forelse($files as $file)
                    <div class="bg-white p-4 rounded-2xl border border-secondary-200 shadow-sm space-y-3">
                        <div class="flex items-start justify-between gap-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0 {{ $file->type === 'file' ? 'bg-primary-50 text-primary-600' : 'bg-green-50 text-green-600' }}">
                                    @if($file->type === 'file')
                                        @if($file->file_type == 'pdf')
                                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"><path d="M9 2a2 2 0 00-2 2v8a2 2 0 002 2h6a2 2 0 002-2V6.414A2 2 0 0016.414 5L14 2.586A2 2 0 0012.586 2H9z"/><path d="M3 8a2 2 0 012-2v10h8a2 2 0 01-2 2H5a2 2 0 01-2-2V8z"/></svg>
                                        @else
                                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"/></svg>
                                        @endif
                                    @else
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    @endif
                                </div>
                                <div class="min-w-0">
                                    <h4 class="text-sm font-bold text-secondary-900 truncate pr-2">{{ $file->type === 'file' ? $file->original_name : 'Google Spreadsheet' }}</h4>
                                    <div class="flex items-center gap-2 mt-0.5">
                                        <span class="text-[10px] font-bold text-secondary-400 uppercase">{{ $file->type === 'file' ? $file->file_type : 'Cloud Link' }}</span>
                                        <span class="w-1 h-1 rounded-full bg-secondary-300"></span>
                                        <span class="text-[10px] font-bold text-primary-600 uppercase">{{ $file->department->name }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="flex items-center gap-1">
                                @if($file->type === 'file')
                                    <a href="{{ auth()->user()->isAdmin() ? route('admin.ai-agen.knowledge.download', $file) : route('pengguna.ai-agen.knowledge.download', $file) }}" 
                                       class="p-2 bg-primary-50 text-primary-600 rounded-xl">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a2 2 0 002 2h12a2 2 0 002-2v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                    </a>
                                @else
                                    <a href="{{ $file->url }}" target="_blank" class="p-2 bg-green-50 text-green-600 rounded-xl">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                    </a>
                                @endif
                                <form action="{{ auth()->user()->isAdmin() ? route('admin.ai-agen.knowledge.destroy', $file) : route('pengguna.ai-agen.knowledge.destroy', $file) }}" method="POST" onsubmit="return confirm('Hapus sumber pengetahuan ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2 bg-red-50 text-red-500 rounded-xl">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="bg-white p-12 rounded-2xl border border-secondary-200 text-center text-secondary-400 text-sm">
                        Belum ada dokumen pengetahun.
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Upload Modal --}}
        <div x-show="showUploadModal" class="fixed inset-0 z-[110] overflow-y-auto" x-cloak>
            <div class="flex items-center justify-center min-h-screen p-4 text-center">
                <div class="fixed inset-0 bg-secondary-900/60 transition-opacity" @click="showUploadModal = false"></div>
                <div class="inline-block align-middle bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:max-w-xl sm:w-full">
                    <form action="{{ auth()->user()->isAdmin() ? route('admin.ai-agen.knowledge.store') : route('pengguna.ai-agen.knowledge.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="px-5 py-5 sm:px-6 sm:py-6">
                            <div class="flex items-center justify-between mb-6">
                                <h3 class="text-base sm:text-lg font-bold text-secondary-900">Tambah Pengetahuan</h3>
                                <button type="button" @click="showUploadModal = false" class="text-secondary-400 hover:text-secondary-600">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                </button>
                            </div>

                            <div class="space-y-6">
                                {{-- Toggle Type inside Modal (Enhanced) --}}
                                <div class="relative flex bg-secondary-100 p-1 rounded-2xl w-full overflow-hidden">
                                    {{-- Sliding Background --}}
                                    <div class="absolute inset-y-1 transition-all duration-300 ease-out bg-white rounded-xl shadow-sm"
                                         :style="uploadType === 'file' ? 'left: 4px; width: calc(50% - 6px);' : 'left: 50%; width: calc(50% - 4px);'">
                                    </div>
                                    
                                    <button type="button" @click.prevent="uploadType = 'file'" 
                                            class="relative z-10 flex-1 py-2.5 rounded-xl text-xs font-bold transition-colors duration-300 flex items-center justify-center gap-2"
                                            :class="uploadType === 'file' ? 'text-primary-600' : 'text-secondary-500 hover:text-secondary-700'">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                                        Upload File
                                    </button>
                                    <button type="button" @click.prevent="uploadType = 'url'" 
                                            class="relative z-10 flex-1 py-2.5 rounded-xl text-xs font-bold transition-colors duration-300 flex items-center justify-center gap-2"
                                            :class="uploadType === 'url' ? 'text-green-600' : 'text-secondary-500 hover:text-secondary-700'">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                        Google Sheet
                                    </button>
                                </div>

                                <input type="hidden" name="type" :value="uploadType">
                                
                                <div class="space-y-4">
                                    @if($departments->isEmpty())
                                        <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 flex items-center gap-3">
                                            <svg class="w-5 h-5 text-amber-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                                            <p class="text-sm text-amber-700">Anda belum memiliki Departemen. Silakan <a href="{{ route(auth()->user()->isAdmin() ? 'admin.ai-agen.departments.index' : 'pengguna.ai-agen.departments.index') }}" class="font-bold underline">buat departemen</a> terlebih dahulu.</p>
                                        </div>
                                    @else
                                        <div>
                                            <label class="block text-[10px] font-bold text-secondary-700 uppercase mb-1.5 ml-1">Pilih Departemen <span class="text-red-500">*</span></label>
                                            <select name="department_id" required class="w-full bg-secondary-50 border border-secondary-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary-500">
                                                <option value="">-- Pilih Departemen --</option>
                                                @foreach($departments as $dept)
                                                    <option value="{{ $dept->id }}">
                                                        {{ $dept->name }} @if(auth()->user()->isAdmin()) (Owner: {{ $dept->user->name }}) @endif
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>

                                        {{-- Dynamic Content Area --}}
                                        <div class="relative min-h-[140px]">
                                            <div x-show="uploadType === 'file'" 
                                                 x-transition:enter="transition ease-out duration-300"
                                                 x-transition:enter-start="opacity-0 translate-y-2"
                                                 x-transition:enter-end="opacity-100 translate-y-0"
                                                 class="space-y-4">
                                                <label class="block text-[10px] font-bold text-secondary-700 uppercase mb-1.5 ml-1">Pilih File (PDF/TXT) <span class="text-red-500">*</span></label>
                                                <input type="file" name="file" accept=".pdf,.txt" :required="uploadType === 'file'"
                                                       class="w-full text-sm text-secondary-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-primary-50 file:text-primary-700 border border-secondary-200 rounded-xl p-1">
                                                <p class="text-[9px] text-secondary-400 mt-2 ml-1">* Ukuran maksimal file adalah 5MB.</p>
                                            </div>

                                            <div x-show="uploadType === 'url'" 
                                                 x-transition:enter="transition ease-out duration-300"
                                                 x-transition:enter-start="opacity-0 translate-y-2"
                                                 x-transition:enter-end="opacity-100 translate-y-0"
                                                 class="space-y-4">
                                                <label class="block text-[10px] font-bold text-green-700 uppercase mb-1.5 ml-1">Link Google Spreadsheet <span class="text-red-500">*</span></label>
                                                <input type="url" name="url" placeholder="https://docs.google.com/spreadsheets/d/..." :required="uploadType === 'url'"
                                                       class="w-full bg-green-50/30 border border-green-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-green-500">
                                                <div class="mt-3 p-3 bg-secondary-50 rounded-xl border border-secondary-100">
                                                    <p class="text-[10px] font-bold text-secondary-900 mb-1">Cara Share:</p>
                                                    <ol class="text-[9px] text-secondary-500 space-y-1 list-decimal ml-3">
                                                        <li>Klik tombol <span class="font-bold text-blue-600">Share</span> di Google Sheet.</li>
                                                        <li>Ubah akses ke <span class="font-bold">"Anyone with the link"</span>.</li>
                                                        <li>Salin link-nya dan tempel di sini.</li>
                                                    </ol>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="bg-secondary-50 px-6 py-4 pb-10 sm:pb-4 flex justify-end gap-3">
                            <button type="button" @click="showUploadModal = false" class="text-sm font-bold text-secondary-500">Batal</button>
                            <button type="submit" class="px-6 py-2 bg-primary-600 text-white text-sm font-bold rounded-xl hover:bg-primary-700 shadow-lg shadow-primary-500/20">
                                Simpan Pengetahuan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-aiagen-layout>
