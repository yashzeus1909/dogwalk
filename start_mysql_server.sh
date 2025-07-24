#!/bin/bash

# Start MySQL server and setup database
echo "Starting MySQL server..."

# Initialize MySQL data directory if it doesn't exist
if [ ! -d "/tmp/mysql_data" ]; then
    mkdir -p /tmp/mysql_data
    mysqld --initialize-insecure --user=runner --datadir=/tmp/mysql_data
fi

# Start MySQL server
mysqld --user=runner --datadir=/tmp/mysql_data --socket=/tmp/mysql.sock --port=3306 &

# Wait for MySQL to start
echo "Waiting for MySQL to start..."
sleep 5

# Setup database
echo "Setting up database..."
mysql -u root --socket=/tmp/mysql.sock < setup_mysql.sql

echo "MySQL server started and database configured!"
echo "Database: dogWalk"
echo "Socket: /tmp/mysql.sock"
echo "Port: 3306"