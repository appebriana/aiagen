const path = require('path');
require('dotenv').config({ path: path.join(__dirname, '../.env') });
const { Client, LocalAuth } = require('whatsapp-web.js');
const qrcode = require('qrcode-terminal');
const axios = require('axios');
const express = require('express');

const app = express();
app.use(express.json());

const DEPARTMENT_ID = process.env.DEPARTMENT_ID || process.argv[2] || 'default';
const PORT = process.env.PORT || process.argv[3] || 3001;
const DEVICE_ID = process.env.DEVICE_ID || null;

let connectionStatus = 'initializing';
let currentQR = '';
let DEVICE_NAME = `Dept ${DEPARTMENT_ID}`;
let initRequested = false;

// Ambil nama perangkat asli dari DB agar log rapi
async function fetchDeviceName() {
    try {
        const settingsRes = await axios.get(`http://127.0.0.1:8000/settings/${DEPARTMENT_ID}`);
        if (settingsRes.data && settingsRes.data.name) {
            DEVICE_NAME = settingsRes.data.name;
        }
    } catch (e) { }
}
fetchDeviceName();

const client = new Client({
    authStrategy: new LocalAuth({
        clientId: DEVICE_ID ? `device-${DEVICE_ID}` : `dept-${DEPARTMENT_ID}`,
        dataPath: './.wwebjs_auth'
    }),
    puppeteer: {
        args: ['--no-sandbox', '--disable-setuid-sandbox', '--disable-dev-shm-usage', '--no-zygote'],
        headless: true
    }
});

async function updateDbStatus(status, phoneNumber = null) {
    try {
        const response = await axios.post(`http://127.0.0.1:8000/update-device-status/${DEVICE_ID}`, { 
            status, 
            phone_number: phoneNumber 
        });

        // Jika API menolak karena nomor sudah digunakan di device lain
        if (response.data && response.data.status === 'error' && response.data.code === 'duplicate_number') {
            console.error(`\n[${DEVICE_NAME}] !!! ERROR: ${response.data.message} !!!`);
            console.log(`[${DEVICE_NAME}] Memutuskan sesi agar tidak terjadi tabrakan AI...`);
            
            try {
                await client.logout();
            } catch (logoutError) {
                console.error("Gagal logout:", logoutError.message);
            }
            
            process.exit(1); // Hentikan proses agar PM2 tidak restart terus menerus (jika tidak dikonfigurasi restart)
        }
    } catch (e) { 
        console.error(`[${DEVICE_NAME}] Gagal update status ke API:`, e.message);
    }
}

client.on('qr', qr => {
    currentQR = qr;
    connectionStatus = 'qr_ready';
    updateDbStatus('disconnected');

    if (initRequested) {
        console.log(`\n[${DEVICE_NAME}] === SCAN QR DI BAWAH INI ===`);
        qrcode.generate(qr, { small: true });
        console.log(`[${DEVICE_NAME}] QR akan muncul di Dashboard juga.\n`);
    } else {
        console.log(`[${DEVICE_NAME}] QR Code dihasilkan (Menunggu di Dashboard...)`);
    }
});

client.on('authenticated', () => {
    console.log(`[${DEVICE_NAME}] Terautentikasi!`);
    initRequested = false;
});

client.on('ready', () => {
    connectionStatus = 'ready';
    currentQR = '';
    initRequested = false;
    
    // Ambil nomor WA yang terhubung
    const phoneNumber = client.info.wid.user;
    updateDbStatus('connected', phoneNumber);
    
    console.log(`[${DEVICE_NAME}] WhatsApp Client SIAP! (Nomor: ${phoneNumber})`);
});

client.on('disconnected', (reason) => {
    connectionStatus = 'disconnected';
    currentQR = '';
    updateDbStatus('disconnected');
    console.log(`[${DEVICE_NAME}] WhatsApp Terputus:`, reason);
});

client.on('message', async msg => {
    let currentDeptSettings = null;
    let aiNameTrigger = '/ai';

    const isGroup = msg.from.includes('@g.us');
    if (msg.from === 'status@broadcast') return;

    console.log(`[${DEVICE_NAME}] Pesan Masuk dari ${msg.from}: ${msg.body}`);

    // --- FITUR HUMAN-LIKE DIHAPUS DARI SINI ---
    // (Akan dipanggil via API oleh Python agar tidak muncul saat MUTE)

    try {
        const settingsRes = await axios.get(`http://127.0.0.1:8000/settings/${DEPARTMENT_ID}`);
        currentDeptSettings = settingsRes.data;
        if (currentDeptSettings && currentDeptSettings.ai_name) {
            aiNameTrigger = `/${currentDeptSettings.ai_name.toLowerCase().replace(/\s+/g, '')}`;
        }
    } catch (e) {
        console.error("Gagal mengambil settings:", e.message);
    }

    let isTriggered = true;
    if (isGroup) {
        if (!currentDeptSettings || !currentDeptSettings.reply_to_groups) {
            isTriggered = false;
        } else if (!msg.body.toLowerCase().startsWith(aiNameTrigger)) {
            isTriggered = false;
        } else {
            msg.body = msg.body.substring(aiNameTrigger.length).trim();
            if (msg.body === '') {
                msg.body = 'Halo';
            }
        }
    }

    try {
        let isHeld = false;
        try {
            const chat = await msg.getChat();
            const labels = await chat.getLabels();
            isHeld = labels.some(l => l.name.toUpperCase().includes('HOLD'));
        } catch (labelError) {
            console.error(`[${DEVICE_NAME}] Error checking labels:`, labelError.message);
        }

        const webhookUrl = process.env.AI_AGENT_WEBHOOK_URL || 'http://127.0.0.1:8000/webhook';
        await axios.post(webhookUrl, {
            sender: msg.from,
            message: msg.body,
            department_id: DEPARTMENT_ID,
            device_id: DEVICE_ID,
            gateway_port: PORT,
            pushname: msg._data?.notifyName || null,
            is_held_by_label: isHeld,
            is_triggered: isTriggered,
            author: msg.author || msg.from,
            message_id: msg.id._serialized
        });
    } catch (error) {
        console.error(`[${DEVICE_NAME}] Gagal meneruskan ke Python:`, error.message);
    }
});

app.get('/status', (req, res) => {
    res.json({
        status: connectionStatus,
        qr: currentQR,
        device_name: DEVICE_NAME
    });
});

app.post('/init', async (req, res) => {
    if (connectionStatus === 'ready') {
        return res.json({ status: 'already_connected' });
    }

    initRequested = true; // Tandai bahwa user meminta QR muncul di terminal
    console.log(`[${DEVICE_NAME}] Memulai inisialisasi QR atas permintaan user...`);

    client.initialize().catch(err => {
        console.error(`[${DEVICE_NAME}] Gagal inisialisasi:`, err.message);
        initRequested = false;
    });

    res.json({ status: 'initializing' });
});

app.post('/disconnect', async (req, res) => {
    try {
        console.log(`[${DEVICE_NAME}] Memutuskan koneksi...`);
        await client.logout();
        connectionStatus = 'disconnected';
        currentQR = '';
        updateDbStatus('disconnected');
        res.json({ status: 'success' });
    } catch (error) {
        connectionStatus = 'disconnected';
        updateDbStatus('disconnected');
        res.status(500).json({ status: 'error', message: error.message });
    }
});

app.post('/typing', async (req, res) => {
    const { target } = req.body;
    try {
        const chat = await client.getChatById(target);
        await chat.sendSeen();
        await chat.sendStateTyping();
        res.json({ status: 'success' });
    } catch (error) {
        res.status(500).json({ status: 'error', message: error.message });
    }
});

app.post('/stop-typing', async (req, res) => {
    const { target } = req.body;
    try {
        const chat = await client.getChatById(target);
        await chat.clearState();
        res.json({ status: 'success' });
    } catch (error) {
        res.status(500).json({ status: 'error', message: error.message });
    }
});

app.post('/send', async (req, res) => {
    const { target, message, reply_to_msg_id } = req.body;
    try {
        const chat = await client.getChatById(target);
        
        let options = {};
        if (reply_to_msg_id) {
            options.quotedMessageId = reply_to_msg_id;
        }
        
        await chat.sendMessage(message, options);
        await chat.clearState();
        res.json({ status: 'success' });
    } catch (error) {
        res.status(500).json({ status: 'error', message: error.message });
    }
});

const server = app.listen(PORT, '127.0.0.1', () => {
    console.log(`[${DEVICE_NAME}] API Gateway aktif di Port ${PORT} (127.0.0.1)`);

    // Baru inisialisasi WhatsApp SETELAH server HTTP berhasil jalan
    console.log(`[${DEVICE_NAME}] Mencoba inisialisasi sesi...`);
    client.initialize().catch(err => {
        console.error(`[${DEVICE_NAME}] Gagal inisialisasi awal:`, err.message);
    });
});

server.on('error', (err) => {
    if (err.code === 'EADDRINUSE') {
        console.error(`[${DEVICE_NAME}] Port ${PORT} sudah digunakan oleh proses lain. Keluar...`);
        process.exit(1);
    } else {
        throw err;
    }
});

/**
 * Graceful Shutdown:
 * Pastikan Chromium browser dan HTTP server dimatikan dengan bersih
 * saat menerima sinyal dari PM2 atau manager.js
 */
async function shutdownGateway(signal) {
    console.log(`[${DEVICE_NAME}] Menerima sinyal ${signal}. Mematikan gateway...`);
    
    try {
        // Tutup WhatsApp client (dan Chromium browser)
        await client.destroy();
        console.log(`[${DEVICE_NAME}] WhatsApp client dimatikan.`);
    } catch (e) {
        console.error(`[${DEVICE_NAME}] Error saat menutup client:`, e.message);
    }
    
    // Tutup HTTP server
    server.close(() => {
        console.log(`[${DEVICE_NAME}] HTTP server ditutup. Keluar.`);
        process.exit(0);
    });
    
    // Force exit setelah 5 detik jika masih menggantung
    setTimeout(() => {
        console.error(`[${DEVICE_NAME}] Force exit setelah timeout.`);
        process.exit(1);
    }, 5000);
}

process.on('SIGINT', () => shutdownGateway('SIGINT'));
process.on('SIGTERM', () => shutdownGateway('SIGTERM'));
