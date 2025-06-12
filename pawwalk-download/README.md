# PawWalk - jQuery Dog Walker Booking App

A mobile-friendly dog walker booking platform built with HTML, CSS, jQuery, and PHP.

## Features

- Browse available dog walkers with ratings and reviews
- Search walkers by location and service type
- Book walking sessions with real-time pricing
- Manage bookings and view booking history
- User profile management with personal information
- Responsive design optimized for mobile devices
- RESTful PHP API backend with MySQL database

## Setup Instructions

### Prerequisites

- PHP 7.4 or higher
- MySQL 5.7 or higher
- Web server (Apache/Nginx) or PHP built-in server
- Modern web browser

### Installation

1. **Clone or download the project files**
   ```bash
   git clone <repository-url>
   cd dog-walker-jquery-app
   ```

2. **Database Setup**
   - Create a MySQL database named `dog_walker_app`
   - Import the database schema:
   ```bash
   mysql -u root -p dog_walker_app < database/schema.sql
   ```

3. **Configure Database Connection**
   - Edit `config/database.php`
   - Update the database credentials:
   ```php
   private $host = 'localhost';
   private $db_name = 'dog_walker_app';
   private $username = 'your_username';
   private $password = 'your_password';
   ```

4. **Start the Development Server**
   
   Using PHP built-in server:
   ```bash
   php -S localhost:8000
   ```
   
   Or configure your web server to serve the project directory.

5. **Access the Application**
   - Open your browser and navigate to `http://localhost:8000`
   - The app will load with sample data already populated

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