import type { Express } from "express";
import { createServer, type Server } from "http";
import { demoStorage } from "./demo-storage";
import { z } from "zod";

export async function registerRoutes(app: Express): Promise<Server> {
  // Get all walkers
  app.get("/api/walkers", async (_req, res) => {
    try {
      const walkers = await demoStorage.getAllWalkers();
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
      const walker = await demoStorage.getWalker(id);
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
      const walkerData = req.body;
      
      // Validate required fields
      if (!walkerData.name || !walkerData.price) {
        return res.status(400).json({ message: "Name and price are required" });
      }

      const walker = await demoStorage.createWalker(walkerData);
      res.status(201).json(walker);
    } catch (error) {
      console.error("Walker creation error:", error);
      res.status(500).json({ message: "Failed to create walker" });
    }
  });

  // Update a walker
  app.put("/api/walkers/:id", async (req, res) => {
    try {
      const id = parseInt(req.params.id);
      const walkerData = req.body;
      
      // Validate required fields
      if (!walkerData.name || !walkerData.price) {
        return res.status(400).json({ message: "Name and price are required" });
      }

      const walker = await demoStorage.updateWalker(id, walkerData);
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
      
      const success = await demoStorage.deleteWalker(id);
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
      const booking = await demoStorage.createBooking(req.body);
      res.status(201).json(booking);
    } catch (error) {
      console.error("Booking creation error:", error);
      res.status(500).json({ message: "Failed to create booking" });
    }
  });

  // Get all bookings
  app.get("/api/bookings", async (_req, res) => {
    try {
      const bookings = await demoStorage.getAllBookings();
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
