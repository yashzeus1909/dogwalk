import { users, walkers, bookings, type User, type InsertUser, type Walker, type InsertWalker, type Booking, type InsertBooking } from "@shared/schema";

export interface IStorage {
  getUser(id: number): Promise<User | undefined>;
  getUserByUsername(username: string): Promise<User | undefined>;
  createUser(user: InsertUser): Promise<User>;
  
  getAllWalkers(): Promise<Walker[]>;
  getWalker(id: number): Promise<Walker | undefined>;
  createWalker(walker: InsertWalker): Promise<Walker>;
  
  getAllBookings(): Promise<Booking[]>;
  getBooking(id: number): Promise<Booking | undefined>;
  getBookingsByWalker(walkerId: number): Promise<Booking[]>;
  createBooking(booking: InsertBooking): Promise<Booking>;
  updateBookingStatus(id: number, status: string): Promise<Booking | undefined>;
}

export class MemStorage implements IStorage {
  private users: Map<number, User>;
  private walkers: Map<number, Walker>;
  private bookings: Map<number, Booking>;
  private currentUserId: number;
  private currentWalkerId: number;
  private currentBookingId: number;

  constructor() {
    this.users = new Map();
    this.walkers = new Map();
    this.bookings = new Map();
    this.currentUserId = 1;
    this.currentWalkerId = 1;
    this.currentBookingId = 1;

    // Initialize with sample walkers
    this.initializeWalkers();
  }

  private initializeWalkers() {
    const sampleWalkers: InsertWalker[] = [
      {
        name: "Sarah M.",
        image: "https://images.unsplash.com/photo-1438761681033-6461ffad8d80?ixlib=rb-4.0.3&ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=crop&w=150&h=150",
        rating: 49,
        reviewCount: 127,
        distance: "0.8 miles away",
        price: 25,
        description: "Experienced dog walker with 5+ years caring for dogs of all sizes. I love long walks in the park and ensuring your furry friend gets the exercise they need!",
        availability: "Available today",
        badges: ["Background checked"],
        backgroundCheck: true,
        insured: false,
        certified: false,
      },
      {
        name: "Mike R.",
        image: "https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?ixlib=rb-4.0.3&ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=crop&w=150&h=150",
        rating: 47,
        reviewCount: 89,
        distance: "1.2 miles away",
        price: 22,
        description: "Former veterinary technician turned professional dog walker. I specialize in high-energy dogs and provide detailed updates with photos after each walk.",
        availability: "Available tomorrow",
        badges: ["Insured"],
        backgroundCheck: false,
        insured: true,
        certified: false,
      },
      {
        name: "Emma L.",
        image: "https://images.unsplash.com/photo-1494790108755-2616b612b786?ixlib=rb-4.0.3&ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=crop&w=150&h=150",
        rating: 50,
        reviewCount: 45,
        distance: "0.5 miles away",
        price: 30,
        description: "Certified dog trainer offering walking services. Perfect for dogs that need behavioral guidance or socialization during their walks. Premium service guaranteed!",
        availability: "Available today",
        badges: ["Certified trainer"],
        backgroundCheck: true,
        insured: true,
        certified: true,
      },
    ];

    sampleWalkers.forEach(walker => {
      this.createWalker(walker);
    });
  }

  async getUser(id: number): Promise<User | undefined> {
    return this.users.get(id);
  }

  async getUserByUsername(username: string): Promise<User | undefined> {
    return Array.from(this.users.values()).find(
      (user) => user.username === username,
    );
  }

  async createUser(insertUser: InsertUser): Promise<User> {
    const id = this.currentUserId++;
    const user: User = { ...insertUser, id };
    this.users.set(id, user);
    return user;
  }

  async getAllWalkers(): Promise<Walker[]> {
    return Array.from(this.walkers.values());
  }

  async getWalker(id: number): Promise<Walker | undefined> {
    return this.walkers.get(id);
  }

  async createWalker(insertWalker: InsertWalker): Promise<Walker> {
    const id = this.currentWalkerId++;
    const walker: Walker = { 
      ...insertWalker, 
      id,
      badges: insertWalker.badges || [],
      backgroundCheck: insertWalker.backgroundCheck || false,
      insured: insertWalker.insured || false,
      certified: insertWalker.certified || false
    };
    this.walkers.set(id, walker);
    return walker;
  }

  async getAllBookings(): Promise<Booking[]> {
    return Array.from(this.bookings.values());
  }

  async getBooking(id: number): Promise<Booking | undefined> {
    return this.bookings.get(id);
  }

  async getBookingsByWalker(walkerId: number): Promise<Booking[]> {
    return Array.from(this.bookings.values()).filter(
      booking => booking.walkerId === walkerId
    );
  }

  async createBooking(insertBooking: InsertBooking): Promise<Booking> {
    const id = this.currentBookingId++;
    const booking: Booking = { 
      ...insertBooking, 
      id, 
      status: insertBooking.status || "pending",
      instructions: insertBooking.instructions || null,
      createdAt: new Date() 
    };
    this.bookings.set(id, booking);
    return booking;
  }

  async updateBookingStatus(id: number, status: string): Promise<Booking | undefined> {
    const booking = this.bookings.get(id);
    if (booking) {
      const updatedBooking = { ...booking, status };
      this.bookings.set(id, updatedBooking);
      return updatedBooking;
    }
    return undefined;
  }
}

export const storage = new MemStorage();
