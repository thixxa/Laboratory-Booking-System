<?php
// Database configuration
$host = "localhost";
$user = "root";
$password = "";
$dbname = "laboratory_booking_system";

// Create connection
$conn = new mysqli($host, $user, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set charset
$conn->set_charset("utf8");

// Helper function for generating IDs
function generate_id($prefix, $table, $id_column) {
    global $conn;
    $result = $conn->query("SELECT $id_column FROM $table ORDER BY $id_column DESC LIMIT 1");
    if ($result->num_rows > 0) {
        $last_id = $result->fetch_assoc()[$id_column];
        $num = (int)substr($last_id, strlen($prefix)) + 1;
        return $prefix . str_pad($num, 3, '0', STR_PAD_LEFT);
    }
    return $prefix . '001';
}

// Debug function
function debug_log($message) {
    error_log(date('[Y-m-d H:i:s] ') . $message);
}
?>
