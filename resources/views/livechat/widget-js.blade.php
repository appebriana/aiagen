(function() {
    // Inject CSS dynamically
    const style = document.createElement('style');
    style.innerHTML = `
        :root {
            --lc-primary: {{ $primaryColor }};
            --lc-primary-rgb: ${hexToRgb('{{ $primaryColor }}')};
        }
        #aiagen-livechat-container {
            position: fixed;
            bottom: 20px;
            right: 20px;
            z-index: 999999;
            font-family: 'Figtree', 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            display: flex;
            flex-direction: column;
            align-items: flex-end;
        }
        #aiagen-livechat-bubble {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background-color: var(--lc-primary);
            box-shadow: 0 8px 24px rgba(var(--lc-primary-rgb), 0.3);
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            color: #ffffff;
        }
        #aiagen-livechat-bubble:hover {
            transform: scale(1.1) translateY(-3px);
            box-shadow: 0 12px 30px rgba(var(--lc-primary-rgb), 0.4);
        }
        #aiagen-livechat-bubble svg {
            width: 28px;
            height: 28px;
            transition: transform 0.3s ease;
        }
        #aiagen-livechat-bubble.active svg {
            transform: rotate(90deg) scale(0.8);
        }
        #aiagen-livechat-window {
            width: 380px;
            height: 520px;
            background: #ffffff;
            border-radius: 24px;
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.12);
            margin-bottom: 20px;
            display: none;
            flex-direction: column;
            overflow: hidden;
            animation: lc-fade-in 0.35s cubic-bezier(0.16, 1, 0.3, 1);
            border: 1px solid rgba(0, 0, 0, 0.05);
        }
        @keyframes lc-fade-in {
            from {
                opacity: 0;
                transform: translateY(20px) scale(0.95);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }
        #aiagen-livechat-header {
            background: var(--lc-primary);
            padding: 24px;
            color: #ffffff;
            display: flex;
            align-items: center;
            gap: 12px;
            position: relative;
        }
        #aiagen-livechat-header::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 10px;
            background: linear-gradient(to bottom, rgba(0,0,0,0.05), transparent);
        }
        .lc-avatar {
            width: 44px;
            height: 44px;
            background: rgba(255,255,255,0.2);
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 18px;
            box-shadow: inset 0 2px 4px rgba(255,255,255,0.2);
        }
        .lc-header-info h4 {
            margin: 0;
            font-size: 16px;
            font-weight: 700;
            letter-spacing: -0.3px;
        }
        .lc-status {
            font-size: 11px;
            opacity: 0.85;
            display: flex;
            align-items: center;
            gap: 5px;
            margin-top: 2px;
        }
        .lc-status-dot {
            width: 6px;
            height: 6px;
            background: #10b981;
            border-radius: 50%;
            box-shadow: 0 0 8px #10b981;
        }
        #aiagen-livechat-body {
            flex: 1;
            padding: 20px;
            overflow-y: auto;
            background: #f8fafc;
            display: flex;
            flex-direction: column;
            gap: 12px;
        }
        .lc-message {
            max-width: 80%;
            padding: 12px 16px;
            border-radius: 18px;
            font-size: 13.5px;
            line-height: 1.5;
            word-wrap: break-word;
            box-shadow: 0 2px 4px rgba(0,0,0,0.02);
            animation: lc-msg-slide-in 0.3s cubic-bezier(0.16, 1, 0.3, 1) forwards;
            opacity: 0;
            transform: translateY(10px);
            white-space: pre-wrap;
        }
        @keyframes lc-msg-slide-in {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        .lc-message.visitor {
            align-self: flex-end;
            background: var(--lc-primary);
            color: #ffffff;
            border-bottom-right-radius: 4px;
        }
        .lc-message.agent {
            align-self: flex-start;
            background: #ffffff;
            color: #1e293b;
            border-bottom-left-radius: 4px;
            border: 1px solid rgba(0,0,0,0.05);
        }
        .lc-message-time {
            font-size: 9px;
            margin-top: 4px;
            opacity: 0.6;
            text-align: right;
        }
        #aiagen-livechat-footer {
            padding: 16px;
            background: #ffffff;
            border-top: 1px solid rgba(0,0,0,0.06);
            display: flex;
            gap: 10px;
            align-items: center;
        }
        #aiagen-livechat-input {
            flex: 1;
            border: none;
            background: #f1f5f9;
            padding: 12px 16px;
            border-radius: 14px;
            font-size: 13px;
            outline: none;
            transition: all 0.2s;
        }
        #aiagen-livechat-input:focus {
            background: #e2e8f0;
        }
        #aiagen-livechat-send {
            background: var(--lc-primary);
            border: none;
            width: 40px;
            height: 40px;
            border-radius: 12px;
            color: white;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s;
            box-shadow: 0 4px 12px rgba(var(--lc-primary-rgb), 0.2);
        }
        #aiagen-livechat-send:hover {
            transform: scale(1.05);
        }
        #aiagen-livechat-send svg {
            width: 18px;
            height: 18px;
        }

        @media (max-width: 480px) {
            #aiagen-livechat-window {
                width: calc(100vw - 40px);
                height: calc(100vh - 120px);
            }
        }
    `;
    document.head.appendChild(style);

    // Helpers
    function hexToRgb(hex) {
        let c;
        if(/^#([A-Fa-f0-9]{3}){1,2}$/.test(hex)){
            c= hex.substring(1).split('');
            if(c.length== 3){
                c= [c[0], c[0], c[1], c[1], c[2], c[2]];
            }
            c= '0x' + c.join('');
            return [(c>>16)&255, (c>>8)&255, c&255].join(',');
        }
        return '79,70,229';
    }

    // Generate/retrieve Session ID
    let sessionId = localStorage.getItem('aiagen_livechat_sess');
    if (!sessionId) {
        sessionId = 'lc_sess_' + Math.random().toString(36).substring(2, 15) + Math.random().toString(36).substring(2, 15);
        localStorage.setItem('aiagen_livechat_sess', sessionId);
    }

    // Build DOM Elements
    const container = document.createElement('div');
    container.id = 'aiagen-livechat-container';

    container.innerHTML = `
        <div id="aiagen-livechat-window">
            <div id="aiagen-livechat-header">
                <div class="lc-avatar">${'{{ strtoupper(substr($aiName, 0, 1)) }}'}</div>
                <div class="lc-header-info">
                    <h4>{{ $aiName }}</h4>
                    <div class="lc-status">
                        <span class="lc-status-dot"></span>
                        <span>Aktif | Didukung oleh AI</span>
                    </div>
                </div>
            </div>
            <div id="aiagen-livechat-body"></div>
            <div id="aiagen-livechat-footer">
                <input type="text" id="aiagen-livechat-input" placeholder="Tulis pesan..." autocomplete="off" />
                <button id="aiagen-livechat-send">
                    <svg fill="currentColor" viewBox="0 0 20 20"><path d="M10.894 2.553a1 1 0 00-1.788 0l-7 14a1 1 0 001.169 1.409l5-1.429A1 1 0 009 15.571V11a1 1 0 112 0v4.571a1 1 0 00.725.962l5 1.428a1 1 0 001.17-1.408l-7-14z"></path></svg>
                </button>
            </div>
        </div>
        <div id="aiagen-livechat-bubble">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
            </svg>
        </div>
    `;

    document.body.appendChild(container);

    // Elements References
    const bubble = document.getElementById('aiagen-livechat-bubble');
    const win = document.getElementById('aiagen-livechat-window');
    const body = document.getElementById('aiagen-livechat-body');
    const input = document.getElementById('aiagen-livechat-input');
    const sendBtn = document.getElementById('aiagen-livechat-send');

    // Toggle Chat Window
    bubble.addEventListener('click', () => {
        const isOpen = win.style.display === 'flex';
        win.style.display = isOpen ? 'none' : 'flex';
        bubble.classList.toggle('active', !isOpen);
        if (!isOpen) {
            input.focus();
            body.scrollTop = body.scrollHeight;
        }
    });

    // Welcome Message Helper
    let localChats = [];
    let inputInitialized = false;
    
    function appendMessage(sender, text, time, id = null) {
        if (id) {
            const existing = document.getElementById(`lc-msg-${sender}-${id}`);
            if (existing) return;
        }

        const msg = document.createElement('div');
        msg.className = `lc-message ${sender}`;
        if (id) {
            msg.id = `lc-msg-${sender}-${id}`;
        } else {
            msg.classList.add('lc-msg-temp');
        }
        
        let cleanText = text.replace(/\[\[.*?\]\]/g, "").trim();
        cleanText = cleanText.replace(/\*\*(.*?)\*\*/g, "<strong>$1</strong>");
        
        msg.innerHTML = `<div>${cleanText}</div><div class="lc-message-time">${time}</div>`;
        body.appendChild(msg);
        body.scrollTop = body.scrollHeight;
    }

    // Fetch message history
    async function loadHistory() {
        try {
            const res = await fetch('{{ $appUrl }}/api/livechat/chats?token={{ $token }}&session_id=' + sessionId);
            const result = await res.json();
            if (result.status === 'success') {
                if (result.data.length > 0) {
                    document.querySelectorAll('.lc-msg-temp').forEach(el => el.remove());
                }
                
                if (result.data.length === 0) {
                    if (body.children.length === 0) {
                        appendMessage('agent', '{{ $welcomeMessage }}', new Date().toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'}));
                    }
                    if (!inputInitialized) {
                        input.value = "Halo, nama saya ";
                        inputInitialized = true;
                    }
                } else {
                    inputInitialized = true;
                    result.data.forEach(chat => {
                        if (chat.question && chat.question !== '[ADMIN MANUAL REPLY]') {
                            appendMessage('visitor', chat.question, chat.formatted_time, chat.id);
                        }
                        if (chat.answer && chat.answer.trim() !== '') {
                            appendMessage('agent', chat.answer, chat.formatted_time, chat.id);
                        }
                    });
                }
                localChats = result.data;
            }
        } catch (e) {
            console.error('AIAGEN Livechat: Failed to load history', e);
        }
    }

    // Send Message
    async function sendMessage() {
        const text = input.value.trim();
        if (!text) return;
        
        input.value = '';
        
        const timeNow = new Date().toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
        appendMessage('visitor', text, timeNow);

        try {
            const res = await fetch('{{ $appUrl }}/api/livechat/send', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    token: '{{ $token }}',
                    session_id: sessionId,
                    message: text,
                    name: 'Visitor'
                })
            });
            const result = await res.json();
            
            if (result.status === 'success' && result.ai_reply) {
                appendMessage('agent', result.ai_reply, new Date().toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'}));
            }
        } catch (e) {
            console.error('AIAGEN Livechat: Send error', e);
        }
    }

    sendBtn.addEventListener('click', sendMessage);
    input.addEventListener('keydown', (e) => {
        if (e.key === 'Enter') sendMessage();
    });

    // Initialize Widget
    loadHistory();

    // Polling new messages
    setInterval(() => {
        // Poll only if window is visible or active
        loadHistory();
    }, 4000);
})();
