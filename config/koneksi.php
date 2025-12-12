<?php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "laundry";

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Set charset to handle special characters
mysqli_set_charset($conn, "utf8mb4");

// Test query
$test_query = mysqli_query($conn, "SELECT 1");
if (!$test_query) {
    die("Database error: " . mysqli_error($conn));
}
?>