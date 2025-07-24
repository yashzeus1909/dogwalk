-- Dog Walker App MySQL Database Schema

CREATE DATABASE IF NOT EXISTS dog_walker_app;
USE dog_walker_app;

-- Users table (stores both walkers and customers with role differentiation)
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(20),
    address TEXT,
    role ENUM('customer', 'walker') NOT NULL DEFAULT 'customer',
    
    -- Walker specific fields (only used when role = 'walker')
    image VARCHAR(500),
    rating DECIMAL(2,1) DEFAULT 0.0,
    review_count INT DEFAULT 0,
    distance VARCHAR(50),
    price INT DEFAULT 0,
    description TEXT,
    availability VARCHAR(100),
    badges JSON,
    services JSON,
    background_check BOOLEAN DEFAULT FALSE,
    insured BOOLEAN DEFAULT FALSE,
    certified BOOLEAN DEFAULT FALSE,
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Bookings table
CREATE TABLE bookings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    walker_id INT NOT NULL,
    customer_id INT NOT NULL,
    dog_name VARCHAR(100) NOT NULL,
    dog_size ENUM('Small', 'Medium', 'Large', 'Extra Large') NOT NULL,
    booking_date DATE NOT NULL,
    booking_time TIME NOT NULL,
    duration INT NOT NULL, -- in minutes
    phone VARCHAR(20) NOT NULL,
    address TEXT NOT NULL,
    special_notes TEXT,
    total_price DECIMAL(10,2) NOT NULL,
    status ENUM('pending', 'confirmed', 'completed', 'cancelled') NOT NULL DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (walker_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (customer_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Insert sample walker data
INSERT INTO users (first_name, last_name, email, password, phone, role, image, rating, review_count, distance, price, description, availability, badges, services, background_check, insured, certified) VALUES
('Sarah', 'Johnson', 'sarah@walkers.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '+1-555-0101', 'walker', 'https://images.unsplash.com/photo-1494790108755-2616b332c371?w=400&h=400&fit=crop&crop=face', 4.8, 127, '0.5 miles', 25, 'Experienced professional dog walker with 5+ years caring for dogs of all sizes. Specializes in pet sitting and overnight care.', 'Mon-Fri 9am-5pm', JSON_ARRAY('Verified', 'Insured', 'Pet Sitting', 'Pet Boarding'), JSON_ARRAY('Dog Walking', 'Pet Sitting'), TRUE, TRUE, TRUE),

('Mike', 'Chen', 'mike@walkers.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '+1-555-0102', 'walker', 'https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=400&h=400&fit=crop&crop=face', 4.6, 89, '1.2 miles', 22, 'Part-time dog walker specializing in small breeds and puppies. Available for walking services.', 'Weekends and evenings', JSON_ARRAY('Verified', 'Walking'), JSON_ARRAY('Dog Walking'), FALSE, FALSE, TRUE),

('Emma', 'Rodriguez', 'emma@walkers.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '+1-555-0103', 'walker', 'https://images.unsplash.com/photo-1438761681033-6461ffad8d80?w=400&h=400&fit=crop&crop=face', 4.9, 203, '0.8 miles', 30, 'Professional pet care specialist with behavioral training expertise. Offers grooming and overnight care services.', 'Daily 6am-8pm', JSON_ARRAY('Verified', 'Insured', 'Background Checked', 'Grooming', 'Doggy Daycare'), JSON_ARRAY('Dog Walking', 'Pet Sitting', 'Grooming'), TRUE, TRUE, TRUE);

-- Insert sample customer data
INSERT INTO users (first_name, last_name, email, password, phone, role, address) VALUES
('John', 'Doe', 'john@customer.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '+1-555-0201', 'customer', '123 Main St, City, State 12345'),
('Jane', 'Smith', 'jane@customer.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '+1-555-0202', 'customer', '456 Oak Ave, City, State 12345');