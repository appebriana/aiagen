const path = require('path');
require('dotenv').config({ path: path.join(__dirname, '../.env') });
const mysql = require('mysql2/promise');
const { spawn } = require('child_process');

/**
 * WA GATEWAY MANAGER
 * Skrip ini otomatis mendeteksi departemen dari database
 * dan menjalankan instans gateway.js untuk masing-masing departemen.
 */

// Map untuk melacak proses yang sedang berjalan { deviceId: childProcess }
const runningProcesses = new Map();

async function startManager() {
    console.log("==========================================");
    console.log("   AIAGEN SMART GATEWAY MONITOR           ");
    console.log("   Monitoring database for new devices... ");
    console.log("==========================================");

    // Jalankan pengecekan setiap 10 detik
    setInterval(async () => {
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
            devices.forEach(device => {
                if (!runningProcesses.has(device.id)) {
                    spawnGateway(device);
                }
            });

            // 2. Bersihkan proses jika perangkat dihapus dari DB
            for (const [id, child] of runningProcesses.entries()) {
                const stillExists = devices.some(d => d.id === id);
                if (!stillExists) {
                    console.log(`[MANAGER] Perangkat ID ${id} dihapus dari DB. Mematikan gateway...`);
                    child.kill();
                    runningProcesses.delete(id);
                }
            }

        } catch (error) {
            console.error("[MANAGER] Error saat polling DB:", error.message);
        }
    }, 10000); // Cek setiap 10 detik
}

function spawnGateway(device) {
    const port = 3000 + (parseInt(device.department_id) - 1);
    
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
