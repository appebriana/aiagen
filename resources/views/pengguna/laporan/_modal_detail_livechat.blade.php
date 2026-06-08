{{-- REUSABLE MODAL DETAIL LIVECHAT --}}
<div x-data="{ 
        isModalOpen: false, 
        phone: '', 
        name: '', 
        logs: [], 
        loading: false,
        init() {
            // No manual scroll needed with flex-col-reverse
        },
        async fetchLogs() {
            this.loading = true;
            this.logs = [];
            try {
                const prefix = '{{ auth()->user()->isAdmin() ? '/admin' : '/pengguna' }}';
                const userParam = '{{ isset($selectedUser) && $selectedUser ? '&user_id=' . $selectedUser->id : '' }}';
                const rangeParam = '{{ $range ?? 'harian' }}';
                const response = await fetch(`${prefix}/laporan/interaksi/livechat/detail/${this.phone}?range=${rangeParam}${userParam}`);
                const result = await response.json();
                if (result.status === 'success') {
                    this.logs = result.data;
                }
            } catch (error) {
                console.error('Gagal mengambil log:', error);
            } finally {
                this.loading = false;
            }
        }
     }" 
      x-show="isModalOpen" 
      @open-detail.window="isModalOpen = true; phone = $event.detail.phone; name = $event.detail.name; fetchLogs()"
      class="fixed inset-0 z-[60] overflow-y-auto" 
      x-cloak>
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <div x-show="isModalOpen" x-transition.opacity @click="isModalOpen = false" class="fixed inset-0 transition-opacity bg-secondary-900/75 backdrop-blur-sm"></div>

        <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>

        <div x-show="isModalOpen" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
             class="inline-block w-full max-w-4xl overflow-hidden text-left align-middle transition-all transform bg-white shadow-2xl rounded-3xl sm:my-8">
            
            {{-- Modal Header --}}
            <div class="px-6 py-4 border-b border-secondary-100 flex items-center justify-between bg-secondary-50">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-indigo-600 rounded-full flex items-center justify-center text-white font-bold">
                        <span x-text="name ? name.substring(0, 1).toUpperCase() : '?'"></span>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-secondary-900" x-text="name"></h3>
                        <p class="text-xs text-secondary-500" x-text="phone"></p>
                    </div>
                </div>
                <button @click="isModalOpen = false" class="text-secondary-400 hover:text-secondary-600 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            {{-- Modal Body (Chat Logs) --}}
            <div class="p-6 max-h-[65vh] overflow-y-auto bg-[#efeae2] flex flex-col-reverse" style="font-family: 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;">
                <template x-if="loading">
                    <div class="flex justify-center py-12">
                        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-indigo-600"></div>
                    </div>
                </template>

                <template x-if="!loading && logs.length === 0">
                    <div class="text-center py-12 text-secondary-500 italic bg-white/50 rounded-2xl">Tidak ada riwayat percakapan.</div>
                </template>

                {{-- AI Session Summary (If Available) --}}
                <template x-if="!loading && logs.length > 0 && logs[0].context_summary">
                    <div class="mb-6 px-4 py-3 bg-indigo-50 border border-indigo-100 rounded-2xl shadow-sm">
                        <div class="flex items-center gap-2 mb-2">
                            <div class="w-6 h-6 bg-indigo-600 rounded-lg flex items-center justify-center">
                                <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                            </div>
                            <span class="text-[10px] font-black uppercase tracking-widest text-indigo-700">Ringkasan Konteks AI</span>
                        </div>
                        <p class="text-[13px] text-secondary-700 leading-relaxed italic" x-text="logs[0].context_summary"></p>
                    </div>
                </template>

                <template x-for="log in logs" :key="log.id">
                    <div class="flex flex-col space-y-1 mb-4">
                        {{-- Customer Message (Left) --}}
                        <div class="flex justify-start">
                            <div class="max-w-[85%] bg-white text-[#111b21] p-2.5 px-4 rounded-2xl rounded-tl-none shadow-sm relative border-b border-secondary-200">
                                <p class="text-[13.5px] leading-relaxed" x-text="log.question"></p>
                                <div class="flex justify-end mt-1">
                                    <span class="text-[10px] text-[#667781]" x-text="log.formatted_date.split(' ').pop()"></span>
                                </div>
                            </div>
                        </div>
                        
                        {{-- AI/Admin Response (Right) --}}
                        <div class="flex justify-end">
                            <div class="max-w-[85%] bg-indigo-50 text-[#111b21] p-2.5 px-4 rounded-2xl rounded-tr-none shadow-sm relative border-b border-indigo-200/50">
                                <div class="flex items-center gap-1.5 mb-0.5 opacity-70">
                                    <span class="text-[9px] font-bold uppercase tracking-widest text-indigo-700" 
                                          x-text="log.model === 'MANUAL_ADMIN' ? 'Admin' : 'AI Agent'"></span>
                                </div>
                                <p class="text-[13.5px] leading-relaxed whitespace-pre-wrap" x-text="log.answer"></p>
                                <div class="flex items-center justify-end gap-2 mt-1">
                                    <p class="text-[10px] text-[#667781]" x-text="log.formatted_date.split(' ').pop()"></p>
                                    
                                    <template x-if="log.sentiment">
                                        <span class="text-xs" :title="log.sentiment" x-text="log.sentiment === 'positive' ? '😊' : (log.sentiment === 'negative' ? '😠' : '😐')"></span>
                                    </template>

                                    <template x-if="log.rating">
                                        <div class="flex items-center gap-0.5 bg-black/5 px-1.5 py-0.5 rounded-full">
                                            <span class="text-[9px] font-bold" x-text="log.rating"></span>
                                            <svg class="w-2 h-2 fill-current text-amber-500" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                        </div>
                                    </template>
                                    
                                    {{-- Double Checkmark --}}
                                    <svg class="w-3.5 h-3.5 text-indigo-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M3 12l4 4L18 4M9 18l4 4L22 8"/></svg>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>
            </div>

            {{-- Modal Footer --}}
            <div class="px-6 py-4 bg-white border-t border-secondary-100 flex justify-end">
                <button @click="isModalOpen = false" class="px-6 py-2 bg-secondary-100 text-secondary-700 rounded-xl font-bold hover:bg-secondary-200 transition-colors">Tutup</button>
            </div>
        </div>
    </div>
</div>
