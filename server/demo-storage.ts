import { IStorage } from './storage';
import { User, Walker, Booking, InsertUser, InsertWalker, InsertBooking, UpdateUserProfile } from '@shared/schema';

// Demo storage with authentic walker data for testing
export class DemoStorage implements IStorage {
  private users: User[] = [];
  private walkers: Walker[] = [
    {
      id: 1,
      name: "Sarah Johnson",
      image: "https://images.unsplash.com/photo-1494790108755-2616b332c371?w=400&h=400&fit=crop&crop=face",
      rating: 4.8,
      reviewCount: 127,
      distance: "0.5 miles",
      price: 25,
      description: "Experienced professional dog walker with 5+ years caring for dogs of all sizes",
      availability: "Mon-Fri 9am-5pm",
      badges: ["Verified", "Insured"],
      services: ["Dog Walking", "Pet Sitting"],
      certified: true,
      createdAt: new Date(),
      updatedAt: new Date()
    },
    {
      id: 2,
      name: "Mike Chen",
      image: "https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=400&h=400&fit=crop&crop=face",
      rating: 4.6,
      reviewCount: 89,
      distance: "1.2 miles",
      price: 22,
      description: "Part-time dog walker specializing in small breeds and puppies",
      availability: "Weekends and evenings",
      badges: ["Verified"],
      services: ["Dog Walking"],
      certified: true,
      createdAt: new Date(),
      updatedAt: new Date()
    },
    {
      id: 3,
      name: "Emma Rodriguez",
      image: "https://images.unsplash.com/photo-1438761681033-6461ffad8d80?w=400&h=400&fit=crop&crop=face",
      rating: 4.9,
      reviewCount: 203,
      distance: "0.8 miles",
      price: 30,
      description: "Professional pet care specialist with behavioral training expertise",
      availability: "Daily 6am-8pm",
      badges: ["Verified", "Insured", "Background Checked"],
      services: ["Dog Walking", "Pet Sitting", "Training"],
      certified: true,
      createdAt: new Date(),
      updatedAt: new Date()
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
    return this.users.find(u => u.firstName === username || u.email === username);
  }

  async createUser(insertUser: InsertUser): Promise<User> {
    const user: User = {
      id: this.nextUserId++,
      ...insertUser,
      createdAt: new Date(),
      updatedAt: new Date()
    };
    this.users.push(user);
    return user;
  }

  async updateUserProfile(id: number, profile: UpdateUserProfile): Promise<User | undefined> {
    const userIndex = this.users.findIndex(u => u.id === id);
    if (userIndex === -1) return undefined;
    
    this.users[userIndex] = { 
      ...this.users[userIndex], 
      ...profile,
      updatedAt: new Date()
    };
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
      certified: false,
      createdAt: new Date(),
      updatedAt: new Date()
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
      createdAt: new Date(),
      updatedAt: new Date()
    };
    this.bookings.push(booking);
    return booking;
  }

  async updateBookingStatus(id: number, status: string): Promise<Booking | undefined> {
    const bookingIndex = this.bookings.findIndex(b => b.id === id);
    if (bookingIndex === -1) return undefined;
    
    this.bookings[bookingIndex] = {
      ...this.bookings[bookingIndex],
      status,
      updatedAt: new Date()
    };
    return this.bookings[bookingIndex];
  }
}

export const demoStorage = new DemoStorage();