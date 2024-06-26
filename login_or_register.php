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

require 'config.php'; // Include your database connection file
session_start();

// Handle POST request for user login or registration
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"));

    if (isset($data->email) && isset($data->password)) {
        $email = $data->email;
        $password = $data->password;

        // Check if user exists and verify password
        $sql = "SELECT id, password FROM users WHERE email = ?";
        if ($stmt = $link->prepare($sql)) {
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $stmt->store_result();
            if ($stmt->num_rows == 1) {
                $stmt->bind_result($id, $hashed_password);
                $stmt->fetch();
                if (password_verify($password, $hashed_password)) {
                    $_SESSION["loggedin"] = true;
                    $_SESSION["id"] = $id;
                    $_SESSION["email"] = $email;
                    echo json_encode(["message" => "Login successful", "token" => session_id(), "email" => $email]);
                } else {
                    http_response_code(401);
                    echo json_encode(["message" => "Invalid password"]);
                }
            } else {
                // User does not exist, register the user
                $sql = "INSERT INTO users (email, password) VALUES (?, ?)";
                if ($stmt = $link->prepare($sql)) {
                    $password_hashed = password_hash($password, PASSWORD_DEFAULT);
                    $stmt->bind_param("ss", $email, $password_hashed);
                    if ($stmt->execute()) {
                        $id = $stmt->insert_id;
                        $_SESSION["loggedin"] = true;
                        $_SESSION["id"] = $id;
                        $_SESSION["email"] = $email;
                        echo json_encode(["message" => "Registration successful", "token" => session_id(), "email" => $email]);
                    } else {
                        http_response_code(500);
                        echo json_encode(["message" => "Something went wrong"]);
                    }
                }
            }
            $stmt->close();
        } else {
            http_response_code(500);
            echo json_encode(["message" => "Database error"]);
        }
    } else {
        http_response_code(400);
        echo json_encode(["message" => "Incomplete data"]);
    }
} else {
    http_response_code(405); // Method Not Allowed
    echo json_encode(["error" => "Method not allowed"]);
}
?>



















