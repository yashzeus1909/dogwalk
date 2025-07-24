import { users, walkers, bookings, type User, type InsertUser, type UpdateUserProfile, type Walker, type InsertWalker, type Booking, type InsertBooking } from "@shared/schema";
import { db } from "./db";
import { eq } from "drizzle-orm";

export interface IStorage {
  getUser(id: number): Promise<User | undefined>;
  getUserByUsername(username: string): Promise<User | undefined>;
  getUserByEmail(email: string): Promise<User | undefined>;
  createUser(user: InsertUser): Promise<User>;
  updateUserProfile(id: number, profile: UpdateUserProfile): Promise<User | undefined>;
  
  getAllWalkers(): Promise<Walker[]>;
  getWalker(id: number): Promise<Walker | undefined>;
  getWalkerByEmail(email: string): Promise<Walker | undefined>;
  createWalker(walker: InsertWalker): Promise<Walker>;
  updateWalker(id: number, walker: Partial<InsertWalker>): Promise<Walker | undefined>;
  deleteWalker(id: number): Promise<boolean>;
  
  getAllBookings(): Promise<Booking[]>;
  getBooking(id: number): Promise<Booking | undefined>;
  getBookingsByWalker(walkerId: number): Promise<Booking[]>;
  getBookingsByUser(userId: number): Promise<Booking[]>;
  createBooking(booking: InsertBooking): Promise<Booking>;
  updateBookingStatus(id: number, status: string): Promise<Booking | undefined>;
}

export class DatabaseStorage implements IStorage {
  async getUser(id: number): Promise<User | undefined> {
    const [user] = await db.select().from(users).where(eq(users.id, id));
    return user || undefined;
  }

  async getUserByUsername(username: string): Promise<User | undefined> {
    const [user] = await db.select().from(users).where(eq(users.email, username));
    return user || undefined;
  }

  async getUserByEmail(email: string): Promise<User | undefined> {
    const [user] = await db.select().from(users).where(eq(users.email, email));
    return user || undefined;
  }

  async createUser(insertUser: InsertUser): Promise<User> {
    const [user] = await db.insert(users).values(insertUser).returning();
    return user;
  }

  async updateUserProfile(id: number, profile: UpdateUserProfile): Promise<User | undefined> {
    await db.update(users).set(profile).where(eq(users.id, id));
    const [user] = await db.select().from(users).where(eq(users.id, id));
    return user || undefined;
  }

  async getAllWalkers(): Promise<Walker[]> {
    return await db.select().from(walkers);
  }

  async getWalker(id: number): Promise<Walker | undefined> {
    const [walker] = await db.select().from(walkers).where(eq(walkers.id, id));
    return walker || undefined;
  }

  async getWalkerByEmail(email: string): Promise<Walker | undefined> {
    const [walker] = await db.select().from(walkers).where(eq(walkers.email, email));
    return walker || undefined;
  }

  async createWalker(insertWalker: InsertWalker): Promise<Walker> {
    const [walker] = await db.insert(walkers).values(insertWalker).returning();
    return walker;
  }

  async getAllBookings(): Promise<Booking[]> {
    return await db.select().from(bookings);
  }

  async getBooking(id: number): Promise<Booking | undefined> {
    const [booking] = await db.select().from(bookings).where(eq(bookings.id, id));
    return booking || undefined;
  }

  async getBookingsByWalker(walkerId: number): Promise<Booking[]> {
    return await db.select().from(bookings).where(eq(bookings.walkerId, walkerId));
  }

  async getBookingsByUser(userId: number): Promise<Booking[]> {
    // For now, we'll return all bookings since we don't have a userId field in bookings table
    // In a real app, you'd want to add a userId field to the bookings table
    return await db.select().from(bookings);
  }

  async createBooking(insertBooking: InsertBooking): Promise<Booking> {
    const [booking] = await db.insert(bookings).values(insertBooking).returning();
    return booking;
  }

  async updateBookingStatus(id: number, status: string): Promise<Booking | undefined> {
    await db.update(bookings).set({ status }).where(eq(bookings.id, id));
    const [booking] = await db.select().from(bookings).where(eq(bookings.id, id));
    return booking || undefined;
  }

  async updateWalker(id: number, updates: Partial<InsertWalker>): Promise<Walker | undefined> {
    await db.update(walkers).set(updates).where(eq(walkers.id, id));
    const [walker] = await db.select().from(walkers).where(eq(walkers.id, id));
    return walker || undefined;
  }

  async deleteWalker(id: number): Promise<boolean> {
    const result = await db.delete(walkers).where(eq(walkers.id, id));
    return (result as any).affectedRows > 0;
  }
}

// Use demo storage for now since database connection is failing
// Use database storage for actual database insertion
export const storage = new DatabaseStorage();
