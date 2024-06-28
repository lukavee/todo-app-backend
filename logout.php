<?php
// Enable CORS headers
header("Access-Control-Allow-Origin: http://localhost:5173");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// Handle OPTIONS requests for CORS preflight
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header("Access-Control-Allow-Origin: http://localhost:5173");
    header("Access-Control-Allow-Methods: POST, GET, OPTIONS, PUT, DELETE");
    header("Access-Control-Allow-Headers: Content-Type, Authorization");
    header("Access-Control-Max-Age: 86400"); // Cache preflight response for 1 day
    header("Content-Length: 0");
    header("Content-Type: text/plain");
    http_response_code(204);
    exit();
}

// Initialize the session
session_start();

// Unset all session variables
$_SESSION = array();

// Destroy the session
session_destroy();

// Return a success message
echo json_encode(["message" => "Logout successful"]);

?>
