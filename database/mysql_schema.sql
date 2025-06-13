-- PawWalk Dog Walker Application - MySQL Database Schema
-- Complete database structure with sample data

-- Create database (run this separately if needed)
-- CREATE DATABASE pawwalk_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
-- USE pawwalk_db;

-- Drop tables if they exist (for clean setup)
DROP TABLE IF EXISTS bookings;
DROP TABLE IF EXISTS walkers;
DROP TABLE IF EXISTS users;

-- Users table
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    phone VARCHAR(20),
    address TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Walkers table
CREATE TABLE walkers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(255) NOT NULL,
    image VARCHAR(500),
    rating INT DEFAULT 0 CHECK (rating >= 0 AND rating <= 50),
    review_count INT DEFAULT 0,
    distance VARCHAR(50),
    price INT NOT NULL,
    description TEXT,
    availability VARCHAR(100),
    badges JSON,
    background_check BOOLEAN DEFAULT false,
    insured BOOLEAN DEFAULT false,
    certified BOOLEAN DEFAULT false,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Bookings table
CREATE TABLE bookings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    walker_id INT,
    user_id INT,
    dog_name VARCHAR(100) NOT NULL,
    dog_size VARCHAR(20) NOT NULL,
    booking_date DATE NOT NULL,
    booking_time VARCHAR(20) NOT NULL,
    duration INT NOT NULL,
    phone VARCHAR(20) NOT NULL,
    email VARCHAR(255) NOT NULL,
    address TEXT NOT NULL,
    special_notes TEXT,
    total_price DECIMAL(10,2) NOT NULL,
    status ENUM('pending', 'confirmed', 'completed', 'cancelled') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (walker_id) REFERENCES walkers(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Create indexes for better performance
CREATE INDEX idx_walkers_rating ON walkers(rating);
CREATE INDEX idx_walkers_price ON walkers(price);
CREATE INDEX idx_bookings_walker_id ON bookings(walker_id);
CREATE INDEX idx_bookings_user_id ON bookings(user_id);
CREATE INDEX idx_bookings_status ON bookings(status);
CREATE INDEX idx_bookings_date ON bookings(booking_date);

-- Insert sample users
INSERT INTO users (first_name, last_name, email, phone, address) VALUES
('John', 'Doe', 'john.doe@example.com', '555-0123', '123 Main St, Downtown'),
('Jane', 'Smith', 'jane.smith@example.com', '555-0456', '456 Oak Ave, Midtown'),
('Mike', 'Johnson', 'mike.johnson@example.com', '555-0789', '789 Pine Rd, Uptown');

-- Insert sample walkers with JSON badges
INSERT INTO walkers (name, email, image, rating, review_count, distance, price, description, availability, badges, background_check, insured, certified) VALUES
('Sarah M.', 'sarah.m@dogwalkers.com', 'https://images.unsplash.com/photo-1544005313-94ddf0286df2?w=400&h=400&fit=crop&crop=face', 49, 127, '0.8 miles away', 25, 'Experienced dog walker with 3+ years of professional pet care. I love spending time with dogs of all sizes and ensuring they get the exercise and attention they deserve. Certified in pet first aid and CPR.', 'Mon-Fri: 7AM-7PM, Weekends: 8AM-6PM', '["Background Checked", "Insured", "5-Star Rated"]', true, true, true),
('Mike T.', 'mike.t@petcare.com', 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=400&h=400&fit=crop&crop=face', 48, 89, '1.2 miles away', 22, 'Reliable and trustworthy dog walker who treats every pet like family. I offer flexible scheduling and always provide updates with photos during walks. Great with both energetic and calm dogs.', 'Mon-Sun: 6AM-8PM', '["Certified", "Experienced"]', true, false, true),
('Emma K.', 'emma.k@vetwalker.com', 'https://images.unsplash.com/photo-1494790108755-2616b612b786?w=400&h=400&fit=crop&crop=face', 50, 203, '0.5 miles away', 30, 'Veterinary student with a passion for animal care. I provide specialized attention for dogs with special needs and senior pets. Currently pursuing certification in animal behavior.', 'Tue-Sat: 9AM-5PM', '["Vet Student", "Special Needs", "Top Rated"]', true, true, true),
('Alex R.', 'alex.r@activewalks.com', 'https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=400&h=400&fit=crop&crop=face', 47, 156, '1.5 miles away', 28, 'Fitness enthusiast who loves long walks and runs with high-energy dogs. Perfect for active breeds that need extra exercise. I also offer basic training reinforcement during walks.', 'Mon-Fri: 5AM-9AM, 5PM-8PM', '["Fitness Focused", "Certified", "High Energy"]', true, true, false),
('Lisa P.', 'lisa.p@dogtrainer.com', 'https://images.unsplash.com/photo-1438761681033-6461ffad8d80?w=400&h=400&fit=crop&crop=face', 50, 312, '0.3 miles away', 35, 'Professional dog trainer with 8+ years of experience. I specialize in behavioral training and can work with reactive or anxious dogs. Fully insured and bonded with excellent references.', 'Mon-Fri: 8AM-6PM', '["Dog Trainer", "Senior Care", "Behavioral Expert"]', true, true, true);

-- Insert sample bookings
INSERT INTO bookings (walker_id, user_id, dog_name, dog_size, booking_date, booking_time, duration, phone, email, address, special_notes, total_price, status) VALUES
(1, 1, 'Rocky', 'Medium', '2024-01-15', '2:00 PM', 45, '555-0123', 'john.doe@example.com', '123 Main St, Downtown', 'Rocky loves to play fetch and is very friendly with other dogs.', 28.75, 'confirmed'),
(2, 2, 'Bella', 'Small', '2024-01-16', '10:00 AM', 30, '555-0456', 'jane.smith@example.com', '456 Oak Ave, Midtown', 'Bella is a bit shy but warms up quickly. Please be gentle.', 12.65, 'pending'),
(3, 3, 'Max', 'Large', '2024-01-17', '4:00 PM', 60, '555-0789', 'mike.johnson@example.com', '789 Pine Rd, Uptown', 'Max has lots of energy and needs a good long walk. He pulls on the leash sometimes.', 51.75, 'confirmed');

-- Update walker ratings based on reviews (convert to decimal for display)
-- Note: Stored as integers (0-50) for rating * 10, displayed as decimals (0.0-5.0)