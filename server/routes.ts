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

  // Create a booking
  app.post("/api/bookings", async (req, res) => {
    try {
      const booking = await mysqlStorage.createBooking(req.body);
      res.status(201).json(booking);
    } catch (error) {
      console.error("Booking creation error:", error);
      res.status(500).json({ message: "Failed to create booking" });
    }
  });

  // Get all bookings
  app.get("/api/bookings", async (_req, res) => {
    try {
      const bookings = await mysqlStorage.getAllBookings();
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
      const booking = await storage.getBooking(id);
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

      const booking = await storage.updateBookingStatus(id, status);
      if (!booking) {
        return res.status(404).json({ message: "Booking not found" });
      }

      // Get walker information for email
      const walker = await storage.getWalker(booking.walkerId);
      
      if (walker) {
        // Send status update email (don't wait for it to complete)
        sendBookingStatusUpdateEmail({ booking, walker }, status).catch(error => {
          console.error("Failed to send status update email:", error);
        });
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
      const user = await storage.getUser(id);
      if (!user) {
        return res.status(404).json({ message: "User not found" });
      }
      // Don't send password in response
      const { password, ...userProfile } = user;
      res.json(userProfile);
    } catch (error) {
      res.status(500).json({ message: "Failed to fetch profile" });
    }
  });

  // Update user profile
  app.patch("/api/profile/:id", async (req, res) => {
    try {
      const id = parseInt(req.params.id);
      const profileData = updateUserProfileSchema.parse(req.body);
      const user = await storage.updateUserProfile(id, profileData);
      if (!user) {
        return res.status(404).json({ message: "User not found" });
      }
      // Don't send password in response
      const { password, ...userProfile } = user;
      res.json(userProfile);
    } catch (error) {
      if (error instanceof z.ZodError) {
        return res.status(400).json({ 
          message: "Invalid profile data", 
          errors: error.errors 
        });
      }
      res.status(500).json({ message: "Failed to update profile" });
    }
  });

  // Get user bookings
  app.get("/api/profile/:id/bookings", async (req, res) => {
    try {
      const id = parseInt(req.params.id);
      const bookings = await storage.getBookingsByUser(id);
      res.json(bookings);
    } catch (error) {
      res.status(500).json({ message: "Failed to fetch user bookings" });
    }
  });

  // Test email endpoint (for development/testing)
  app.post("/api/test-email", async (req, res) => {
    try {
      const { bookingId } = req.body;
      
      if (!bookingId) {
        return res.status(400).json({ message: "Booking ID is required" });
      }
      
      const booking = await storage.getBooking(bookingId);
      if (!booking) {
        return res.status(404).json({ message: "Booking not found" });
      }
      
      const walker = await storage.getWalker(booking.walkerId);
      if (!walker) {
        return res.status(404).json({ message: "Walker not found" });
      }
      
      const emailSent = await sendBookingConfirmationEmail({ booking, walker });
      
      res.json({ 
        success: emailSent,
        message: emailSent ? "Test email sent successfully" : "Failed to send test email"
      });
    } catch (error) {
      console.error("Test email error:", error);
      res.status(500).json({ message: "Failed to send test email" });
    }
  });

  const httpServer = createServer(app);
  return httpServer;
}
