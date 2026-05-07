require('dotenv').config({ path: '../.env' });
const mysql = require('mysql2/promise');

async function test() {
    try {
        const connection = await mysql.createConnection({
            host: process.env.DB_HOST || '127.0.0.1',
            user: process.env.DB_USERNAME || 'root',
            password: process.env.DB_PASSWORD || '',
            database: process.env.DB_DATABASE || 'aiagen'
        });
        console.log("DB Connection Success!");
        const [rows] = await connection.execute('SELECT 1');
        console.log("Query Success!");
        await connection.end();
    } catch (error) {
        console.error("DB Connection Failed:", error.message);
    }
}
test();
