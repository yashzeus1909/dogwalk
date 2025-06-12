# PawWalk - Dog Walker Booking Platform

A complete mobile-friendly dog walker booking platform built with HTML, CSS, jQuery, and PHP with PostgreSQL database integration.

## Features

- Browse available dog walkers with ratings and reviews
- Search walkers by location and service type
- Book walking sessions with real-time pricing calculations
- Manage bookings and view booking history
- User profile management with personal information
- Responsive design optimized for mobile devices
- RESTful PHP API backend with PostgreSQL database
- Complete database schema with sample data
- User authentication and session management

## Technology Stack

- **Frontend**: HTML5, CSS3 (Tailwind CSS), jQuery 3.6.0
- **Backend**: PHP 7.4+, PostgreSQL 13+
- **Database**: PostgreSQL with prepared statements and transactions
- **Icons**: Font Awesome 6.0
- **Images**: Unsplash API for walker photos
- **Architecture**: RESTful API with MVC pattern

## Prerequisites

- PHP 7.4 or higher with PDO PostgreSQL extension
- PostgreSQL 13 or higher
- Web server (Apache/Nginx) or PHP built-in server
- Modern web browser

## Quick Setup

### Option 1: Using Existing PostgreSQL Database

1. **Download and extract all project files**
2. **Configure database connection** by setting environment variables:
   ```bash
   export DATABASE_URL="postgresql://username:password@host:port/database_name"
   # OR set individual variables:
   export PGHOST="localhost"
   export PGPORT="5432"
   export PGDATABASE="pawwalk_db"
   export PGUSER="your_username"
   export PGPASSWORD="your_password"
   ```
3. **Initialize the database**:
   ```bash
   psql -h localhost -U your_username -d your_database -f database/schema.sql
   ```
4. **Start the server**:
   ```bash
   php -S localhost:8000 server.php
   ```
5. **Open browser**: Navigate to `http://localhost:8000`

### Option 2: Fresh PostgreSQL Installation

1. **Install PostgreSQL**:
   ```bash
   # Ubuntu/Debian
   sudo apt update && sudo apt install postgresql postgresql-contrib php-pgsql
   
   # macOS with Homebrew
   brew install postgresql php
   
   # Windows: Download from https://www.postgresql.org/download/
   ```

2. **Create database and user**:
   ```bash
   sudo -u postgres psql
   CREATE DATABASE pawwalk_db;
   CREATE USER pawwalk_user WITH PASSWORD 'secure_password';
   GRANT ALL PRIVILEGES ON DATABASE pawwalk_db TO pawwalk_user;
   \q
   ```

3. **Set environment variables**:
   ```bash
   export DATABASE_URL="postgresql://pawwalk_user:secure_password@localhost:5432/pawwalk_db"
   ```

4. **Initialize database with sample data**:
   ```bash
   psql -h localhost -U pawwalk_user -d pawwalk_db -f database/schema.sql
   ```

5. **Start the application**:
   ```bash
   php -S localhost:8000 server.php
   ```

## Project Structure

```
├── index.html              # Main application file
├── js/
│   └── app.js              # jQuery application logic
├── api/
│   ├── walkers.php         # Walker API endpoints
│   └── bookings.php        # Booking API endpoints
├── models/
│   ├── Walker.php          # Walker data model
│   └── Booking.php         # Booking data model
├── config/
│   └── database.php        # Database configuration
├── database/
│   └── schema.sql          # Database schema and sample data
└── README.md               # This file
```

## API Endpoints

### Walkers
- `GET /api/walkers.php` - Get all walkers
- `GET /api/walkers.php?search=1&location=<location>` - Search walkers
- `POST /api/walkers.php` - Create new walker

### Bookings
- `GET /api/bookings.php` - Get all bookings
- `GET /api/bookings.php?user_email=<email>` - Get user bookings
- `POST /api/bookings.php` - Create new booking
- `PUT /api/bookings.php` - Update booking status

## Technologies Used

- **Frontend**: HTML5, CSS3 (Tailwind CSS), jQuery 3.6.0
- **Backend**: PHP 7.4+, MySQL
- **Icons**: Font Awesome 6.0
- **Images**: Unsplash API for walker photos
- **Architecture**: RESTful API with MVC pattern

## Features in Detail

### Walker Listings
- Display walker profiles with photos, ratings, and descriptions
- Show distance, pricing, and availability
- Badge system for certifications and background checks
- Responsive grid layout for different screen sizes

### Booking System
- Multi-step booking form with validation
- Real-time pricing calculation
- Date/time selection with constraints
- Special instructions and contact information
- Email confirmation system

### User Profile
- Personal information management
- Booking history with status tracking
- Settings and preferences
- Emergency contact information

### Mobile Experience
- Bottom navigation for easy mobile access
- Touch-friendly interface elements
- Responsive design adapts to all screen sizes
- Fast loading with optimized images

## Sample Data

The application comes with pre-populated sample data:

- 3 dog walkers with different specialties
- 1 sample booking for demonstration
- 1 user profile for testing

## Development Notes

- All API responses are in JSON format
- CORS headers are included for cross-origin requests
- Input sanitization and validation on both client and server
- Prepared statements used to prevent SQL injection
- Error handling with user-friendly messages

## Browser Support

- Chrome 80+
- Firefox 75+
- Safari 13+
- Edge 80+
- Mobile browsers (iOS Safari, Chrome Mobile)

## Security Features

- SQL injection protection with prepared statements
- Input sanitization and validation
- CORS configuration for API security
- Password hashing for user accounts

For production deployment, additional security measures should be implemented including HTTPS, input rate limiting, and authentication tokens.