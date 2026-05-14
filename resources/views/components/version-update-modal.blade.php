@props(['version' => '1.1.0'])

<div x-data="{ 
        showUpdateModal: false, 
        dontShowAgain: false,
        version: '{{ $version }}',
        init() {
            const isPermanentlyDismissed = localStorage.getItem('aiagen_dismissed_' + this.version);
            const isSessionDismissed = sessionStorage.getItem('aiagen_session_dismissed_' + this.version);

            if (!isPermanentlyDismissed && !isSessionDismissed) {
                setTimeout(() => {
                    this.showUpdateModal = true;
                }, 1000);
            }
        },
        dismiss() {
            if (this.dontShowAgain) {
                localStorage.setItem('aiagen_dismissed_' + this.version, 'true');
            } else {
                // If not checked, only dismiss for this session
                sessionStorage.setItem('aiagen_session_dismissed_' + this.version, 'true');
            }
            this.showUpdateModal = false;
        }
    }"
    x-show="showUpdateModal"
    x-cloak
    class="fixed inset-0 z-[9999] flex items-center justify-center p-4 bg-secondary-900/60 backdrop-blur-sm"
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100">
    
    <div class="bg-white rounded-3xl md:rounded-[2.5rem] shadow-2xl w-full max-w-xl overflow-hidden border border-white/20 relative"
         x-transition:enter="transition ease-out duration-500 cubic-bezier(0.34, 1.56, 0.64, 1)"
         x-transition:enter-start="opacity-0 scale-90 translate-y-10"
         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
         @click.away="dismiss()">
         
        {{-- Header with Gradient Background --}}
        <div class="bg-gradient-to-br from-primary-600 via-primary-500 to-primary-700 p-6 md:p-8 text-white relative">
            <div class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full -mr-16 -mt-16 blur-2xl"></div>
            <div class="absolute bottom-0 left-0 w-24 h-24 bg-primary-400/20 rounded-full -ml-12 -mb-12 blur-xl"></div>
            
            <div class="relative flex items-center gap-3 md:gap-4">
                <div class="w-12 h-12 md:w-14 md:h-14 bg-white/20 backdrop-blur-md rounded-2xl flex items-center justify-center text-xl md:text-2xl shadow-xl border border-white/30">
                    🚀
                </div>
                <div>
                    <h2 class="text-xl md:text-2xl font-black tracking-tight">Pembaruan Sistem</h2>
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] md:text-xs font-bold bg-white text-primary-600 mt-1">
                        Versi {{ $version }}
                    </span>
                </div>
            </div>
        </div>

        <div class="p-6 md:p-8">
            <p class="text-secondary-600 text-xs md:text-sm mb-6 leading-relaxed">
                Halo! Kami baru saja merilis pembaruan untuk meningkatkan pengalaman Anda menggunakan <strong>AIAGEN</strong>. Berikut adalah rincian perubahannya:
            </p>

            {{-- Feature List --}}
            <div class="space-y-3 md:space-y-4 mb-8">
                <div class="flex gap-3 md:gap-4 p-2 md:p-3 rounded-2xl hover:bg-secondary-50 transition-colors group">
                    <div class="w-10 h-10 bg-blue-50 text-blue-500 rounded-xl flex items-center justify-center flex-shrink-0 group-hover:scale-110 transition-transform">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2m9-10a4 4 0 100-8 4 4 0 000 8zm6 5H12a3 3 0 00-3 3v8m3-13a3 3 0 100-6 3 3 0 000 6z"/></svg>
                    </div>
                    <div>
                        <h4 class="text-sm font-bold text-secondary-900">Multi-Platform CMS</h4>
                        <p class="text-[11px] md:text-xs text-secondary-500">Integrasi penuh WhatsApp, Telegram, IG, dan FB dalam satu dashboard.</p>
                    </div>
                </div>

                <div class="flex gap-3 md:gap-4 p-2 md:p-3 rounded-2xl hover:bg-secondary-50 transition-colors group">
                    <div class="w-10 h-10 bg-amber-50 text-amber-500 rounded-xl flex items-center justify-center flex-shrink-0 group-hover:scale-110 transition-transform">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    </div>
                    <div>
                        <h4 class="text-sm font-bold text-secondary-900">Filter Pengguna (Admin)</h4>
                        <p class="text-[11px] md:text-xs text-secondary-500">Admin kini dapat mengelola departemen berdasarkan filter pengguna spesifik.</p>
                    </div>
                </div>

                <div class="flex gap-3 md:gap-4 p-2 md:p-3 rounded-2xl hover:bg-secondary-50 transition-colors group">
                    <div class="w-10 h-10 bg-emerald-50 text-emerald-500 rounded-xl flex items-center justify-center flex-shrink-0 group-hover:scale-110 transition-transform">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    </div>
                    <div>
                        <h4 class="text-sm font-bold text-secondary-900">Takeover & AI Control</h4>
                        <p class="text-[11px] md:text-xs text-secondary-500">Kendali penuh untuk interupsi AI Agen dan manajemen chat manual.</p>
                    </div>
                </div>

                <div class="flex gap-3 md:gap-4 p-2 md:p-3 rounded-2xl hover:bg-secondary-50 transition-colors group">
                    <div class="w-10 h-10 bg-purple-50 text-purple-500 rounded-xl flex items-center justify-center flex-shrink-0 group-hover:scale-110 transition-transform">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z"/></svg>
                    </div>
                    <div>
                        <h4 class="text-sm font-bold text-secondary-900">Stabilitas Layout CMS</h4>
                        <p class="text-[11px] md:text-xs text-secondary-500">Perbaikan total struktur flexbox dan UI untuk pengalaman chat lebih mulus.</p>
                    </div>
                </div>
            </div>

            {{-- Footer Controls --}}
            <div class="flex flex-col gap-4">
                <label class="flex items-center gap-3 cursor-pointer group">
                    <div class="relative">
                        <input type="checkbox" x-model="dontShowAgain" class="sr-only">
                        <div class="w-5 h-5 md:w-6 md:h-6 border-2 border-secondary-300 rounded-lg transition-all group-hover:border-primary-500 flex items-center justify-center"
                             :class="dontShowAgain ? 'bg-primary-600 border-primary-600 shadow-lg shadow-primary-500/30' : 'bg-white'">
                            <svg x-show="dontShowAgain" class="w-3.5 h-3.5 md:w-4 md:h-4 text-white" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        </div>
                    </div>
                    <span class="text-[10px] md:text-xs font-bold text-secondary-500 uppercase tracking-wider">Jangan tampilkan lagi</span>
                </label>

                <button @click="dismiss()" 
                        class="w-full py-4 bg-primary-600 hover:bg-primary-700 text-white rounded-2xl font-black text-xs md:text-sm transition-all shadow-xl shadow-primary-500/25 active:scale-95 uppercase tracking-[0.2em]">
                    SAYA MENGERTI, LANJUTKAN
                </button>
            </div>
        </div>
    </div>
</div>
