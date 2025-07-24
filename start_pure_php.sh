#!/bin/bash

echo "=== Starting Pure PHP PawWalk Server ==="

# Kill any existing servers
pkill -f "tsx.*server" 2>/dev/null || true
pkill -f "node.*server" 2>/dev/null || true
pkill -f "php.*5000" 2>/dev/null || true

# Wait a moment for processes to stop
sleep 2

# Start the pure PHP server
echo "Starting PHP server on port 5000..."
php -S 0.0.0.0:5000 php_server.php

echo "Server started! Access the application at:"
echo "- Customer Login: http://localhost:5000/customer_login.php"
echo "- Admin Dashboard: http://localhost:5000/admin_dashboard.php"
echo "- Walker Dashboard: http://localhost:5000/walker_dashboard.php"