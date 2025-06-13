-- Migration script to add email column to existing bookings table
-- Run this if you already have a bookings table without the email column

-- Add email column to bookings table
ALTER TABLE bookings 
ADD COLUMN email VARCHAR(255) NOT NULL DEFAULT '' 
AFTER phone;

-- Add index for email lookups
CREATE INDEX idx_bookings_email ON bookings(email);

-- Update existing bookings with placeholder emails (you can update these manually)
UPDATE bookings SET email = CONCAT('user', id, '@example.com') WHERE email = '';