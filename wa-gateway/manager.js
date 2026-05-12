const path = require('path');
require('dotenv').config({ path: path.join(__dirname, '../.env') });
const mysql = require('mysql2/promise');
const net = require('net');
const { spawn, execSync } = require('child_process');

/**
 * WA GATEWAY MANAGER
 * Skrip ini otomatis mendeteksi departemen dari database
 * dan menjalankan instans gateway.js untuk masing-masing departemen.
 * 
 * Fitur:
 * - Cek port sebelum spawn (hindari EADDRINUSE)
 * - Auto-detect orphan gateway dari restart PM2 sebelumnya
 * - Bersihkan proses jika device dihapus dari DB
 * - Graceful shutdown: kill semua child process saat manager dihentikan
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

/**
 * Kill proses yang sedang mendengarkan di port tertentu.
 * Hanya bekerja di Linux (production).
 */
function killProcessOnPort(port) {
    try {
        if (process.platform === 'linux') {
            execSync(`fuser -k ${port}/tcp 2>/dev/null || true`);
            console.log(`[MANAGER] Membersihkan proses lama di port ${port}`);
        }
    } catch (e) {
        // Abaikan error — mungkin tidak ada proses di port ini
    }
}

/**
 * Graceful shutdown: kill semua child processes saat manager dihentikan.
 */
function gracefulShutdown(signal) {
    console.log(`\n[MANAGER] Menerima sinyal ${signal}. Mematikan semua gateway...`);
    
    for (const [id, child] of runningProcesses.entries()) {
        if (child && child.kill) {
            console.log(`[MANAGER] Mematikan gateway Device ID ${id}...`);
            child.kill('SIGTERM');
        }
    }
    
    // Beri waktu child process untuk cleanup, lalu keluar
    setTimeout(() => {
        console.log('[MANAGER] Semua gateway dihentikan. Keluar.');
        process.exit(0);
    }, 3000);
}

// Tangkap sinyal shutdown dari PM2 dan terminal
process.on('SIGINT', () => gracefulShutdown('SIGINT'));
process.on('SIGTERM', () => gracefulShutdown('SIGTERM'));

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
                const port = 3000 + parseInt(device.department_id);
                const portBusy = await isPortInUse(port);
                
                if (portBusy) {
                    // Port sudah aktif — coba kill dulu lalu spawn ulang
                    console.log(`[MANAGER] Port ${port} sudah aktif (orphan) untuk Device ID ${device.id}. Membersihkan...`);
                    killProcessOnPort(port);
                    
                    // Tunggu sebentar agar port benar-benar lepas
                    await new Promise(r => setTimeout(r, 1500));
                    
                    const stillBusy = await isPortInUse(port);
                    if (stillBusy) {
                        console.log(`[MANAGER] Port ${port} masih aktif setelah cleanup. Melewati spawn.`);
                        runningProcesses.set(device.id, { orphan: true, port });
                    } else {
                        spawnGateway(device);
                    }
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
                if (child.kill) child.kill('SIGTERM');
                runningProcesses.delete(id);
            }
        }

    } catch (error) {
        console.error("[MANAGER] Error saat polling DB:", error.message);
    }
}

function spawnGateway(device) {
    const port = 3000 + parseInt(device.department_id);
    
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
