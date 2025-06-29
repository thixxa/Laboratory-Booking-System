<?php
session_start();
include 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect form data
    $username = trim($_POST['username']);
    $password = trim($_POST['password']); // Store as plain text (as requested)
    $name = trim($_POST['name']);
    $role = $_POST['role'];
    
    // Additional role-specific data
    $student_id = isset($_POST['student_id']) ? trim($_POST['student_id']) : null;
    $semester = isset($_POST['semester']) ? $_POST['semester'] : null;
    $department = isset($_POST['department']) ? trim($_POST['department']) : null;

    // Auto-generate user_id based on role
    function generate_user_id($role, $conn) {
        $prefixes = [
            'student' => 'ST',
            'instructor' => 'IT', 
            'lab_to' => 'TO',
            'lecture' => 'L'
        ];
        
        $prefix = $prefixes[$role];
        
        // Get the highest existing ID for this role
        $result = $conn->query("SELECT user_id FROM Users WHERE role = '$role' AND user_id LIKE '$prefix%' ORDER BY user_id DESC LIMIT 1");
        
        if ($result && $result->num_rows > 0) {
            $last_id = $result->fetch_assoc()['user_id'];
            // Extract number and increment
            $num = (int)substr($last_id, strlen($prefix)) + 1;
        } else {
            $num = 1;
        }
        
        return $prefix . $num;
    }
    
    // Check if username already exists
    $check_user = $conn->query("SELECT username FROM Users WHERE username = '$username'");
    if ($check_user->num_rows > 0) {
        $_SESSION['error'] = "Username already exists. Please choose a different username.";
        header("Location: register.php");
        exit();
    }
    
    // For students, check if Student_ID already exists
    if ($role === 'student' && $student_id) {
        $check_student = $conn->query("SELECT Student_ID FROM Student WHERE Student_ID = '$student_id'");
        if ($check_student->num_rows > 0) {
            $_SESSION['error'] = "Student ID already exists. Please check your Student ID.";
            header("Location: register.php");
            exit();
        }
    }
    
    // Generate user ID
    $user_id = generate_user_id($role, $conn);
    
    // Start transaction
    $conn->begin_transaction();

    try {
        // Insert into Users table (plain text password as requested)
        $sql = "INSERT INTO Users (user_id, username, password, role, status) 
                VALUES (?, ?, ?, ?, 'active')";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssss", $user_id, $username, $password, $role);
        $stmt->execute();
        
        // Insert into role-specific table
        switch ($role) {
            case 'student':
                if (!$student_id) {
                    throw new Exception("Student ID is required for student registration");
                }
                $sql = "INSERT INTO Student (Student_ID, user_id, Name, Semester) 
                        VALUES (?, ?, ?, ?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ssss", $student_id, $user_id, $name, $semester);
                break;
                
            case 'instructor':
                $instructor_id = generate_id('IST', 'Instructor', 'Instructor_ID');
                $sql = "INSERT INTO Instructor (Instructor_ID, user_id, Name) 
                        VALUES (?, ?, ?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("sss", $instructor_id, $user_id, $name);
                break;
                
            case 'lab_to':
                $lab_to_id = generate_id('LTO', 'Lab_TO', 'Lab_TO_ID');
                $sql = "INSERT INTO Lab_TO (Lab_TO_ID, user_id, Name) 
                        VALUES (?, ?, ?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("sss", $lab_to_id, $user_id, $name);
                break;
                
            case 'lecture':
                $lecture_id = generate_id('LCT', 'Lecture', 'Lecture_ID');
                $sql = "INSERT INTO Lecture (Lecture_ID, user_id, Name, Department) 
                        VALUES (?, ?, ?, ?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ssss", $lecture_id, $user_id, $name, $department);
                break;
                
            default:
                throw new Exception("Invalid user role");
        }
        
        $stmt->execute();
        $conn->commit();
        
        $_SESSION['success'] = "Registration successful! Your User ID is: $user_id. You can now login.";
        header("Location: login.php");
        exit();
        
    } catch (Exception $e) {
        $conn->rollback();
        $_SESSION['error'] = "Registration failed: " . $e->getMessage();
        header("Location: register.php");
        exit();
    }
} else {
    header("Location: register.php");
    exit();
}
?>
