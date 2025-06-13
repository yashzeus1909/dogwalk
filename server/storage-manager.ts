import { IStorage } from './storage';
import { DatabaseStorage } from './storage';
import { mysqlStorage } from './mysql-storage';
import { User, Walker, Booking, InsertUser, InsertWalker, InsertBooking, UpdateUserProfile } from '@shared/schema';

// In-memory storage with sample data for demonstration
class MemStorage implements IStorage {
  private users: User[] = [];
  private walkers: Walker[] = [
    {
      id: 1,
      name: "Sarah Johnson",
      email: "sarah@example.com",
      phone: "+1-555-0123",
      location: "Downtown",
      rating: 4.8,
      hourlyRate: 25,
      availability: "Mon-Fri 9am-5pm",
      experience: "5 years professional dog walking experience",
      specialties: "Large breeds, senior dogs",
      profileImage: "https://images.unsplash.com/photo-1494790108755-2616b332c371?w=400&h=400&fit=crop&crop=face",
      verified: true,
      createdAt: new Date()
    },
    {
      id: 2,
      name: "Mike Chen",
      email: "mike@example.com",
      phone: "+1-555-0124",
      location: "Midtown",
      rating: 4.6,
      hourlyRate: 22,
      availability: "Weekends and evenings",
      experience: "3 years part-time dog walking",
      specialties: "Small dogs, puppies",
      profileImage: "https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=400&h=400&fit=crop&crop=face",
      verified: true,
      createdAt: new Date()
    },
    {
      id: 3,
      name: "Emma Rodriguez",
      email: "emma@example.com",
      phone: "+1-555-0125",
      location: "Uptown",
      rating: 4.9,
      hourlyRate: 30,
      availability: "Daily 6am-8pm",
      experience: "7 years professional pet care",
      specialties: "Multiple dogs, behavioral training",
      profileImage: "https://images.unsplash.com/photo-1438761681033-6461ffad8d80?w=400&h=400&fit=crop&crop=face",
      verified: true,
      createdAt: new Date()
    }
  ];
  private bookings: Booking[] = [];
  private nextUserId = 1;
  private nextWalkerId = 4;
  private nextBookingId = 1;

  async getUser(id: number): Promise<User | undefined> {
    return this.users.find(u => u.id === id);
  }

  async getUserByUsername(username: string): Promise<User | undefined> {
    return this.users.find(u => u.username === username);
  }

  async createUser(insertUser: InsertUser): Promise<User> {
    const user: User = {
      id: this.nextUserId++,
      ...insertUser,
      createdAt: new Date()
    };
    this.users.push(user);
    return user;
  }

  async updateUserProfile(id: number, profile: UpdateUserProfile): Promise<User | undefined> {
    const userIndex = this.users.findIndex(u => u.id === id);
    if (userIndex === -1) return undefined;
    
    this.users[userIndex] = { ...this.users[userIndex], ...profile };
    return this.users[userIndex];
  }

  async getAllWalkers(): Promise<Walker[]> {
    return this.walkers;
  }

  async getWalker(id: number): Promise<Walker | undefined> {
    return this.walkers.find(w => w.id === id);
  }

  async createWalker(insertWalker: InsertWalker): Promise<Walker> {
    const walker: Walker = {
      id: this.nextWalkerId++,
      ...insertWalker,
      verified: false,
      createdAt: new Date()
    };
    this.walkers.push(walker);
    return walker;
  }

  async getAllBookings(): Promise<Booking[]> {
    return this.bookings;
  }

  async getBooking(id: number): Promise<Booking | undefined> {
    return this.bookings.find(b => b.id === id);
  }

  async getBookingsByWalker(walkerId: number): Promise<Booking[]> {
    return this.bookings.filter(b => b.walkerId === walkerId);
  }

  async getBookingsByUser(userId: number): Promise<Booking[]> {
    return this.bookings.filter(b => b.userId === userId);
  }

  async createBooking(insertBooking: InsertBooking): Promise<Booking> {
    const booking: Booking = {
      id: this.nextBookingId++,
      ...insertBooking,
      status: 'pending',
      createdAt: new Date()
    };
    this.bookings.push(booking);
    return booking;
  }

  async updateBookingStatus(id: number, status: string): Promise<Booking | undefined> {
    const bookingIndex = this.bookings.findIndex(b => b.id === id);
    if (bookingIndex === -1) return undefined;
    
    this.bookings[bookingIndex].status = status;
    return this.bookings[bookingIndex];
  }
}

// Test database connectivity and choose storage
async function createStorage(): Promise<IStorage> {
  try {
    // Try XAMPP MySQL first
    await mysqlStorage.getAllWalkers();
    console.log('✅ Using XAMPP MySQL storage');
    return mysqlStorage;
  } catch (error) {
    console.log('⚠️ XAMPP MySQL not available, using demo storage');
    return new MemStorage();
  }
}

export const storage = await createStorage();