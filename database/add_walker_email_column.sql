-- Migration script to add email column to existing walkers table
-- Run this if you already have a walkers table without the email column

-- Add email column to walkers table
ALTER TABLE walkers 
ADD COLUMN email VARCHAR(255) NOT NULL DEFAULT '' 
AFTER name;

-- Add index for email lookups
CREATE INDEX idx_walkers_email ON walkers(email);

-- Update existing walkers with placeholder emails (you can update these manually)
UPDATE walkers SET email = CONCAT('walker', id, '@example.com') WHERE email = '';