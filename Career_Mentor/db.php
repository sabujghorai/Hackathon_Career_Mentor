<?php

// 1. Session start at the very top
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$host     = "localhost";
$username = "root";
$password = "";
$database = "career_mentor_ai";

// 2. Establish Database Connection
$conn = mysqli_connect($host, $username, $password, $database);

// 3. Connection Check
if (!$conn) {
    die("Database Connection Failed: " . mysqli_connect_error());
}

// 4. Set Character Encoding (Recommended for modern apps)
mysqli_set_charset($conn, "utf8mb4");

?>
