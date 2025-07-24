<?php
// Simple startup script to serve the dog walker application
echo "Starting PawWalk Dog Walker Application...\n";
echo "Server will be available at: http://localhost:5000\n";
echo "Press Ctrl+C to stop the server\n\n";

// Start the built-in PHP server
$command = "php -S 0.0.0.0:5000 server.php";
echo "Executing: $command\n";
exec($command);
?>