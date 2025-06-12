-- Create database
CREATE DATABASE IF NOT EXISTS dog_walker_app;
USE dog_walker_app;

-- Users table
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    first_name VARCHAR(50),
    last_name VARCHAR(50),
    phone VARCHAR(20),
    address TEXT,
    profile_image VARCHAR(255),
    emergency_contact VARCHAR(100),
    emergency_phone VARCHAR(20),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Walkers table
CREATE TABLE walkers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    image VARCHAR(255) NOT NULL,
    rating INT NOT NULL DEFAULT 0,
    review_count INT NOT NULL DEFAULT 0,
    distance VARCHAR(50) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    description TEXT NOT NULL,
    availability VARCHAR(100) NOT NULL,
    badges JSON,
    background_check BOOLEAN DEFAULT FALSE,
    insured BOOLEAN DEFAULT FALSE,
    certified BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Bookings table
CREATE TABLE bookings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    walker_id INT NOT NULL,
    dog_name VARCHAR(100) NOT NULL,
    dog_size VARCHAR(50) NOT NULL,
    date DATE NOT NULL,
    time VARCHAR(20) NOT NULL,
    duration INT NOT NULL,
    phone VARCHAR(20) NOT NULL,
    email VARCHAR(100) NOT NULL,
    instructions TEXT,
    service_fee INT NOT NULL,
    app_fee INT NOT NULL,
    total INT NOT NULL,
    status ENUM('pending', 'confirmed', 'completed', 'cancelled') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (walker_id) REFERENCES walkers(id)
);

-- Insert sample walkers
INSERT INTO walkers (name, image, rating, review_count, distance, price, description, availability, badges, background_check, insured, certified) VALUES
('Sarah M.', 'https://images.unsplash.com/photo-1494790108755-2616b612b47c?w=150&h=150&fit=crop&crop=face', 49, 127, '0.5 miles away', 25.00, 'Experienced dog walker with 5+ years caring for pets of all sizes. I love long walks and ensuring your furry friend gets the exercise they need!', 'Available today', '["Background Check", "Insured", "5-Star Rated"]', TRUE, TRUE, TRUE),

('Mike T.', 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=150&h=150&fit=crop&crop=face', 48, 89, '0.8 miles away', 22.00, 'Professional pet care specialist who treats every dog like family. Available for walks, feeding, and basic training.', 'Available tomorrow', '["Certified", "Experienced"]', TRUE, FALSE, TRUE),

('Emma K.', 'https://images.unsplash.com/photo-1438761681033-6461ffad8d80?w=150&h=150&fit=crop&crop=face', 50, 203, '1.2 miles away', 30.00, 'Veterinary student with a passion for animal care. Specializing in senior dogs and those with special needs.', 'Available this week', '["Vet Student", "Special Needs", "Top Rated"]', TRUE, TRUE, TRUE);

-- Insert sample user
INSERT INTO users (username, email, password, first_name, last_name, phone, address) VALUES
('john_doe', 'john.doe@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'John', 'Doe', '555-0123', '123 Main St, New York, NY 10001');

-- Insert sample booking
INSERT INTO bookings (walker_id, dog_name, dog_size, date, time, duration, phone, email, instructions, service_fee, app_fee, total, status) VALUES
(1, 'Rocky', 'Medium', '2024-06-15', '2:00 PM', 60, '555-0123', 'john.doe@example.com', 'Rocky loves to play fetch in the park!', 2500, 375, 2875, 'confirmed');