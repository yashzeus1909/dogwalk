import type { Express } from "express";
import { createServer, type Server } from "http";
import { storage } from "./storage";
import { insertWalkerSchema } from "@shared/schema";
import { z } from "zod";
import { spawn } from "child_process";
import path from "path";
import bcrypt from "bcrypt";
import { db } from "./db";
import { users, walkers } from "@shared/schema";
import { eq } from "drizzle-orm";
import "./types";

// PHP executor function
async function executePHP(phpFile: string, method: string, body?: any, query?: any, sessionData?: any): Promise<string> {
  return new Promise((resolve, reject) => {
    const phpPath = path.join(process.cwd(), 'api', phpFile);
    
    // Set up environment for PHP with database connection details
    const env = { 
      ...process.env,
      REQUEST_METHOD: method,
      CONTENT_TYPE: 'application/json',
      QUERY_STRING: query ? new URLSearchParams(query).toString() : '',
      DB_HOST: process.env.DB_HOST || 'localhost',
      DB_PORT: process.env.DB_PORT || '5432',
      DB_DATABASE: process.env.DB_DATABASE || 'dog_walker_app',
      DB_USERNAME: process.env.DB_USER || 'postgres',
      DB_PASSWORD: process.env.DB_PASSWORD || '',
      POST_DATA: body ? JSON.stringify(body) : '',
      HTTP_RAW_POST_DATA: body ? JSON.stringify(body) : ''
    };
    
    // Use PHP CLI with session simulation
    const args = ['-c', '/etc/php/8.2/cli/php.ini', phpPath];
    
    const php = spawn('php', args, { 
      env,
      stdio: ['pipe', 'pipe', 'pipe'],
      cwd: process.cwd()
    });
    
    let stdout = '';
    let stderr = '';
    
    php.stdout.on('data', (data) => {
      stdout += data.toString();
    });
    
    php.stderr.on('data', (data) => {
      stderr += data.toString();
    });
    
    php.on('close', (code) => {
      if (code !== 0) {
        reject(new Error(`PHP process exited with code ${code}: ${stderr}`));
      } else {
        // Remove any HTML/headers from output to get just JSON
        const jsonMatch = stdout.match(/\{.*\}/s);
        if (jsonMatch) {
          resolve(jsonMatch[0]);
        } else {
          resolve(stdout);
        }
      }
    });
    
    // Send POST data to PHP script via stdin  
    if (body && method === 'POST') {
      console.log('Node.js received body:', body);
      console.log('Sending to PHP stdin:', JSON.stringify(body));
      php.stdin.write(JSON.stringify(body));
    }
    php.stdin.end();
  });
}

export async function registerRoutes(app: Express): Promise<Server> {
  
  // Database-based registration endpoint
  app.post('/api/register', async (req, res) => {
    try {
      const { firstName, lastName, email, password, phone, address } = req.body;
      
      // Validation
      if (!firstName || !lastName || !email || !password) {
        return res.status(400).json({ success: false, message: 'All fields are required' });
      }
      
      if (!email.includes('@')) {
        return res.status(400).json({ success: false, message: 'Invalid email format' });
      }
      
      if (password.length < 6) {
        return res.status(400).json({ success: false, message: 'Password must be at least 6 characters long' });
      }
      
      // Check if email already exists
      const existingUser = await db.select().from(users).where(eq(users.email, email));
      
      if (existingUser.length > 0) {
        return res.status(400).json({ success: false, message: 'Email address is already registered' });
      }
      
      // Hash password
      const hashedPassword = await bcrypt.hash(password, 10);
      
      // Create user
      const result = await db.insert(users).values({
        firstName,
        lastName,
        email,
        password: hashedPassword,
        phone,
        address
      }).returning();
      
      res.json({
        success: true,
        message: 'Account created successfully',
        user_id: result[0].id
      });
      
    } catch (error) {
      console.error('Registration error:', error);
      res.status(500).json({ success: false, message: 'Registration failed' });
    }
  });
  
  // Database-based login endpoint
  app.post('/api/login', async (req, res) => {
    try {
      const { email, password } = req.body;
      
      if (!email || !password) {
        return res.status(400).json({ success: false, message: 'Email and password are required' });
      }
      
      // Get user from database
      const result = await db.select().from(users).where(eq(users.email, email));
      
      if (result.length === 0) {
        return res.status(401).json({ success: false, message: 'Invalid credentials' });
      }
      
      const user = result[0];
      
      // Verify password
      const isValidPassword = await bcrypt.compare(password, user.password);
      
      if (!isValidPassword) {
        return res.status(401).json({ success: false, message: 'Invalid credentials' });
      }
      
      // Set session
      req.session.userId = user.id;
      req.session.user = {
        id: user.id,
        name: `${user.firstName} ${user.lastName}`,
        email: user.email
      };
      
      res.json({
        success: true,
        message: 'Login successful',
        user: {
          id: user.id,
          name: `${user.firstName} ${user.lastName}`,
          email: user.email
        }
      });
      
    } catch (error) {
      console.error('Login error:', error);
      res.status(500).json({ success: false, message: 'Login failed' });
    }
  });
  
  // Database-based walkers endpoint  
  app.get('/api/walkers', async (req, res) => {
    try {
      const result = await db.select().from(walkers);
      
      const walkersData = result.map(walker => ({
        id: walker.id,
        name: walker.name,
        email: walker.email,
        phone: walker.phone,
        description: walker.description,
        price: parseFloat(walker.price?.toString() || '0'),
        image: walker.image,
        rating: parseFloat(walker.rating?.toString() || '0'),
        reviewCount: walker.reviewCount || 0,
        distance: walker.distance,
        availability: walker.availability,
        badges: walker.badges || [],
        backgroundCheck: walker.backgroundCheck,
        insured: walker.insured,
        certified: walker.certified
      }));
      
      res.json(walkersData);
      
    } catch (error) {
      console.error('Walkers error:', error);
      res.status(500).json({ success: false, message: 'Failed to get walkers' });
    }
  });
  
  // Check authentication endpoint
  app.get('/api/check-auth', (req, res) => {
    if (req.session.userId) {
      res.json({ success: true, user: req.session.user });
    } else {
      res.status(401).json({ success: false, message: 'Not authenticated' });
    }
  });

  // Logout endpoint
  app.post('/api/logout', (req, res) => {
    req.session.destroy((err) => {
      if (err) {
        return res.status(500).json({ success: false, message: 'Logout failed' });
      }
      res.json({ success: true, message: 'Logged out successfully' });
    });
  });
  
  // Users list endpoint (for testing)
  app.get('/api/users', async (req, res) => {
    try {
      const result = await db.select({
        id: users.id,
        firstName: users.firstName,
        lastName: users.lastName,
        email: users.email,
        createdAt: users.createdAt
      }).from(users);
      
      res.json(result);
    } catch (error) {
      console.error('Users error:', error);
      res.status(500).json({ success: false, message: 'Failed to get users' });
    }
  });
  // Keep existing PHP-based routes working
  app.get("/api/walkers.php", async (_req, res) => {
    try {
      const walkers = await storage.getAllWalkers();
      res.json(walkers);
    } catch (error) {
      console.error("Walkers fetch error:", error);
      res.status(500).json({ message: "Failed to fetch walkers" });
    }
  });

  // Get walker by ID
  app.get("/api/walkers/:id", async (req, res) => {
    try {
      const id = parseInt(req.params.id);
      const walker = await storage.getWalker(id);
      if (!walker) {
        return res.status(404).json({ message: "Walker not found" });
      }
      res.json(walker);
    } catch (error) {
      res.status(500).json({ message: "Failed to fetch walker" });
    }
  });

  // Create a new walker
  app.post("/api/walkers", async (req, res) => {
    try {
      // Validate the request body using the schema
      const validatedData = insertWalkerSchema.parse(req.body);
      
      // Check if email already exists
      const existingWalker = await storage.getWalkerByEmail(validatedData.email);
      if (existingWalker) {
        return res.status(400).json({ message: "Email address is already registered" });
      }

      const walker = await storage.createWalker(validatedData);
      res.status(201).json(walker);
    } catch (error) {
      console.error("Walker creation error:", error);
      if (error instanceof z.ZodError) {
        return res.status(400).json({ 
          message: "Validation error", 
          details: error.errors 
        });
      }
      res.status(500).json({ message: "Failed to create walker" });
    }
  });

  // Update a walker
  app.put("/api/walkers/:id", async (req, res) => {
    try {
      const id = parseInt(req.params.id);
      const walkerData = req.body;
      
      // Check if updating email and if it's already taken by another walker
      if (walkerData.email) {
        const existingWalker = await storage.getWalkerByEmail(walkerData.email);
        if (existingWalker && existingWalker.id !== id) {
          return res.status(400).json({ message: "Email address is already registered" });
        }
      }

      const walker = await storage.updateWalker(id, walkerData);
      if (!walker) {
        return res.status(404).json({ message: "Walker not found" });
      }
      
      res.json(walker);
    } catch (error) {
      console.error("Walker update error:", error);
      res.status(500).json({ message: "Failed to update walker" });
    }
  });

  // Delete a walker
  app.delete("/api/walkers/:id", async (req, res) => {
    try {
      const id = parseInt(req.params.id);
      
      const success = await storage.deleteWalker(id);
      if (!success) {
        return res.status(404).json({ message: "Walker not found" });
      }
      
      res.json({ message: "Walker deleted successfully" });
    } catch (error) {
      console.error("Walker deletion error:", error);
      res.status(500).json({ message: "Failed to delete walker" });
    }
  });

  // Create a booking
  app.post("/api/bookings", async (req, res) => {
    try {
      const booking = await storage.createBooking(req.body);
      res.status(201).json(booking);
    } catch (error) {
      console.error("Booking creation error:", error);
      res.status(500).json({ message: "Failed to create booking" });
    }
  });

  // Update booking status
  app.patch("/api/bookings/:id", async (req, res) => {
    try {
      const id = parseInt(req.params.id);
      const { status } = req.body;
      const booking = await storage.updateBookingStatus(id, status);
      if (!booking) {
        return res.status(404).json({ message: "Booking not found" });
      }
      res.json(booking);
    } catch (error) {
      console.error("Booking update error:", error);
      res.status(500).json({ message: "Failed to update booking" });
    }
  });

  // Get all bookings
  app.get("/api/bookings", async (_req, res) => {
    try {
      const bookings = await storage.getAllBookings();
      res.json(bookings);
    } catch (error) {
      console.error("Bookings fetch error:", error);
      res.status(500).json({ message: "Failed to fetch bookings" });
    }
  });

  // Get booking by ID
  app.get("/api/bookings/:id", async (req, res) => {
    try {
      const id = parseInt(req.params.id);
      const booking = await demoStorage.getBooking(id);
      if (!booking) {
        return res.status(404).json({ message: "Booking not found" });
      }
      res.json(booking);
    } catch (error) {
      res.status(500).json({ message: "Failed to fetch booking" });
    }
  });

  // Update booking status
  app.patch("/api/bookings/:id/status", async (req, res) => {
    try {
      const id = parseInt(req.params.id);
      const { status } = req.body;
      
      if (!status || typeof status !== 'string') {
        return res.status(400).json({ message: "Valid status is required" });
      }

      const booking = await demoStorage.updateBookingStatus(id, status);
      if (!booking) {
        return res.status(404).json({ message: "Booking not found" });
      }

      res.json(booking);
    } catch (error) {
      res.status(500).json({ message: "Failed to update booking status" });
    }
  });

  // Get user profile
  app.get("/api/profile/:id", async (req, res) => {
    try {
      const id = parseInt(req.params.id);
      const user = await demoStorage.getUser(id);
      if (!user) {
        return res.status(404).json({ message: "User not found" });
      }
      res.json(user);
    } catch (error) {
      res.status(500).json({ message: "Failed to fetch profile" });
    }
  });

  // Update user profile
  app.patch("/api/profile/:id", async (req, res) => {
    try {
      const id = parseInt(req.params.id);
      const user = await demoStorage.updateUserProfile(id, req.body);
      if (!user) {
        return res.status(404).json({ message: "User not found" });
      }
      res.json(user);
    } catch (error) {
      res.status(500).json({ message: "Failed to update profile" });
    }
  });

  // User registration endpoint
  app.post("/api/register", async (req, res) => {
    try {
      const { firstName, lastName, email, password, phone, address } = req.body;
      
      // Basic validation
      if (!firstName || !lastName || !email || !password || !phone) {
        return res.status(400).json({ 
          success: false, 
          message: "All fields are required" 
        });
      }
      
      if (password.length < 6) {
        return res.status(400).json({ 
          success: false, 
          message: "Password must be at least 6 characters long" 
        });
      }
      
      // Check if user already exists
      const existingUser = await storage.getUserByEmail?.(email);
      if (existingUser) {
        return res.status(400).json({ 
          success: false, 
          message: "Email address already exists" 
        });
      }
      
      // Create user
      const user = await storage.createUser({
        firstName,
        lastName,
        email,
        password, // In a real app, this should be hashed
        phone,
        address
      });
      
      res.json({
        success: true,
        message: "Account created successfully",
        user_id: user.id
      });
    } catch (error) {
      console.error("Registration error:", error);
      res.status(500).json({ 
        success: false, 
        message: "Registration failed: " + error.message 
      });
    }
  });

  // Get user bookings
  app.get("/api/profile/:id/bookings", async (req, res) => {
    try {
      const id = parseInt(req.params.id);
      const bookings = await demoStorage.getBookingsByUser(id);
      res.json(bookings);
    } catch (error) {
      res.status(500).json({ message: "Failed to fetch user bookings" });
    }
  });

  // Customer registration endpoint
  app.post('/api/customer_register', async (req, res) => {
    try {
      const { firstName, lastName, email, password, phone, address } = req.body;
      
      // Validate required fields
      if (!firstName || !lastName || !email || !password || !phone) {
        return res.status(400).json({ success: false, message: 'All fields are required' });
      }
      
      // Validate password length
      if (password.length < 6) {
        return res.status(400).json({ success: false, message: 'Password must be at least 6 characters long' });
      }
      
      // Check if email already exists
      const existingUser = await storage.getUserByEmail(email);
      if (existingUser) {
        return res.status(400).json({ success: false, message: 'Email address already exists' });
      }
      
      // Create user
      const user = await storage.createUser({
        firstName,
        lastName,
        email,
        phone,
        address: address || '',
        password // Note: In production, this should be hashed
      });
      
      res.json({
        success: true,
        message: 'Account created successfully',
        user_id: user.id
      });
      
    } catch (error) {
      console.error('Registration error:', error);
      res.status(500).json({ success: false, message: 'Registration failed' });
    }
  });

  // PHP endpoints for customer authentication and bookings
  
  // Customer authentication check
  app.get("/api/check_customer_auth.php", async (_req, res) => {
    try {
      const output = await executePHP('check_customer_auth.php', 'GET');
      res.setHeader('Content-Type', 'application/json');
      res.send(output);
    } catch (error) {
      console.error("PHP auth check error:", error);
      res.status(500).json({ success: false, message: "Internal server error" });
    }
  });

  // Customer login
  app.post("/api/customer_login.php", async (req, res) => {
    try {
      const output = await executePHP('customer_login.php', 'POST', req.body);
      res.setHeader('Content-Type', 'application/json');
      res.send(output);
    } catch (error) {
      console.error("PHP login error:", error);
      res.status(500).json({ success: false, message: "Internal server error" });
    }
  });

  // Customer logout
  app.post("/api/customer_logout.php", async (_req, res) => {
    try {
      const output = await executePHP('customer_logout.php', 'POST');
      res.setHeader('Content-Type', 'application/json');
      res.send(output);
    } catch (error) {
      console.error("PHP logout error:", error);
      res.status(500).json({ success: false, message: "Internal server error" });
    }
  });

  // Customer registration
  app.post("/api/customer_register.php", async (req, res) => {
    try {
      const output = await executePHP('customer_register.php', 'POST', req.body);
      res.setHeader('Content-Type', 'application/json');
      res.send(output);
    } catch (error) {
      console.error("PHP registration error:", error);
      res.status(500).json({ success: false, message: "Internal server error" });
    }
  });

  // Customer bookings
  app.get("/api/customer_bookings.php", async (req, res) => {
    try {
      const output = await executePHP('customer_bookings.php', 'GET', undefined, req.query);
      res.setHeader('Content-Type', 'application/json');
      res.send(output);
    } catch (error) {
      console.error("PHP bookings error:", error);
      res.status(500).json({ success: false, message: "Internal server error" });
    }
  });

  // Add booking
  app.post("/api/add_booking.php", async (req, res) => {
    try {
      const output = await executePHP('add_booking.php', 'POST', req.body);
      res.setHeader('Content-Type', 'application/json');
      res.send(output);
    } catch (error) {
      console.error("PHP add booking error:", error);
      res.status(500).json({ success: false, message: "Internal server error" });
    }
  });

  // Walkers PHP endpoint
  app.get("/api/walkers.php", async (req, res) => {
    try {
      const output = await executePHP('walkers.php', 'GET', undefined, req.query);
      res.setHeader('Content-Type', 'application/json');
      res.send(output);
    } catch (error) {
      console.error("PHP walkers error:", error);
      res.status(500).json({ success: false, message: "Internal server error" });
    }
  });

  const httpServer = createServer(app);
  return httpServer;
}
