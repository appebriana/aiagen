const path = require('path');
require('dotenv').config({ path: path.join(__dirname, '../.env') });
const mysql = require('mysql2/promise');
const net = require('net');
const { spawn } = require('child_process');

/**
 * WA GATEWAY MANAGER
 * Skrip ini otomatis mendeteksi departemen dari database
 * dan menjalankan instans gateway.js untuk masing-masing departemen.
 * 
 * Fitur:
 * - Cek port sebelum spawn (hindari EADDRINUSE)
 * - Auto-detect orphan gateway dari restart PM2 sebelumnya
 * - Bersihkan proses jika device dihapus dari DB
 */

// Map untuk melacak proses yang sedang berjalan { deviceId: childProcess }
const runningProcesses = new Map();

/**
 * Cek apakah port sudah digunakan.
 * Jika sudah digunakan (oleh proses gateway lama/orphan), kita skip spawn.
 */
function isPortInUse(port) {
    return new Promise((resolve) => {
        const tester = net.createConnection({ port, host: '127.0.0.1' }, () => {
            tester.end();
            resolve(true); // Port aktif, ada yang listen
        });
        tester.on('error', () => {
            resolve(false); // Port bebas
        });
    });
}

async function startManager() {
    console.log("==========================================");
    console.log("   AIAGEN SMART GATEWAY MONITOR           ");
    console.log("   Monitoring database for new devices... ");
    console.log("==========================================");

    // Jalankan sekali langsung saat startup, lalu interval
    await pollDevices();
    setInterval(pollDevices, 10000); // Cek setiap 10 detik
}

async function pollDevices() {
    try {
        const connection = await mysql.createConnection({
            host: process.env.DB_HOST || '127.0.0.1',
            user: process.env.DB_USERNAME || 'root',
            password: process.env.DB_PASSWORD || '',
            database: process.env.DB_DATABASE || 'aiagen'
        });

        // Ambil semua perangkat WhatsApp
        const [devices] = await connection.execute('SELECT * FROM whatsapp_devices');
        await connection.end();

        // 1. Cek perangkat baru atau yang belum jalan
        for (const device of devices) {
            if (!runningProcesses.has(device.id)) {
                const port = 3000 + (parseInt(device.id) - 1);
                const portBusy = await isPortInUse(port);
                
                if (portBusy) {
                    // Port sudah aktif (orphan dari restart sebelumnya), tandai sebagai "running" 
                    // agar tidak terus mencoba spawn
                    console.log(`[MANAGER] Port ${port} sudah aktif (gateway orphan untuk Device ID ${device.id}). Melewati spawn.`);
                    runningProcesses.set(device.id, { orphan: true, port });
                } else {
                    spawnGateway(device);
                }
            }
        }

        // 2. Bersihkan proses jika perangkat dihapus dari DB
        for (const [id, child] of runningProcesses.entries()) {
            const stillExists = devices.some(d => d.id === id);
            if (!stillExists) {
                console.log(`[MANAGER] Perangkat ID ${id} dihapus dari DB. Mematikan gateway...`);
                if (child.kill) child.kill(); // Hanya kill jika bukan orphan
                runningProcesses.delete(id);
            }
        }

    } catch (error) {
        console.error("[MANAGER] Error saat polling DB:", error.message);
    }
}

function spawnGateway(device) {
    const port = 3000 + (parseInt(device.id) - 1);
    
    console.log(`[MANAGER] Menjalankan Gateway Baru: ID ${device.id} | Name: ${device.name} | Port: ${port}`);

    const child = spawn(process.execPath, [path.join(__dirname, 'gateway.js')], {
        env: { 
            ...process.env, 
            DEVICE_ID: device.id,
            DEPARTMENT_ID: device.department_id,
            PORT: port
        },
        stdio: 'inherit'
    });

    // Simpan ke daftar proses
    runningProcesses.set(device.id, child);

    child.on('close', (code) => {
        console.log(`[MANAGER] Gateway ID ${device.id} berhenti (Code: ${code})`);
        runningProcesses.delete(device.id);
    });

    child.on('error', (err) => {
        console.error(`[MANAGER] Fatal Error di Gateway ID ${device.id}:`, err.message);
        runningProcesses.delete(device.id);
    });
}

startManager();
