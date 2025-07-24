-- PawWalk MySQL Database Setup
CREATE DATABASE IF NOT EXISTS dog_walker_app;
USE dog_walker_app;

-- Create users table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    phone VARCHAR(20),
    address TEXT,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Create walkers table
CREATE TABLE IF NOT EXISTS walkers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    phone VARCHAR(20),
    location VARCHAR(255),
    bio TEXT,
    price_per_hour DECIMAL(10, 2),
    rating DECIMAL(3, 2) DEFAULT 0,
    profile_image VARCHAR(255),
    services TEXT,
    availability TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Create bookings table
CREATE TABLE IF NOT EXISTS bookings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    walker_id INT,
    service_type VARCHAR(100),
    date DATE,
    start_time TIME,
    end_time TIME,
    duration INT,
    price DECIMAL(10, 2),
    location TEXT,
    notes TEXT,
    status VARCHAR(50) DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (walker_id) REFERENCES walkers(id) ON DELETE CASCADE
);

-- Insert sample walkers
INSERT INTO walkers (name, email, phone, location, bio, price_per_hour, rating, profile_image, services, availability) VALUES
('Sarah Johnson', 'sarah.johnson@email.com', '555-0101', 'Downtown', 'Professional dog walker with 5+ years experience', 25.00, 4.8, 'https://images.unsplash.com/photo-1494790108755-2616c24c7b95?w=300', 'Dog Walking, Pet Sitting, Overnight Care', 'Monday-Friday: 8AM-6PM, Weekend: 9AM-4PM'),
('Mike Chen', 'mike.chen@email.com', '555-0102', 'Midtown', 'Certified animal care specialist', 30.00, 4.9, 'https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=300', 'Dog Walking, Training, Grooming', 'Monday-Sunday: 7AM-7PM'),
('Emma Williams', 'emma.williams@email.com', '555-0103', 'Uptown', 'Loving pet care provider', 20.00, 4.7, 'https://images.unsplash.com/photo-1438761681033-6461ffad8d80?w=300', 'Dog Walking, Pet Sitting', 'Tuesday-Saturday: 9AM-5PM');

-- Insert sample users
INSERT INTO users (first_name, last_name, email, phone, address, password) VALUES
('John', 'Doe', 'john.doe@example.com', '555-1234', '123 Main St', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
('Jane', 'Smith', 'jane.smith@example.com', '555-5678', '456 Oak Ave', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');

SELECT 'Database setup complete!' as message;