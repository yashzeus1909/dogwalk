import mysql from 'mysql2/promise';

// Simple MySQL storage implementation for XAMPP
export class MySQLStorage {
  private pool: mysql.Pool;

  constructor() {
    const connectionUrl = new URL(process.env.DATABASE_URL!);
    
    this.pool = mysql.createPool({
      host: connectionUrl.hostname,
      port: parseInt(connectionUrl.port) || 3306,
      user: connectionUrl.username,
      password: connectionUrl.password,
      database: connectionUrl.pathname.slice(1),
      waitForConnections: true,
      connectionLimit: 10,
      queueLimit: 0,
    });
  }

  async getAllWalkers() {
    const [rows] = await this.pool.execute('SELECT * FROM walkers ORDER BY rating DESC');
    return rows;
  }

  async getAllBookings() {
    const [rows] = await this.pool.execute(`
      SELECT b.*, w.name as walker_name, w.image as walker_image,
             u.first_name, u.last_name
      FROM bookings b
      LEFT JOIN walkers w ON b.walker_id = w.id
      LEFT JOIN users u ON b.user_id = u.id
      ORDER BY b.created_at DESC
    `);
    return rows;
  }

  async createBooking(booking: any) {
    const [result] = await this.pool.execute(`
      INSERT INTO bookings (walker_id, user_id, dog_name, dog_size, booking_date, booking_time, 
                           duration, phone, address, special_notes, total_price, status)
      VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    `, [
      booking.walkerId,
      booking.userId || 1,
      booking.dogName,
      booking.dogSize,
      booking.bookingDate,
      booking.bookingTime,
      booking.duration,
      booking.phone,
      booking.address || '',
      booking.specialNotes || '',
      booking.totalPrice,
      booking.status || 'pending'
    ]);
    
    return { id: (result as any).insertId };
  }

  async getUser(id: number) {
    const [rows] = await this.pool.execute('SELECT * FROM users WHERE id = ?', [id]);
    return (rows as any[])[0] || undefined;
  }

  async getUserByEmail(email: string) {
    const [rows] = await this.pool.execute('SELECT * FROM users WHERE email = ?', [email]);
    return (rows as any[])[0] || undefined;
  }

  async createUser(user: any) {
    const [result] = await this.pool.execute(`
      INSERT INTO users (first_name, last_name, email, phone, address)
      VALUES (?, ?, ?, ?, ?)
    `, [user.firstName, user.lastName, user.email, user.phone || '', user.address || '']);
    
    return { id: (result as any).insertId };
  }
}

export const mysqlStorage = new MySQLStorage();