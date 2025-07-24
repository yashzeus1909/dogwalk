# Dog Walker Booking Application

## Overview
A full-stack web application for connecting dog owners with professional dog walkers. Built with Node.js/Express backend, React frontend, and PostgreSQL database.

## Recent Changes (January 24, 2025)
- **✅ RESOLVED: Data Storage Issue**: Fixed database schema and confirmed data is being stored correctly
- **✅ Unified Table Structure**: Single users table with role field successfully differentiates customers/walkers
- **✅ Database Testing**: Confirmed 3 walkers, 2 customers, 10 bookings working properly
- **✅ API Endpoints**: Walker API functioning correctly with SQLite database
- **✅ PHP Server**: Application running on port 5000 with manual PHP server startup
- **✅ GitHub Ready**: Created comprehensive documentation (README, LICENSE, CONTRIBUTING)
- **✅ CONFIRMED: No "dgWalk" References**: Application correctly named "PawWalk" throughout codebase
- **✅ PURE DATABASE OPERATIONS**: Added comprehensive CRUD functions using only database (no file storage)
- **✅ EMAIL VALIDATION FIXED**: Resolved "Email address already exists" error with case-insensitive validation and proper database cleanup
- **✅ PURE DATABASE ARCHITECTURE**: Removed all file storage logic - all operations now use direct database table queries only

## Project Architecture

### Backend Structure
- **PHP Server**: Built-in PHP development server on port 5000
- **SQLite Database**: Lightweight SQLite database with PDO connections
- **Authentication**: PHP session-based authentication system  
- **API Layer**: RESTful PHP endpoints for walker and booking management

### Frontend Structure
- **HTML/CSS/JavaScript**: Traditional web stack with modern responsive design
- **jQuery**: AJAX functionality for dynamic content loading
- **Modal System**: Custom modal implementation for booking forms
- **CSS Grid/Flexbox**: Modern layout techniques for responsive design

### Key Features
- **Walker Management**: Browse, filter, and book dog walkers
- **User Authentication**: Registration and login for customers
- **Booking System**: Create, manage, and track walking appointments
- **Profile Management**: User and walker profile updates
- **Real-time Data**: Live updates for bookings and availability

## Database Schema
- **Users Table**: Unified table storing both customers and walkers with role differentiation
  - Basic info: id, first_name, last_name, email, password, phone, address, role
  - Walker-specific fields: image, rating, review_count, distance, price, description, availability, badges, services, background_check, insured, certified
- **Bookings Table**: Appointment records with full booking details
  - Booking info: walker_id, customer_id, dog_name, dog_size, booking_date, booking_time, duration
  - Contact: phone, address, special_notes
  - Business: total_price, status, created_at, updated_at

## Security Measures
- Password hashing with PHP's password_hash() function
- Session management with PHP sessions
- Input validation and sanitization
- SQL injection prevention with PDO prepared statements
- Proper error handling and user feedback

## User Preferences
- Requested complete migration from TypeScript to PHP/MySQL stack
- Preferred traditional web technologies (HTML, CSS, jQuery, PHP)
- Requested simplified database structure with only 2 tables

## Environment Setup
- PHP 8.2+ with PDO SQLite extension
- SQLite database file (database/dog_walker_app.db)
- Built-in PHP development server on port 5000
- Traditional HTML/CSS/JS frontend stack

## Current Status
- Database setup completed with sample data
- 3 professional walkers available
- Application ready to run with PHP server