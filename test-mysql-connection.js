import mysql from 'mysql2/promise';
import { parse } from 'url';

async function testConnection() {
    try {
        console.log('Testing MySQL connection for XAMPP...');
        
        const DATABASE_URL = 'mysql://root:@localhost:3306/dogWalk';
        console.log('Database URL:', DATABASE_URL);
        
        const connectionUrl = parse(DATABASE_URL);
        
        const connection = await mysql.createConnection({
            host: connectionUrl.hostname,
            port: parseInt(connectionUrl.port) || 3306,
            user: connectionUrl.auth?.split(':')[0] || 'root',
            password: connectionUrl.auth?.split(':')[1] || '',
            database: connectionUrl.pathname?.slice(1) || 'dogWalk',
        });
        
        console.log('✅ Connected to MySQL successfully');
        
        // Test database and tables
        const [databases] = await connection.execute('SHOW DATABASES');
        console.log('Available databases:', databases.map(db => db.Database));
        
        try {
            const [tables] = await connection.execute('SHOW TABLES');
            console.log('Tables in dogWalk database:', tables);
            
            if (tables.length > 0) {
                const [walkers] = await connection.execute('SELECT COUNT(*) as count FROM walkers');
                console.log('Walkers count:', walkers[0].count);
                
                const [users] = await connection.execute('SELECT COUNT(*) as count FROM users');
                console.log('Users count:', users[0].count);
                
                const [bookings] = await connection.execute('SELECT COUNT(*) as count FROM bookings');
                console.log('Bookings count:', bookings[0].count);
            }
        } catch (tableError) {
            console.log('Database exists but tables not found. Run setup script first.');
        }
        
        await connection.end();
        console.log('✅ MySQL connection test completed');
        
    } catch (error) {
        console.error('❌ MySQL connection failed:', error.message);
        console.log('\nTroubleshooting:');
        console.log('1. Start XAMPP and ensure MySQL service is running');
        console.log('2. Check if database "dogWalk" exists');
        console.log('3. Run: php scripts/setup_xampp_database.php');
    }
}

testConnection();