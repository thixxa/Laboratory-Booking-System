<?php
session_start();
include 'config.php';

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $selected_role = $_POST['role'];
    
    // Debug: Log the attempt
    error_log("Login attempt: $username, Role: $selected_role");
    
    if (empty($username) || empty($password) || empty($selected_role)) {
        $_SESSION['error'] = "Please fill all fields";
        header("Location: login.php");
        exit();
    }
    
    // Check user credentials
    $stmt = $conn->prepare("SELECT user_id, password, role FROM Users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        
        // For now, use plain text comparison (we'll fix hashing later)
        if ($password === $user['password'] && $selected_role === $user['role']) {
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['username'] = $username;
            
            // Debug: Log successful login
            error_log("Successful login: $username as {$user['role']}");
            
            // Redirect based on role
            switch($user['role']) {
                case 'student': 
                    header("Location: student_dashboard.php"); 
                    break;
                case 'instructor': 
                    header("Location: instructor_dashboard.php"); 
                    break;
                case 'lab_to': 
                    header("Location: labto_dashboard.php"); 
                    break;
                case 'lecture': 
                    header("Location: lecture_dashboard.php"); 
                    break;
                default:
                    $_SESSION['error'] = "Invalid role";
                    header("Location: login.php");
            }
            exit();
        } else {
            $_SESSION['error'] = "Invalid password or role mismatch";
        }
    } else {
        $_SESSION['error'] = "Username not found";
    }
    
    // Failed login
    error_log("Failed login attempt: $username");
    header("Location: login.php");
    exit();
} else {
    // Direct access to auth.php
    header("Location: login.php");
    exit();
}
?>
