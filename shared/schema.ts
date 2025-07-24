import { pgTable, text, integer, boolean, timestamp, json, varchar, decimal } from "drizzle-orm/pg-core";
import { createInsertSchema } from "drizzle-zod";
import { z } from "zod";

export const walkers = pgTable("walkers", {
  id: integer("id").primaryKey().generatedAlwaysAsIdentity(),
  name: varchar("name", { length: 100 }).notNull(),
  email: varchar("email", { length: 255 }).notNull().unique(),
  password: varchar("password", { length: 255 }).notNull(),
  image: varchar("image", { length: 500 }),
  rating: integer("rating").notNull().default(0), // stored as integer (e.g., 49 for 4.9)
  reviewCount: integer("review_count").notNull().default(0),
  distance: varchar("distance", { length: 50 }),
  price: integer("price").notNull(), // price in dollars
  description: text("description"),
  availability: varchar("availability", { length: 100 }),
  badges: json("badges"),
  services: json("services"), // Array of services offered
  backgroundCheck: boolean("background_check").notNull().default(false),
  insured: boolean("insured").notNull().default(false),
  certified: boolean("certified").notNull().default(false),
  createdAt: timestamp("created_at").defaultNow(),
  updatedAt: timestamp("updated_at").defaultNow(),
});

export const bookings = pgTable("bookings", {
  id: integer("id").primaryKey().generatedAlwaysAsIdentity(),
  walkerId: integer("walker_id").notNull(),
  userId: integer("user_id"),
  dogName: varchar("dog_name", { length: 100 }).notNull(),
  dogSize: varchar("dog_size", { length: 20 }).notNull(),
  bookingDate: varchar("booking_date", { length: 20 }).notNull(),
  bookingTime: varchar("booking_time", { length: 20 }).notNull(),
  duration: integer("duration").notNull(), // in minutes
  phone: varchar("phone", { length: 20 }).notNull(),
  address: text("address").notNull(),
  specialNotes: text("special_notes"),
  totalPrice: decimal("total_price", { precision: 10, scale: 2 }).notNull(),
  status: varchar("status", { length: 20 }).notNull().default("pending"),
  createdAt: timestamp("created_at").defaultNow(),
  updatedAt: timestamp("updated_at").defaultNow(),
});

export const insertWalkerSchema = createInsertSchema(walkers).omit({
  id: true,
});

export const insertBookingSchema = createInsertSchema(bookings).omit({
  id: true,
  createdAt: true,
  updatedAt: true,
});

export type InsertWalker = z.infer<typeof insertWalkerSchema>;
export type Walker = typeof walkers.$inferSelect;
export type InsertBooking = z.infer<typeof insertBookingSchema>;
export type Booking = typeof bookings.$inferSelect;

export const users = pgTable("users", {
  id: integer("id").primaryKey().generatedAlwaysAsIdentity(),
  firstName: varchar("first_name", { length: 100 }).notNull(),
  lastName: varchar("last_name", { length: 100 }).notNull(),
  email: varchar("email", { length: 255 }).notNull().unique(),
  password: varchar("password", { length: 255 }).notNull(),
  phone: varchar("phone", { length: 20 }),
  address: text("address"),
  createdAt: timestamp("created_at").defaultNow(),
  updatedAt: timestamp("updated_at").defaultNow(),
});

export const insertUserSchema = createInsertSchema(users).omit({
  id: true,
  createdAt: true,
  updatedAt: true,
});

export const updateUserProfileSchema = createInsertSchema(users).omit({
  id: true,
  createdAt: true,
  updatedAt: true,
});

export type InsertUser = z.infer<typeof insertUserSchema>;
export type User = typeof users.$inferSelect;
export type UpdateUserProfile = z.infer<typeof updateUserProfileSchema>;
