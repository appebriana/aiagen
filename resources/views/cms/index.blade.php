<x-cms-layout :departments="$departments" :activeDepartment="$activeDepartment">
    <x-slot name="header">
        CMS Omnichannel - {{ $activeDepartment->name }}
    </x-slot>

    <div class="flex h-full overflow-hidden" 
         x-data="{ 
            activePhone: null, 
            activeName: '', 
            chats: [], 
            loading: false,
            message: '',
            isAiEnabled: true,
            
            async selectConversation(phone, name, aiStatus) {
                this.activePhone = phone;
                this.activeName = name;
                this.isAiEnabled = aiStatus;
                this.fetchChats();
            },

            async fetchChats() {
                if (!this.activePhone) return;
                this.loading = true;
                try {
                    const prefix = '{{ auth()->user()->isAdmin() ? '/admin' : '/pengguna' }}';
                    const response = await fetch(`${prefix}/cms/chats/{{ $activeDepartment->id }}/${this.activePhone}`);
                    const result = await response.json();
                    if (result.status === 'success') {
                        this.chats = result.data;
                        this.scrollToBottom();
                    }
                } catch (error) {
                    console.error('Error fetching chats:', error);
                } finally {
                    this.loading = false;
                }
            },

            async sendMessage() {
                if (!this.message.trim() || !this.activePhone) return;
                
                try {
                    const prefix = '{{ auth()->user()->isAdmin() ? '/admin' : '/pengguna' }}';
                    const response = await fetch(`${prefix}/cms/send`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            department_id: '{{ $activeDepartment->id }}',
                            phone: this.activePhone,
                            message: this.message
                        })
                    });

                    if (response.ok) {
                        this.message = '';
                        this.isAiEnabled = false; // Takeover otomatis
                        this.fetchChats();
                    }
                } catch (error) {
                    console.error('Error sending message:', error);
                }
            },

            async toggleAi() {
                try {
                    const prefix = '{{ auth()->user()->isAdmin() ? '/admin' : '/pengguna' }}';
                    const newStatus = this.isAiEnabled ? 0 : 1;
                    const response = await fetch(`${prefix}/laporan/interaksi/wa/toggle-ai`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            phone: this.activePhone,
                            status: newStatus,
                            user_id: '{{ $activeDepartment->user_id }}'
                        })
                    });

                    if (response.ok) {
                        this.isAiEnabled = !this.isAiEnabled;
                    }
                } catch (error) {
                    console.error('Error toggling AI:', error);
                }
            },

            scrollToBottom() {
                setTimeout(() => {
                    const container = document.getElementById('chat-scroll');
                    if (container) container.scrollTop = container.scrollHeight;
                }, 100);
            }
         }"
         x-init="setInterval(() => { if(activePhone && !loading) fetchChats() }, 5000)">
        
        {{-- 1. Conversation List (Left) --}}
        <div class="w-80 flex-shrink-0 border-r border-secondary-200 flex flex-col bg-secondary-50/30">
            <div class="p-4 border-b border-secondary-200 bg-white">
                <div class="relative">
                    <input type="text" placeholder="Cari percakapan..." class="w-full pl-9 pr-4 py-2 bg-secondary-100 border-none rounded-xl text-sm focus:ring-2 focus:ring-primary-500">
                    <svg class="w-4 h-4 text-secondary-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
            </div>
            <div class="flex-1 overflow-y-auto divide-y divide-secondary-100">
                @forelse($conversations as $conv)
                    <button @click="selectConversation('{{ $conv->customer_phone }}', '{{ $conv->customer_name }}', {{ $conv->is_ai_enabled ? 'true' : 'false' }})"
                            class="w-full p-4 flex items-start gap-3 hover:bg-white transition-all text-left group"
                            :class="activePhone === '{{ $conv->customer_phone }}' ? 'bg-white border-l-4 border-primary-600 shadow-sm' : ''">
                        <div class="relative flex-shrink-0">
                            <div class="w-12 h-12 bg-primary-100 text-primary-700 rounded-2xl flex items-center justify-center font-bold">
                                {{ strtoupper(substr($conv->customer_name, 0, 1)) }}
                            </div>
                            <div class="absolute -bottom-1 -right-1 w-4 h-4 rounded-full border-2 border-white"
                                 :class="{{ $conv->is_ai_enabled ? 'true' : 'false' }} ? 'bg-green-500' : 'bg-rose-500'"></div>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between mb-0.5">
                                <h4 class="text-sm font-bold text-secondary-900 truncate">{{ $conv->customer_name }}</h4>
                                <span class="text-[10px] text-secondary-400 font-medium whitespace-nowrap">{{ \Carbon\Carbon::parse($conv->last_chat)->format('H:i') }}</span>
                            </div>
                            <p class="text-xs text-secondary-500 truncate">{{ $conv->last_message }}</p>
                            <div class="flex items-center gap-2 mt-1.5">
                                <span class="px-1.5 py-0.5 rounded text-[8px] font-extrabold uppercase {{ $conv->is_ai_enabled ? 'bg-green-50 text-green-700' : 'bg-rose-50 text-rose-700 animate-pulse' }}">
                                    {{ $conv->is_ai_enabled ? 'AI Active' : 'Human Takeover' }}
                                </span>
                            </div>
                        </div>
                    </button>
                @empty
                    <div class="p-8 text-center text-secondary-400">
                        <p class="text-sm">Belum ada percakapan di departemen ini.</p>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- 2. Chat Area (Middle) --}}
        <div class="flex-1 flex flex-col bg-white">
            <template x-if="activePhone">
                <div class="flex flex-col h-full">
                    {{-- Chat Content --}}
                    <div class="flex-1 overflow-y-auto p-6 space-y-4 bg-secondary-50/50" id="chat-scroll">
                        <template x-for="chat in chats" :key="chat.id">
                            <div class="space-y-3">
                                {{-- User Message --}}
                                <div class="flex justify-start">
                                    <div class="max-w-[75%] bg-white p-3 rounded-2xl rounded-tl-none shadow-sm border border-secondary-200">
                                        <p class="text-sm text-secondary-800" x-text="chat.question"></p>
                                        <p class="text-[9px] text-secondary-400 mt-1 text-right" x-text="chat.formatted_time"></p>
                                    </div>
                                </div>
                                {{-- AI / Admin Answer --}}
                                <div class="flex justify-end">
                                    <div class="max-w-[75%] p-3 rounded-2xl rounded-tr-none shadow-md"
                                         :class="chat.question === '[ADMIN MANUAL REPLY]' ? 'bg-indigo-600 text-white' : 'bg-primary-700 text-white'">
                                        <div class="flex items-center gap-1.5 mb-1 opacity-70">
                                            <template x-if="chat.question === '[ADMIN MANUAL REPLY]'">
                                                <span class="text-[9px] font-bold uppercase tracking-wider">Admin</span>
                                            </template>
                                            <template x-if="chat.question !== '[ADMIN MANUAL REPLY]'">
                                                <span class="text-[9px] font-bold uppercase tracking-wider">AI Agent</span>
                                            </template>
                                        </div>
                                        <p class="text-sm" x-text="chat.answer"></p>
                                        <p class="text-[9px] mt-1 text-right opacity-60" x-text="chat.formatted_time"></p>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>

                    {{-- Input Box --}}
                    <div class="p-4 bg-white border-t border-secondary-200">
                        <div class="flex items-end gap-3 bg-secondary-100 rounded-2xl p-2 pr-3">
                            <textarea x-model="message" 
                                      @keydown.enter.prevent="if(!event.shiftKey) sendMessage()"
                                      placeholder="Ketik pesan balasan (Takeover)..." 
                                      class="flex-1 bg-transparent border-none focus:ring-0 text-sm resize-none py-2 max-h-32 scrollbar-hide"
                                      rows="1"></textarea>
                            <button @click="sendMessage()" 
                                    class="p-2.5 bg-primary-600 text-white rounded-xl hover:bg-primary-700 transition-all shadow-lg shadow-primary-500/20 active:scale-95">
                                <svg class="w-5 h-5 rotate-90" fill="currentColor" viewBox="0 0 20 20"><path d="M10.894 2.553a1 1 0 00-1.788 0l-7 14a1 1 0 001.169 1.409l5-1.429A1 1 0 009 15.571V11a1 1 0 112 0v4.571a1 1 0 00.725.962l5 1.428a1 1 0 001.17-1.408l-7-14z"/></svg>
                            </button>
                        </div>
                    </div>
                </div>
            </template>
            <template x-if="!activePhone">
                <div class="flex-1 flex flex-col items-center justify-center text-center p-12">
                    <div class="w-24 h-24 bg-secondary-100 rounded-full flex items-center justify-center mb-6">
                        <svg class="w-12 h-12 text-secondary-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                    </div>
                    <h3 class="text-xl font-bold text-secondary-900">Pilih Percakapan</h3>
                    <p class="text-secondary-500 mt-2 max-w-sm">Klik pada salah satu kontak di sebelah kiri untuk mulai mengelola percakapan dan melakukan takeover.</p>
                </div>
            </template>
        </div>

        {{-- 3. Details/Control (Right) --}}
        <div class="w-72 flex-shrink-0 border-l border-secondary-200 bg-secondary-50/10 p-6">
            <template x-if="activePhone">
                <div class="space-y-8">
                    <div class="text-center">
                        <div class="w-20 h-20 bg-primary-100 text-primary-700 rounded-3xl flex items-center justify-center font-bold text-2xl mx-auto mb-4 border-2 border-white shadow-xl">
                            <span x-text="activeName.substring(0, 1).toUpperCase()"></span>
                        </div>
                        <h4 class="font-bold text-secondary-900" x-text="activeName"></h4>
                        <p class="text-xs text-secondary-500 mt-1" x-text="activePhone"></p>
                    </div>

                    <div class="space-y-4">
                        <div class="bg-white p-4 rounded-2xl border border-secondary-200 shadow-sm">
                            <label class="text-[10px] font-bold text-secondary-400 uppercase tracking-widest block mb-3">Kontrol AI Agen</label>
                            <div class="flex items-center justify-between">
                                <span class="text-sm font-medium text-secondary-700">Auto Response</span>
                                <button @click="toggleAi()" 
                                        class="w-12 h-6 rounded-full transition-colors relative"
                                        :class="isAiEnabled ? 'bg-green-500' : 'bg-secondary-300'">
                                    <div class="absolute top-1 w-4 h-4 bg-white rounded-full transition-all"
                                         :class="isAiEnabled ? 'left-7' : 'left-1'"></div>
                                </button>
                            </div>
                            <p class="text-[10px] mt-3 leading-relaxed" :class="isAiEnabled ? 'text-green-600' : 'text-rose-500'">
                                <span x-text="isAiEnabled ? 'AI sedang aktif melayani pelanggan.' : 'Human Takeover aktif. AI berhenti merespon.'"></span>
                            </p>
                        </div>

                        <div class="bg-white p-4 rounded-2xl border border-secondary-200 shadow-sm">
                            <label class="text-[10px] font-bold text-secondary-400 uppercase tracking-widest block mb-2">Platform</label>
                            <div class="flex items-center gap-2">
                                <span class="px-3 py-1 bg-green-50 text-green-700 rounded-lg text-xs font-bold border border-green-100">WhatsApp</span>
                            </div>
                        </div>
                    </div>
                </div>
            </template>
            <template x-if="!activePhone">
                <div class="text-center py-20 text-secondary-300 italic text-sm">
                    Detail pelanggan akan muncul di sini.
                </div>
            </template>
        </div>
    </div>
</x-cms-layout>
