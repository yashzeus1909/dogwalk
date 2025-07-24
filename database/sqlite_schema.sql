-- Dog Walker Application SQLite Schema

-- Create users table (unified for customers and walkers)
CREATE TABLE IF NOT EXISTS users (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(20),
    address TEXT,
    role TEXT DEFAULT 'customer' CHECK (role IN ('customer', 'walker', 'admin')),
    is_active BOOLEAN DEFAULT 1,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    
    -- Walker-specific fields
    profile_image_url TEXT,
    rating DECIMAL(3,2) DEFAULT 0.00,
    review_count INTEGER DEFAULT 0,
    distance VARCHAR(20),
    price_per_hour DECIMAL(8,2),
    description TEXT,
    availability TEXT,
    badges TEXT, -- JSON string of badges
    services TEXT, -- JSON string of services
    background_check BOOLEAN DEFAULT 0,
    insured BOOLEAN DEFAULT 0,
    certified BOOLEAN DEFAULT 0
);

-- Create bookings table
CREATE TABLE IF NOT EXISTS bookings (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    walker_id INTEGER,
    customer_id INTEGER,
    dog_name VARCHAR(100) NOT NULL,
    dog_size TEXT CHECK (dog_size IN ('Small', 'Medium', 'Large')) NOT NULL,
    booking_date DATE NOT NULL,
    booking_time TIME NOT NULL,
    duration INTEGER NOT NULL, -- in minutes
    phone VARCHAR(20),
    address TEXT NOT NULL,
    special_notes TEXT,
    total_price DECIMAL(8,2) NOT NULL,
    status TEXT DEFAULT 'pending' CHECK (status IN ('pending', 'confirmed', 'completed', 'cancelled')),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (walker_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (customer_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Create indexes for better performance
CREATE INDEX IF NOT EXISTS idx_users_email ON users(email);
CREATE INDEX IF NOT EXISTS idx_users_role ON users(role);
CREATE INDEX IF NOT EXISTS idx_users_rating ON users(rating);
CREATE INDEX IF NOT EXISTS idx_bookings_walker_id ON bookings(walker_id);
CREATE INDEX IF NOT EXISTS idx_bookings_customer_id ON bookings(customer_id);
CREATE INDEX IF NOT EXISTS idx_bookings_date ON bookings(booking_date);
CREATE INDEX IF NOT EXISTS idx_bookings_status ON bookings(status);

-- Insert sample walkers
INSERT OR IGNORE INTO users (first_name, last_name, email, password, phone, address, role, profile_image_url, rating, review_count, distance, price_per_hour, description, availability, badges, services, background_check, insured, certified) VALUES
('Sarah', 'Johnson', 'sarah.johnson@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '(555) 123-4567', '123 Main St, City', 'walker', 'https://randomuser.me/api/portraits/women/1.jpg', 4.9, 127, '0.8 miles', 25.00, 'Experienced dog walker with 5+ years caring for dogs of all sizes. I love taking dogs on adventures and ensuring they get the exercise they need!', 'Available Mon-Fri 9AM-5PM', '["Certified", "Insured", "Pet First Aid"]', '["Dog Walking", "Pet Sitting"]', 1, 1, 1),
('Mike', 'Chen', 'mike.chen@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '(555) 234-5678', '456 Oak Ave, City', 'walker', 'https://randomuser.me/api/portraits/men/2.jpg', 4.8, 89, '1.2 miles', 22.00, 'Professional pet sitter and dog walker. Specializing in senior dogs and puppies. Background checked and fully insured.', 'Available 7 days a week', '["Background Checked", "Insured"]', '["Dog Walking", "Pet Sitting", "Grooming"]', 1, 1, 0),
('Emma', 'Davis', 'emma.davis@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '(555) 345-6789', '789 Pine St, City', 'walker', 'https://randomuser.me/api/portraits/women/3.jpg', 4.7, 156, '0.5 miles', 28.00, 'Passionate about dogs! I offer personalized walks and plenty of attention for your furry friends. Great with anxious or reactive dogs.', 'Available weekdays 8AM-6PM', '["Pet First Aid", "Certified"]', '["Dog Walking", "Pet Training"]', 0, 1, 1);

-- Insert sample customers
INSERT OR IGNORE INTO users (first_name, last_name, email, password, phone, address, role) VALUES
('John', 'Doe', 'john.doe@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '(555) 456-7890', '321 Elm St, City', 'customer'),
('Jane', 'Smith', 'jane.smith@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '(555) 567-8901', '654 Maple Ave, City', 'customer');

-- Insert sample bookings
INSERT OR IGNORE INTO bookings (walker_id, customer_id, dog_name, dog_size, booking_date, booking_time, duration, phone, address, special_notes, total_price, status) VALUES
(1, 4, 'Buddy', 'Medium', '2025-01-25', '10:00:00', 60, '(555) 456-7890', '321 Elm St, City', 'Buddy loves to play fetch and needs lots of exercise', 25.00, 'confirmed'),
(2, 5, 'Luna', 'Small', '2025-01-26', '14:00:00', 30, '(555) 567-8901', '654 Maple Ave, City', 'Luna is very friendly but pulls on the leash', 11.00, 'pending'),
(3, 4, 'Max', 'Large', '2025-01-27', '16:00:00', 90, '(555) 456-7890', '321 Elm St, City', 'Max is very energetic and loves long walks', 42.00, 'confirmed');