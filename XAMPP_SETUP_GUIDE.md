# PawWalk - XAMPP Installation Guide

## Complete XAMPP Setup Instructions

### Step 1: Install XAMPP

1. **Download XAMPP**:
   - Visit https://www.apachefriends.org/download.html
   - Download the latest version for your operating system
   - Choose the version with PHP 8.0 or higher

2. **Install XAMPP**:
   - **Windows**: Run the installer as Administrator
   - **macOS**: Mount the DMG and drag XAMPP to Applications
   - **Linux**: Make the installer executable and run it

3. **Default Installation Path**:
   - **Windows**: `C:\xampp\`
   - **macOS**: `/Applications/XAMPP/`
   - **Linux**: `/opt/lampp/`

### Step 2: Setup Project Files

1. **Extract Project Files**:
   - Create folder: `C:\xampp\htdocs\dogWalk\` (Windows)
   - Or: `/Applications/XAMPP/htdocs/dogWalk/` (macOS)
   - Copy all project files to this directory

2. **File Structure Should Look Like**:
   ```
   C:\xampp\htdocs\dogWalk\
   ├── index.html
   ├── .env.example
   ├── server.php
   ├── api/
   ├── config/
   ├── models/
   ├── database/
   ├── scripts/
   └── js/
   ```

### Step 3: Configure Environment

1. **Copy Environment File**:
   ```bash
   # In your dogWalk folder
   copy .env.example .env
   ```

2. **Your .env file should contain**:
   ```env
   DATABASE_URL=mysql://root:@localhost/dogWalk
   DB_HOST=localhost
   DB_PORT=3306
   DB_DATABASE=dogWalk
   DB_USERNAME=root
   DB_PASSWORD=
   API_BASE_URL=http://localhost/dogWalk/api/
   ```

### Step 4: Start XAMPP Services

1. **Open XAMPP Control Panel**:
   - **Windows**: Start Menu → XAMPP → XAMPP Control Panel
   - **macOS**: Applications → XAMPP → XAMPP Control Panel
   - **Linux**: sudo /opt/lampp/manager-linux-x64.run

2. **Start Required Services**:
   - Click **Start** next to **Apache**
   - Click **Start** next to **MySQL**
   - Both should show green "Running" status

3. **Verify Services**:
   - Apache should run on port 80
   - MySQL should run on port 3306
   - If ports are occupied, change them in XAMPP config

### Step 5: Create Database

1. **Option A: Using Setup Script (Recommended)**:
   ```bash
   # Navigate to your project folder
   cd C:\xampp\htdocs\dogWalk
   
   # Run the setup script
   php scripts/setup_xampp_database.php
   ```

2. **Option B: Using phpMyAdmin**:
   - Open browser: http://localhost/phpmyadmin
   - Click "New" to create database
   - Name: `dogWalk`
   - Collation: `utf8mb4_unicode_ci`
   - Click "Create"
   - Import: `database/xampp_mysql_schema.sql`

### Step 6: Test Installation

1. **Open Application**:
   - Browser: http://localhost/dogWalk/
   - You should see the PawWalk homepage

2. **Test Database Connection**:
   - Check if walkers load on homepage
   - Try searching for walkers
   - Test booking functionality

### Step 7: Troubleshooting

#### Common Issues:

1. **Apache Won't Start**:
   - Port 80 conflict (Skype, IIS)
   - Change Apache port to 8080 in XAMPP config
   - Access via: http://localhost:8080/dogWalk/

2. **MySQL Won't Start**:
   - Port 3306 conflict
   - Stop MySQL Windows service
   - Restart XAMPP MySQL

3. **Database Connection Failed**:
   - Verify MySQL is running in XAMPP
   - Check .env database credentials
   - Ensure database `dogWalk` exists

4. **File Permissions (Linux/macOS)**:
   ```bash
   sudo chmod -R 755 /opt/lampp/htdocs/dogWalk/
   sudo chown -R daemon:daemon /opt/lampp/htdocs/dogWalk/
   ```

5. **API Not Working**:
   - Check .htaccess file in api folder
   - Verify Apache mod_rewrite is enabled
   - Test direct API access: http://localhost/dogWalk/api/walkers.php

#### Database Verification:

```sql
-- Test in phpMyAdmin SQL tab
USE dogWalk;
SELECT COUNT(*) FROM users;     -- Should return 3
SELECT COUNT(*) FROM walkers;   -- Should return 5
SELECT COUNT(*) FROM bookings;  -- Should return 3
```

### Step 8: Development Tips

1. **Error Logging**:
   - Enable PHP error reporting in development
   - Check Apache error logs: `C:\xampp\apache\logs\error.log`

2. **Database Management**:
   - Use phpMyAdmin: http://localhost/phpmyadmin
   - Default login: root (no password)

3. **File Editing**:
   - Use any text editor
   - Recommended: VS Code, Sublime Text, or Notepad++

### Step 9: Production Considerations

1. **Security**:
   - Change MySQL root password
   - Disable phpMyAdmin in production
   - Use environment variables for sensitive data

2. **Performance**:
   - Enable MySQL query cache
   - Configure Apache modules
   - Optimize database indexes

## Quick Reference

### XAMPP URLs:
- **Application**: http://localhost/dogWalk/
- **phpMyAdmin**: http://localhost/phpmyadmin/
- **XAMPP Dashboard**: http://localhost/

### Default Ports:
- **Apache**: 80 (HTTP), 443 (HTTPS)
- **MySQL**: 3306
- **FileZilla FTP**: 21

### Important Files:
- **XAMPP Config**: `C:\xampp\xampp-control.ini`
- **Apache Config**: `C:\xampp\apache\conf\httpd.conf`
- **MySQL Config**: `C:\xampp\mysql\bin\my.ini`
- **PHP Config**: `C:\xampp\php\php.ini`

This guide provides everything needed to run the PawWalk application in your local XAMPP environment with the MySQL database integration.