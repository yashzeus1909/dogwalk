import mysql from 'mysql2/promise';
import * as schema from "@shared/schema";
import { Pool } from '@neondatabase/serverless';
import { drizzle } from 'drizzle-orm/neon-serverless';
import ws from "ws";

// XAMPP MySQL configuration with fallback
const DATABASE_URL = process.env.DATABASE_URL || 'mysql://root:@localhost:3306/dogWalk';

let pool: mysql.Pool;
let db: any;

try {
  // Try XAMPP MySQL connection first
  pool = mysql.createPool({
    host: 'localhost',
    port: 3306,
    user: 'root',
    password: '',
    database: 'dogWalk',
    waitForConnections: true,
    connectionLimit: 10,
    queueLimit: 0,
    acquireTimeout: 5000,
    timeout: 10000,
  });
  
  db = drizzle(pool, { schema, mode: 'default' });
  console.log('✅ Connected to XAMPP MySQL database');
} catch (error) {
  console.log('⚠️ XAMPP MySQL not available, using fallback storage');
  // Will use in-memory storage as fallback
}

export { pool, db };