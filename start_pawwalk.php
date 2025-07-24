<?php
// PawWalk PHP Server Startup Script
echo "Starting PawWalk Dog Walker Application...\n";
echo "Server will be available at: http://localhost:5000\n";
echo "Main page: http://localhost:5000/index.html\n";
echo "Registration: http://localhost:5000/register.html\n";
echo "Login: http://localhost:5000/login.html\n";
echo "\nPress Ctrl+C to stop the server\n\n";

// Start the built-in PHP server
$command = "php -S 0.0.0.0:5000";
passthru($command);
?>