<x-cms-layout :departments="$departments" :activeDepartment="$activeDepartment">
    <x-slot name="header">
        CMS Omnichannel - {{ $activeDepartment->name ?? 'Pilih Departemen' }}
    </x-slot>

    @if($activeDepartment)
    <div class="flex flex-1 w-full overflow-hidden" 
         x-data="{ 
            activePhone: null, 
            activeName: '', 
            chats: [], 
            loading: false,
            message: '',
            isAiEnabled: true,
            activePlatform: '{{ $whatsappDevices->first() ? 'wa-'.$whatsappDevices->first()->id : 'wa' }}',
            selectedDeviceId: {{ $whatsappDevices->first() ? $whatsappDevices->first()->id : 'null' }},
            conversations: @json($conversations),
            
            async selectConversation(phone, name, aiStatus) {
                this.activePhone = phone;
                this.activeName = name;
                this.isAiEnabled = aiStatus;
                this.fetchChats();
            },

            async fetchConversations() {
                try {
                    const prefix = '{{ auth()->user()->isAdmin() ? '/admin' : '/pengguna' }}';
                    const response = await fetch(`${prefix}/cms/conversations/{{ $activeDepartment->id }}`);
                    const result = await response.json();
                    if (result.status === 'success') {
                        this.conversations = result.data;
                    }
                } catch (error) {
                    console.error('Error fetching conversations:', error);
                }
            },

            async fetchChats(background = false) {
                if (!this.activePhone) return;
                if (!background) this.loading = true;
                try {
                    const prefix = '{{ auth()->user()->isAdmin() ? '/admin' : '/pengguna' }}';
                    const response = await fetch(`${prefix}/cms/chats/{{ $activeDepartment->id }}/${this.activePhone}`);
                    const result = await response.json();
                    if (result.status === 'success') {
                        // Hanya update jika data berbeda untuk menghindari flicker
                        if (JSON.stringify(this.chats) !== JSON.stringify(result.data)) {
                            this.chats = result.data;
                            this.scrollToBottom();
                        }
                    }
                } catch (error) {
                    console.error('Error fetching chats:', error);
                } finally {
                    if (!background) this.loading = false;
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
                            message: this.message,
                            device_id: this.selectedDeviceId
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
            },

            cleanMessage(text) {
                if (!text) return '';
                // Menghapus semua tag teknis [[...]] dari tampilan chat
                return text.replace(/\[\[.*?\]\]/g, '').trim();
            }
         }"
         x-init="setInterval(() => { 
                fetchConversations();
                if(activePhone && !loading) fetchChats(true); 
             }, 5000)">
        
        {{-- 0. Platform Selector (Far Left) --}}
        <div class="w-20 flex-shrink-0 bg-secondary-100 border-r border-secondary-200 flex flex-col items-center py-6 gap-6 overflow-y-auto scrollbar-hide">
            {{-- Connected WhatsApp Devices --}}
            @forelse($whatsappDevices as $device)
                <div class="flex flex-col items-center gap-1">
                    <button @click="activePlatform = 'wa-{{ $device->id }}'; selectedDeviceId = {{ $device->id }}" 
                            class="w-12 h-12 rounded-full flex items-center justify-center transition-all shadow-md active:scale-90 relative group"
                            :class="activePlatform === 'wa-{{ $device->id }}' ? 'bg-green-600 text-white ring-4 ring-green-100' : 'bg-white text-secondary-400 hover:bg-secondary-50'">
                        <svg class="w-7 h-7" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                        
                        {{-- Tooltip name --}}
                        <div class="absolute left-14 bg-secondary-900 text-white text-[10px] px-2 py-1 rounded opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none whitespace-nowrap z-50">
                            {{ $device->name }} (Connected)
                        </div>
                    </button>
                    <span class="text-[9px] font-bold text-secondary-500">WA</span>
                </div>
            @empty
                <div class="flex flex-col items-center gap-1 opacity-40">
                    <button class="w-12 h-12 rounded-full bg-white flex items-center justify-center text-secondary-300">
                        <svg class="w-7 h-7" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                    </button>
                    <span class="text-[9px] font-bold">No Device</span>
                </div>
            @endforelse

            <div class="h-px w-8 bg-secondary-200 my-2"></div>

            {{-- Connected Telegram Devices --}}
            @foreach($telegramDevices as $device)
                <div class="flex flex-col items-center gap-1">
                    <button @click="activePlatform = 'tele-{{ $device->id }}'; selectedDeviceId = {{ $device->id }}" 
                            class="w-12 h-12 rounded-full flex items-center justify-center transition-all shadow-md active:scale-90 relative group"
                            :class="activePlatform === 'tele-{{ $device->id }}' ? 'bg-sky-500 text-white ring-4 ring-sky-100' : 'bg-white text-secondary-400 hover:bg-secondary-50'">
                        <svg class="w-6 h-6" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm4.64 6.8c-.15 1.58-.8 5.42-1.13 7.19-.14.75-.42 1-.68 1.03-.58.05-1.02-.38-1.58-.75-.88-.58-1.38-.94-2.23-1.5-.99-.65-.35-1.01.22-1.59.15-.15 2.71-2.48 2.76-2.69a.2.2 0 00-.07-.2c-.08-.06-.19-.04-.27-.02-.12.03-1.99 1.27-5.62 3.72-.53.36-1.01.54-1.44.53-.47-.01-1.38-.27-2.06-.49-.83-.27-1.49-.42-1.43-.88.03-.24.37-.49 1.02-.75 3.99-1.73 6.65-2.88 7.99-3.44 3.81-1.58 4.6-1.86 5.12-1.87.11 0 .37.03.54.17.14.12.18.28.2.46-.01.06.01.24 0 .38z"/></svg>
                        <div class="absolute left-14 bg-secondary-900 text-white text-[10px] px-2 py-1 rounded opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none whitespace-nowrap z-50">
                            {{ $device->name }} (Telegram)
                        </div>
                    </button>
                    <span class="text-[9px] font-bold text-secondary-500">Tele</span>
                </div>
            @endforeach

            {{-- Telegram Placeholder (If none connected) --}}
            @if(count($telegramDevices) === 0)
                <div class="flex flex-col items-center gap-1 opacity-50 cursor-not-allowed">
                    <button class="w-12 h-12 rounded-full bg-white flex items-center justify-center text-secondary-300 transition-all shadow-sm relative group">
                        <svg class="w-6 h-6" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm4.64 6.8c-.15 1.58-.8 5.42-1.13 7.19-.14.75-.42 1-.68 1.03-.58.05-1.02-.38-1.58-.75-.88-.58-1.38-.94-2.23-1.5-.99-.65-.35-1.01.22-1.59.15-.15 2.71-2.48 2.76-2.69a.2.2 0 00-.07-.2c-.08-.06-.19-.04-.27-.02-.12.03-1.99 1.27-5.62 3.72-.53.36-1.01.54-1.44.53-.47-.01-1.38-.27-2.06-.49-.83-.27-1.49-.42-1.43-.88.03-.24.37-.49 1.02-.75 3.99-1.73 6.65-2.88 7.99-3.44 3.81-1.58 4.6-1.86 5.12-1.87.11 0 .37.03.54.17.14.12.18.28.2.46-.01.06.01.24 0 .38z"/></svg>
                        <div class="absolute left-14 bg-secondary-900 text-white text-[10px] px-2 py-1 rounded opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none whitespace-nowrap z-50">
                            Telegram (Coming Soon)
                        </div>
                    </button>
                    <span class="text-[9px] font-bold text-secondary-400">Tele</span>
                </div>
            @endif

            {{-- Connected Instagram Devices --}}
            @foreach($instagramDevices as $device)
                <div class="flex flex-col items-center gap-1">
                    <button @click="activePlatform = 'ig-{{ $device->id }}'; selectedDeviceId = {{ $device->id }}" 
                            class="w-12 h-12 rounded-full flex items-center justify-center transition-all shadow-md active:scale-90 relative group"
                            :class="activePlatform === 'ig-{{ $device->id }}' ? 'bg-pink-600 text-white ring-4 ring-pink-100' : 'bg-white text-secondary-400 hover:bg-secondary-50'">
                        <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"/><circle cx="12" cy="12" r="4.5"/><circle cx="17.5" cy="6.5" r="1" fill="currentColor" stroke="none"/></svg>
                        <div class="absolute left-14 bg-secondary-900 text-white text-[10px] px-2 py-1 rounded opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none whitespace-nowrap z-50">
                            {{ $device->name }} (Instagram)
                        </div>
                    </button>
                    <span class="text-[9px] font-bold text-secondary-500">IG</span>
                </div>
            @endforeach

            {{-- Instagram Placeholder --}}
            @if(count($instagramDevices) === 0)
                <div class="flex flex-col items-center gap-1 opacity-50 cursor-not-allowed">
                    <button class="w-12 h-12 rounded-full bg-white flex items-center justify-center text-secondary-300 transition-all shadow-sm relative group">
                        <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"/><circle cx="12" cy="12" r="4.5"/><circle cx="17.5" cy="6.5" r="1" fill="currentColor" stroke="none"/></svg>
                        <div class="absolute left-14 bg-secondary-900 text-white text-[10px] px-2 py-1 rounded opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none whitespace-nowrap z-50">
                            Instagram (Coming Soon)
                        </div>
                    </button>
                    <span class="text-[9px] font-bold text-secondary-400">IG</span>
                </div>
            @endif

            {{-- Connected Facebook Devices --}}
            @foreach($facebookDevices as $device)
                <div class="flex flex-col items-center gap-1">
                    <button @click="activePlatform = 'fb-{{ $device->id }}'; selectedDeviceId = {{ $device->id }}" 
                            class="w-12 h-12 rounded-full flex items-center justify-center transition-all shadow-md active:scale-90 relative group"
                            :class="activePlatform === 'fb-{{ $device->id }}' ? 'bg-blue-600 text-white ring-4 ring-blue-100' : 'bg-white text-secondary-400 hover:bg-secondary-50'">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M22 12c0-5.52-4.48-10-10-10S2 6.48 2 12c0 4.84 3.44 8.87 8 9.8V15H8v-3h2V9.5C10 7.57 11.57 6 13.5 6H16v3h-2c-.55 0-1 .45-1 1v2h3v3h-3v6.95c5.05-.5 9-4.76 9-9.95z"/></svg>
                        <div class="absolute left-14 bg-secondary-900 text-white text-[10px] px-2 py-1 rounded opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none whitespace-nowrap z-50">
                            {{ $device->name }} (Facebook)
                        </div>
                    </button>
                    <span class="text-[9px] font-bold text-secondary-500">FB</span>
                </div>
            @endforeach

            {{-- Facebook Placeholder --}}
            @if(count($facebookDevices) === 0)
                <div class="flex flex-col items-center gap-1 opacity-50 cursor-not-allowed">
                    <button class="w-12 h-12 rounded-full bg-white flex items-center justify-center text-secondary-300 transition-all shadow-sm relative group">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M22 12c0-5.52-4.48-10-10-10S2 6.48 2 12c0 4.84 3.44 8.87 8 9.8V15H8v-3h2V9.5C10 7.57 11.57 6 13.5 6H16v3h-2c-.55 0-1 .45-1 1v2h3v3h-3v6.95c5.05-.5 9-4.76 9-9.95z"/></svg>
                        <div class="absolute left-14 bg-secondary-900 text-white text-[10px] px-2 py-1 rounded opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none whitespace-nowrap z-50">
                            Facebook (Coming Soon)
                        </div>
                    </button>
                    <span class="text-[9px] font-bold text-secondary-400">FB</span>
                </div>
            @endif
        </div>

        {{-- 1. Conversation List (Left) --}}
        <div class="w-80 flex-shrink-0 border-r border-secondary-200 flex flex-col bg-secondary-50/30">
            <div class="p-4 border-b border-secondary-200 bg-white">
                <div class="relative">
                    <input type="text" placeholder="Cari percakapan..." class="w-full pl-9 pr-4 py-2 bg-secondary-100 border-none rounded-xl text-sm focus:ring-2 focus:ring-primary-500">
                    <svg class="w-4 h-4 text-secondary-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
            </div>
            <div class="flex-1 overflow-y-auto divide-y divide-secondary-100">
                <template x-for="conv in conversations" :key="conv.customer_phone">
                    <button @click="selectConversation(conv.customer_phone, conv.customer_name, conv.is_ai_enabled)"
                            class="w-full p-4 flex items-start gap-3 hover:bg-white transition-all text-left group"
                            :class="activePhone === conv.customer_phone ? 'bg-white border-l-4 border-primary-600 shadow-sm' : ''">
                        <div class="relative flex-shrink-0">
                            <div class="w-12 h-12 bg-primary-100 text-primary-700 rounded-2xl flex items-center justify-center font-bold" 
                                 x-text="conv.customer_name.substring(0,1).toUpperCase()">
                            </div>
                            <div class="absolute -bottom-1 -right-1 w-4 h-4 rounded-full border-2 border-white"
                                 :class="conv.is_ai_enabled ? 'bg-green-500' : 'bg-rose-500'"></div>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between mb-0.5">
                                <h4 class="text-sm font-bold text-secondary-900 truncate" x-text="conv.customer_name"></h4>
                                <span class="text-[10px] text-secondary-400 font-medium whitespace-nowrap" x-text="conv.last_chat_time"></span>
                            </div>
                            <p class="text-xs text-secondary-500 truncate" x-text="cleanMessage(conv.last_message)"></p>
                            <div class="flex items-center gap-2 mt-1.5">
                                <span class="px-1.5 py-0.5 rounded text-[8px] font-extrabold uppercase"
                                      :class="conv.is_ai_enabled ? 'bg-green-50 text-green-700' : 'bg-rose-50 text-rose-700 animate-pulse'"
                                      x-text="conv.is_ai_enabled ? 'AI Active' : 'Human Takeover'">
                                </span>
                            </div>
                        </div>
                    </button>
                </template>
                <div x-show="conversations.length === 0" class="p-8 text-center text-secondary-400">
                    <p class="text-sm">Belum ada percakapan di departemen ini.</p>
                </div>
            </div>
        </div>

        {{-- 2. Chat Area (Middle) --}}
        <div class="flex-1 flex flex-col bg-white min-w-0 overflow-hidden">
            <template x-if="activePhone">
                <div class="flex flex-col flex-1 overflow-hidden">
                    {{-- Chat Content --}}
                    <div class="flex-1 overflow-y-auto p-6 space-y-4 bg-secondary-50/50" id="chat-scroll">
                        <template x-for="chat in chats" :key="chat.id">
                            <div class="space-y-3">
                                {{-- User Message --}}
                                <template x-if="chat.question && chat.question !== '[ADMIN MANUAL REPLY]'">
                                    <div class="flex justify-start">
                                        <div class="max-w-[75%] bg-white p-3 rounded-2xl rounded-tl-none shadow-sm border border-secondary-200">
                                            <p class="text-sm text-secondary-800" x-text="cleanMessage(chat.question)"></p>
                                            <p class="text-[9px] text-secondary-400 mt-1 text-right" x-text="chat.formatted_time"></p>
                                        </div>
                                    </div>
                                </template>
                                {{-- AI / Admin Answer --}}
                                <template x-if="chat.answer && chat.answer.trim() !== ''">
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
                                            <p class="text-sm" x-text="cleanMessage(chat.answer)"></p>
                                            <p class="text-[9px] mt-1 text-right opacity-60" x-text="chat.formatted_time"></p>
                                        </div>
                                    </div>
                                </template>
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
    @else
        <div class="flex flex-col items-center justify-center h-full bg-secondary-50/30 text-center p-8">
            <div class="w-24 h-24 bg-white rounded-3xl shadow-sm border border-secondary-100 flex items-center justify-center mb-6">
                <svg class="w-12 h-12 text-secondary-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
            </div>
            <h3 class="text-xl font-bold text-secondary-900 mb-2">Pilih Departemen</h3>
            <p class="text-secondary-500 max-w-sm">Silakan pilih departemen di sebelah kiri untuk mulai mengelola pesan Omnichannel.</p>
        </div>
    @endif
</x-cms-layout>
