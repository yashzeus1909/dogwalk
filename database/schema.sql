-- PawWalk Dog Walker Database Schema
-- PostgreSQL version

-- Drop tables if they exist (for clean setup)
DROP TABLE IF EXISTS bookings CASCADE;
DROP TABLE IF EXISTS walkers CASCADE;
DROP TABLE IF EXISTS users CASCADE;

-- Users table
CREATE TABLE users (
    id SERIAL PRIMARY KEY,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    phone VARCHAR(20),
    address TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Walkers table
CREATE TABLE walkers (
    id SERIAL PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    image VARCHAR(500),
    rating INTEGER DEFAULT 0 CHECK (rating >= 0 AND rating <= 50),
    review_count INTEGER DEFAULT 0,
    distance VARCHAR(50),
    price INTEGER NOT NULL,
    description TEXT,
    availability VARCHAR(100),
    badges TEXT[], -- PostgreSQL array type
    background_check BOOLEAN DEFAULT false,
    insured BOOLEAN DEFAULT false,
    certified BOOLEAN DEFAULT false,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Bookings table
CREATE TABLE bookings (
    id SERIAL PRIMARY KEY,
    walker_id INTEGER REFERENCES walkers(id) ON DELETE CASCADE,
    user_id INTEGER REFERENCES users(id) ON DELETE CASCADE,
    dog_name VARCHAR(100) NOT NULL,
    dog_size VARCHAR(20) NOT NULL,
    booking_date DATE NOT NULL,
    booking_time VARCHAR(20) NOT NULL,
    duration INTEGER NOT NULL,
    phone VARCHAR(20) NOT NULL,
    email VARCHAR(255) NOT NULL,
    instructions TEXT,
    service_fee INTEGER NOT NULL,
    app_fee INTEGER NOT NULL,
    total INTEGER NOT NULL,
    status VARCHAR(20) DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert sample data
INSERT INTO users (first_name, last_name, email, phone, address) VALUES
('John', 'Doe', 'john.doe@example.com', '555-0123', '123 Main St, New York, NY 10001'),
('Jane', 'Smith', 'jane.smith@example.com', '555-0456', '456 Oak Ave, Brooklyn, NY 11201'),
('Mike', 'Johnson', 'mike.johnson@example.com', '555-0789', '789 Pine St, Queens, NY 11375');

INSERT INTO walkers (name, image, rating, review_count, distance, price, description, availability, badges, background_check, insured, certified) VALUES
('Sarah M.', 'https://images.unsplash.com/photo-1494790108755-2616b612b47c?w=150&h=150&fit=crop&crop=face', 49, 127, '0.5 miles away', 25, 'Experienced dog walker with 5+ years caring for pets of all sizes. I love long walks and ensuring your furry friend gets the exercise they need!', 'Available today', ARRAY['Background Check', 'Insured', '5-Star Rated'], true, true, true),
('Mike T.', 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=150&h=150&fit=crop&crop=face', 48, 89, '0.8 miles away', 22, 'Professional pet care specialist who treats every dog like family. Available for walks, feeding, and basic training.', 'Available tomorrow', ARRAY['Certified', 'Experienced'], true, false, true),
('Emma K.', 'https://images.unsplash.com/photo-1438761681033-6461ffad8d80?w=150&h=150&fit=crop&crop=face', 50, 203, '1.2 miles away', 30, 'Veterinary student with a passion for animal care. Specializing in senior dogs and those with special needs.', 'Available this week', ARRAY['Vet Student', 'Special Needs', 'Top Rated'], true, true, true),
('Alex R.', 'https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=150&h=150&fit=crop&crop=face', 47, 156, '1.5 miles away', 28, 'Passionate about dogs and fitness! I provide energetic walks and playtime for active dogs. Former personal trainer with pet care certification.', 'Available weekends', ARRAY['Fitness Focused', 'Certified', 'High Energy'], true, true, true),
('Lisa P.', 'https://images.unsplash.com/photo-1544005313-94ddf0286df2?w=150&h=150&fit=crop&crop=face', 50, 298, '0.3 miles away', 35, 'Professional dog trainer and walker with 8+ years experience. Specialized in behavioral training and senior dog care.', 'Available daily', ARRAY['Dog Trainer', 'Senior Care', 'Behavioral Expert'], true, true, true);

INSERT INTO bookings (walker_id, user_id, dog_name, dog_size, booking_date, booking_time, duration, phone, email, instructions, service_fee, app_fee, total, status) VALUES
(1, 1, 'Rocky', 'Medium', '2024-06-15', '2:00 PM', 60, '555-0123', 'john.doe@example.com', 'Rocky loves to play fetch in the park!', 2500, 375, 2875, 'confirmed'),
(2, 1, 'Bella', 'Small', '2024-06-20', '10:00 AM', 30, '555-0123', 'john.doe@example.com', 'Bella is very friendly but gets tired easily.', 1100, 165, 1265, 'pending'),
(3, 2, 'Max', 'Large', '2024-06-18', '4:00 PM', 90, '555-0456', 'jane.smith@example.com', 'Max needs lots of exercise and loves running.', 4500, 675, 5175, 'confirmed');

-- Create indexes for better performance
CREATE INDEX idx_bookings_walker_id ON bookings(walker_id);
CREATE INDEX idx_bookings_user_id ON bookings(user_id);
CREATE INDEX idx_bookings_email ON bookings(email);
CREATE INDEX idx_bookings_date ON bookings(booking_date);
CREATE INDEX idx_walkers_price ON walkers(price);
CREATE INDEX idx_users_email ON users(email);

-- Create updated_at trigger function
CREATE OR REPLACE FUNCTION update_updated_at_column()
RETURNS TRIGGER AS $$
BEGIN
    NEW.updated_at = CURRENT_TIMESTAMP;
    RETURN NEW;
END;
$$ language 'plpgsql';

-- Create triggers for automatic updated_at updates
CREATE TRIGGER update_users_updated_at BEFORE UPDATE ON users FOR EACH ROW EXECUTE FUNCTION update_updated_at_column();
CREATE TRIGGER update_walkers_updated_at BEFORE UPDATE ON walkers FOR EACH ROW EXECUTE FUNCTION update_updated_at_column();
CREATE TRIGGER update_bookings_updated_at BEFORE UPDATE ON bookings FOR EACH ROW EXECUTE FUNCTION update_updated_at_column();