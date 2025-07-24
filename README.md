# PawWalk - Dog Walker Booking Application

A professional dog walking service platform that connects dog owners with trusted, vetted dog walkers in their neighborhood.

## 🐕 Features

- **Browse Professional Walkers**: View profiles, ratings, and availability of certified dog walkers
- **Easy Booking System**: Simple booking process with instant confirmations
- **Real-time Updates**: Live availability and booking status updates
- **Secure Authentication**: User registration and login system
- **Responsive Design**: Works perfectly on desktop and mobile devices
- **Database Persistence**: All data stored securely in SQLite database

## 🚀 Quick Start

### Prerequisites
- PHP 8.2 or higher
- SQLite3 support (included in most PHP installations)

### Installation

1. **Clone the repository**
   ```bash
   git clone https://github.com/yourusername/pawwalk-dog-walker.git
   cd pawwalk-dog-walker
   ```

2. **Set up the database**
   ```bash
   php setup_database.php
   ```

3. **Start the application**
   ```bash
   php -S localhost:5000 server.php
   ```

4. **Open your browser**
   Navigate to `http://localhost:5000`

## 🏗️ Project Structure

```
pawwalk-dog-walker/
├── index.html              # Main application interface
├── server.php              # PHP server router
├── setup_database.php      # Database initialization
├── api/                    # REST API endpoints
│   ├── walkers.php         # Walker management
│   ├── bookings.php        # Booking operations
│   ├── config.php          # API configuration
│   └── ...
├── config/
│   └── database.php        # Database connection
├── database/
│   ├── sqlite_schema.sql   # Database schema
│   └── dog_walker_app.db   # SQLite database file
├── css/
│   └── style.css           # Application styles
├── js/
│   └── app.js              # Frontend JavaScript
└── README.md
```

## 💾 Database Schema

The application uses a simple SQLite database with two main tables:

### Users Table
- Unified table for customers and walkers
- Walker-specific fields: rating, price, services, badges
- Customer fields: basic profile information

### Bookings Table
- Appointment records with full booking details
- Links walkers and customers via foreign keys
- Tracks booking status and payment information

## 🔧 API Endpoints

| Endpoint | Method | Description |
|----------|--------|-------------|
| `/api/walkers.php` | GET | Fetch all available walkers |
| `/api/bookings.php` | GET/POST | Manage bookings |
| `/api/login.php` | POST | User authentication |
| `/api/register.php` | POST | User registration |

## 🛠️ Technology Stack

- **Frontend**: HTML5, CSS3, JavaScript (jQuery)
- **Backend**: PHP 8.2+
- **Database**: SQLite3
- **Server**: Built-in PHP development server
- **Architecture**: Traditional LAMP-style stack

## 🎨 Features in Detail

### Walker Profiles
- Professional photos and detailed descriptions
- Rating system with review counts
- Service badges (Certified, Insured, Pet First Aid)
- Hourly pricing and availability schedules

### Booking System
- Select dog size and walk duration
- Special instructions and contact information
- Real-time price calculations
- Booking confirmation system

### Responsive Design
- Mobile-first CSS design
- Flexible grid layouts
- Touch-friendly interfaces
- Cross-browser compatibility

## 🚀 Deployment

### Local Development
```bash
php -S localhost:5000 server.php
```

### Production Deployment
1. Upload files to web server
2. Ensure PHP 8.2+ with SQLite support
3. Run database setup: `php setup_database.php`
4. Configure web server to serve from project root

## 📝 Sample Data

The application comes with sample data including:
- 3 professional dog walkers with real profiles
- 2 sample customers
- 3 sample bookings with different statuses

## 🔒 Security Features

- Password hashing with PHP's `password_hash()`
- SQL injection prevention with PDO prepared statements
- Input validation and sanitization
- Session-based authentication
- CORS headers for API security

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

## 📄 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## 🐾 About PawWalk

PawWalk was created to make it easy for dog owners to find reliable, professional dog walking services. Our platform ensures all walkers are background-checked and insured, giving pet owners peace of mind.

## 📞 Support

For support, email support@pawwalk.com or create an issue in this repository.

---

Made with ❤️ for dogs and their humans