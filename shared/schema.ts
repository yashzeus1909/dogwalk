import { pgTable, text, serial, integer, boolean, timestamp } from "drizzle-orm/pg-core";
import { createInsertSchema } from "drizzle-zod";
import { z } from "zod";

export const walkers = pgTable("walkers", {
  id: serial("id").primaryKey(),
  name: text("name").notNull(),
  image: text("image").notNull(),
  rating: integer("rating").notNull(), // stored as integer (e.g., 49 for 4.9)
  reviewCount: integer("review_count").notNull(),
  distance: text("distance").notNull(),
  price: integer("price").notNull(), // price in dollars
  description: text("description").notNull(),
  availability: text("availability").notNull(),
  badges: text("badges").array().notNull().default([]),
  backgroundCheck: boolean("background_check").notNull().default(false),
  insured: boolean("insured").notNull().default(false),
  certified: boolean("certified").notNull().default(false),
});

export const bookings = pgTable("bookings", {
  id: serial("id").primaryKey(),
  walkerId: integer("walker_id").notNull(),
  dogName: text("dog_name").notNull(),
  dogSize: text("dog_size").notNull(),
  date: text("date").notNull(),
  time: text("time").notNull(),
  duration: integer("duration").notNull(), // in minutes
  instructions: text("instructions"),
  phone: text("phone").notNull(),
  email: text("email").notNull(),
  serviceFee: integer("service_fee").notNull(), // in cents
  appFee: integer("app_fee").notNull(), // in cents
  total: integer("total").notNull(), // in cents
  status: text("status").notNull().default("pending"),
  createdAt: timestamp("created_at").defaultNow(),
});

export const insertWalkerSchema = createInsertSchema(walkers).omit({
  id: true,
});

export const insertBookingSchema = createInsertSchema(bookings).omit({
  id: true,
  createdAt: true,
});

export type InsertWalker = z.infer<typeof insertWalkerSchema>;
export type Walker = typeof walkers.$inferSelect;
export type InsertBooking = z.infer<typeof insertBookingSchema>;
export type Booking = typeof bookings.$inferSelect;

export const users = pgTable("users", {
  id: serial("id").primaryKey(),
  username: text("username").notNull().unique(),
  password: text("password").notNull(),
});

export const insertUserSchema = createInsertSchema(users).pick({
  username: true,
  password: true,
});

export type InsertUser = z.infer<typeof insertUserSchema>;
export type User = typeof users.$inferSelect;
