import { mysqlTable, text, int, boolean, timestamp, json, varchar, decimal } from "drizzle-orm/mysql-core";
import { createInsertSchema } from "drizzle-zod";
import { z } from "zod";

export const walkers = mysqlTable("walkers", {
  id: int("id").primaryKey().autoincrement(),
  name: varchar("name", { length: 100 }).notNull(),
  image: varchar("image", { length: 500 }),
  rating: int("rating").notNull().default(0), // stored as integer (e.g., 49 for 4.9)
  reviewCount: int("review_count").notNull().default(0),
  distance: varchar("distance", { length: 50 }),
  price: int("price").notNull(), // price in dollars
  description: text("description"),
  availability: varchar("availability", { length: 100 }),
  badges: json("badges"),
  backgroundCheck: boolean("background_check").notNull().default(false),
  insured: boolean("insured").notNull().default(false),
  certified: boolean("certified").notNull().default(false),
  createdAt: timestamp("created_at").defaultNow(),
  updatedAt: timestamp("updated_at").defaultNow(),
});

export const bookings = mysqlTable("bookings", {
  id: int("id").primaryKey().autoincrement(),
  walkerId: int("walker_id").notNull(),
  userId: int("user_id"),
  dogName: varchar("dog_name", { length: 100 }).notNull(),
  dogSize: varchar("dog_size", { length: 20 }).notNull(),
  bookingDate: varchar("booking_date", { length: 20 }).notNull(),
  bookingTime: varchar("booking_time", { length: 20 }).notNull(),
  duration: int("duration").notNull(), // in minutes
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

export const users = mysqlTable("users", {
  id: int("id").primaryKey().autoincrement(),
  firstName: varchar("first_name", { length: 100 }).notNull(),
  lastName: varchar("last_name", { length: 100 }).notNull(),
  email: varchar("email", { length: 255 }).notNull().unique(),
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
