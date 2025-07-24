<?php
echo "Starting PawWalk Dog Walker Application...\n";
echo "Server will start on http://localhost:5000\n";
echo "Press Ctrl+C to stop the server\n\n";

// Start the built-in PHP server
$command = "php -t . -S 0.0.0.0:5000";
passthru($command);
?>