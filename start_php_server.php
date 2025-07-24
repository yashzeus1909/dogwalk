<?php
// Start PHP built-in server for dogWalk project
// This script starts a pure PHP server on port 8000

$host = '0.0.0.0';
$port = 8000;
$docRoot = __DIR__;

echo "Starting PHP server for dogWalk project...\n";
echo "Server: http://localhost:$port\n";
echo "Document root: $docRoot\n";
echo "Access your app at: http://localhost:$port/common_login.html\n";
echo "\nPress Ctrl+C to stop the server\n\n";

// Start the built-in PHP server
$command = "php -S $host:$port -t $docRoot";
passthru($command);
?>