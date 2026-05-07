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
        
        const [depts] = await connection.execute('SELECT * FROM departments');
        console.log("--- Departments ---");
        console.table(depts);
        
        await connection.end();
    } catch (error) {
        console.error("DB Error:", error.message);
    }
}
test();
