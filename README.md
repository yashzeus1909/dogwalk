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

### Option 1: Using Existing MySQL Database

1. **Download and extract all project files**
2. **Configure environment**:
   ```bash
   cp .env.example .env
   # Edit .env file with your database credentials
   ```
   Update `.env` with your MySQL connection details:
   ```
   DATABASE_URL=mysql://your_user:your_password@localhost:3306/your_database
   ```
3. **Initialize the database**:
   ```bash
   php scripts/setup_mysql_database.php
   ```
4. **Start the server**:
   ```bash
   php -S localhost:8000 server.php
   ```
5. **Open browser**: Navigate to `http://localhost:8000`

### Option 2: Fresh MySQL Installation

1. **Install MySQL**:
   ```bash
   # Ubuntu/Debian
   sudo apt update && sudo apt install mysql-server php-mysql
   sudo mysql_secure_installation
   
   # macOS with Homebrew
   brew install mysql php
   brew services start mysql
   
   # Windows: Download from https://dev.mysql.com/downloads/mysql/
   ```

2. **Create database and user**:
   ```bash
   sudo mysql
   CREATE DATABASE pawwalk_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   CREATE USER 'pawwalk_user'@'localhost' IDENTIFIED BY 'secure_password';
   GRANT ALL PRIVILEGES ON pawwalk_db.* TO 'pawwalk_user'@'localhost';
   FLUSH PRIVILEGES;
   EXIT;
   ```

3. **Configure environment**:
   ```bash
   cp .env.example .env
   ```
   Edit `.env` and update the DATABASE_URL:
   ```
   DATABASE_URL=mysql://pawwalk_user:secure_password@localhost:3306/pawwalk_db
   ```

4. **Initialize database with sample data**:
   ```bash
   php scripts/setup_mysql_database.php
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