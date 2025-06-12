# PawWalk jQuery Dog Walker App - Download Package

## Files Included

### Core Application Files
- `index.html` - Main application interface
- `js/app.js` - Complete jQuery application logic
- `api/walkers.php` - Walker API endpoints
- `api/bookings.php` - Booking API endpoints
- `server.php` - PHP development server router
- `test-api.php` - API testing script
- `README.md` - Complete setup instructions

## Quick Start

1. **Download all files** to your local directory
2. **Start PHP server**: `php -S localhost:8000 server.php`
3. **Open browser**: Navigate to `http://localhost:8000`

## Features Working Out of the Box

- Browse 3 sample dog walkers with photos and ratings
- Book walking sessions with real-time pricing
- View booking history and manage profile
- Search walkers by location
- Mobile-responsive design
- Session-based data persistence

## File Structure After Download

```
pawwalk-app/
├── index.html          # Main app
├── js/
│   └── app.js         # jQuery logic
├── api/
│   ├── walkers.php    # Walker API
│   └── bookings.php   # Booking API
├── server.php         # PHP router
├── test-api.php       # API tester
└── README.md          # Full documentation
```

## Technologies Used

- HTML5 + CSS3 (Tailwind CSS via CDN)
- jQuery 3.6.0
- PHP 7.4+ (no database required)
- Font Awesome icons
- Unsplash images

The application uses session storage and mock data, so no database setup is required for immediate testing.