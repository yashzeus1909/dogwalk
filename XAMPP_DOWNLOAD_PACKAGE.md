# PawWalk - Complete XAMPP Download Package

## Download Instructions for XAMPP

Create a folder called `dogWalk` in your XAMPP htdocs directory and save all files with the exact structure shown below.

## Complete File Structure (22 Files)

```
C:\xampp\htdocs\dogWalk\
├── index.html                      # Main application interface
├── server.php                      # PHP development server router
├── .env.example                    # XAMPP MySQL configuration template
├── README.md                       # Complete setup documentation
├── XAMPP_SETUP_GUIDE.md           # Detailed XAMPP installation guide
├── js/
│   └── app.js                      # Complete jQuery application logic
├── api/
│   ├── walkers.php                 # Walker API with MySQL JSON support
│   ├── bookings.php                # Booking API with MySQL integration
│   ├── profile-bookings.php        # Profile-specific booking operations
│   └── users.php                   # User profile management API
├── config/
│   ├── database.php                # MySQL database configuration
│   └── env.php                     # Environment variable loader
├── models/
│   ├── Walker.php                  # Walker model with MySQL JSON handling
│   ├── Booking.php                 # Booking model with CRUD operations
│   └── User.php                    # User model with CRUD operations
├── database/
│   ├── schema.sql                  # Original PostgreSQL schema (reference)
│   ├── mysql_schema.sql            # MySQL schema
│   └── xampp_mysql_schema.sql      # XAMPP-specific MySQL schema
└── scripts/
    ├── setup_database.php          # PostgreSQL setup script (reference)
    ├── setup_mysql_database.php    # MySQL setup script
    └── setup_xampp_database.php    # XAMPP-specific setup script
```

## XAMPP Quick Setup (4 Steps)

1. **Install XAMPP**:
   - Download from https://www.apachefriends.org/
   - Install with default settings

2. **Setup Project Files**:
   ```bash
   # Create directory: C:\xampp\htdocs\dogWalk\
   # Extract all files to this directory
   cp .env.example .env
   ```

3. **Start XAMPP Services**:
   - Open XAMPP Control Panel
   - Click "Start" for Apache and MySQL

4. **Initialize Database**:
   ```bash
   cd C:\xampp\htdocs\dogWalk
   php scripts/setup_xampp_database.php
   ```

5. **Access Application**:
   - Open browser: http://localhost/dogWalk/

## XAMPP Configuration (.env file)

Pre-configured for XAMPP default settings:

```env
DATABASE_URL=mysql://root:@localhost/dogWalk
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=dogWalk
DB_USERNAME=root
DB_PASSWORD=
API_BASE_URL=http://localhost/dogWalk/api/
```

## XAMPP-Specific Features

### Database Configuration:
- **Database Name**: `dogWalk` (matches your preference)
- **Username**: `root` (XAMPP default)
- **Password**: Empty (XAMPP default)
- **Host**: `localhost`
- **Port**: `3306` (MySQL default)

### URL Structure:
- **Application**: http://localhost/dogWalk/
- **API Endpoints**: http://localhost/dogWalk/api/
- **phpMyAdmin**: http://localhost/phpmyadmin/

### File Locations:
- **Project Root**: `C:\xampp\htdocs\dogWalk\`
- **Apache Logs**: `C:\xampp\apache\logs\`
- **MySQL Data**: `C:\xampp\mysql\data\`

## Sample Data Included

### Dog Walkers (5 Professional Profiles):
- **Sarah M.** - 4.9★, $25/hour, Background checked, Insured, 5-Star Rated
- **Mike T.** - 4.8★, $22/hour, Certified, Experienced
- **Emma K.** - 5.0★, $30/hour, Vet Student, Special Needs, Top Rated
- **Alex R.** - 4.7★, $28/hour, Fitness Focused, Certified, High Energy
- **Lisa P.** - 5.0★, $35/hour, Dog Trainer, Senior Care, Behavioral Expert

### Sample Users (3 Profiles):
- **John Doe** - john.doe@example.com, 555-0123
- **Jane Smith** - jane.smith@example.com, 555-0456
- **Mike Johnson** - mike.johnson@example.com, 555-0789

### Sample Bookings (3 Records):
- Rocky (Medium) with Sarah M. - Confirmed - $28.75
- Bella (Small) with Mike T. - Pending - $12.65
- Max (Large) with Emma K. - Confirmed - $51.75

## XAMPP Troubleshooting

### Common Issues:

1. **Apache Won't Start**:
   - Port 80 conflict (change to 8080)
   - Access via: http://localhost:8080/dogWalk/

2. **MySQL Won't Start**:
   - Port 3306 conflict
   - Stop Windows MySQL service
   - Restart XAMPP MySQL

3. **Database Connection Failed**:
   - Verify MySQL is running in XAMPP Control Panel
   - Check green "Running" status for MySQL
   - Test connection via phpMyAdmin

4. **File Not Found Errors**:
   - Ensure files are in `C:\xampp\htdocs\dogWalk\`
   - Check file permissions
   - Verify Apache is running

### Testing Steps:

1. **Verify XAMPP Services**:
   - Apache: Green "Running" status
   - MySQL: Green "Running" status

2. **Test Database**:
   - Open phpMyAdmin: http://localhost/phpmyadmin/
   - Check `dogWalk` database exists
   - Verify tables: users, walkers, bookings

3. **Test Application**:
   - Homepage loads: http://localhost/dogWalk/
   - Walker profiles display with photos
   - Search functionality works
   - Booking modal opens

## Development Features

### XAMPP Advantages:
- **All-in-one package** (Apache, MySQL, PHP)
- **phpMyAdmin included** for database management
- **Easy service management** via Control Panel
- **No complex configuration** required
- **Perfect for local development**

### Application Features:
- **Mobile-responsive design** with jQuery
- **Real-time walker search** and filtering
- **Complete booking system** with form validation
- **User profile management**
- **MySQL database persistence**
- **Professional walker profiles** with authentic photos
- **JSON storage** for walker badges and certifications

## Production Considerations

When moving from XAMPP to production:

1. **Security Updates**:
   - Change MySQL root password
   - Remove phpMyAdmin access
   - Enable PHP error logging only

2. **Performance Optimization**:
   - Enable MySQL query cache
   - Configure Apache modules
   - Optimize database indexes
   - Enable gzip compression

3. **Environment Variables**:
   - Update .env for production database
   - Set proper API_BASE_URL
   - Configure email settings

This XAMPP package provides everything needed to run the PawWalk dog walker booking application in your local development environment with full MySQL database integration and authentic sample data.