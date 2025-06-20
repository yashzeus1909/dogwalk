import type { Express } from "express";
import { createServer, type Server } from "http";
import { storage } from "./storage";
import { insertWalkerSchema } from "@shared/schema";
import { z } from "zod";

export async function registerRoutes(app: Express): Promise<Server> {
  // Get all walkers
  app.get("/api/walkers", async (_req, res) => {
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

  const httpServer = createServer(app);
  return httpServer;
}
