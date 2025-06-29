<?php
session_start();
if ($_SESSION['role'] !== 'student') header("Location: login.php");
include 'config.php';

// Handle lab booking
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $schedule_id = $_POST['schedule_id'];
    $user_id = $_SESSION['user_id'];
    $student = $conn->query("SELECT Student_ID FROM Student WHERE user_id = '$user_id'")->fetch_assoc();
    $student_id = $student['Student_ID'];

    // Check if already booked
    $check = $conn->query("SELECT * FROM Lab_Booking WHERE Student_ID = '$student_id' AND Schedule_ID = '$schedule_id'");
    if ($check->num_rows > 0) {
        $_SESSION['error'] = "You've already booked this lab session";
    } else {
        // Generate booking ID
        $booking_id = generate_id('BO', 'Lab_Booking', 'Booking_ID');
        // Create booking
        $sql = "INSERT INTO Lab_Booking (Booking_ID, Status, Student_ID, Schedule_ID) VALUES ('$booking_id', 'confirmed', '$student_id', '$schedule_id')";
        if ($conn->query($sql) === TRUE) {
            $conn->query("UPDATE Lab_Schedule SET Remaining_Capacity = Remaining_Capacity - 1 WHERE Schedule_ID = '$schedule_id'");
            $_SESSION['success'] = "Lab booked successfully! Booking ID: $booking_id";
        } else {
            $_SESSION['error'] = "Error: " . $conn->error;
        }
    }
    header("Location: student_dashboard.php");
    exit();
}

// Get student ID for welcome message
$user_id = $_SESSION['user_id'];
$student_result = $conn->query("SELECT Student_ID FROM Student WHERE user_id = '$user_id'");
$student_id = '';
if ($student_result && $student_result->num_rows > 0) {
    $student = $student_result->fetch_assoc();
    $student_id = $student['Student_ID'];
}

// Get available schedules (excluding labs already booked by this student)
$available_schedules = $conn->query("
    SELECT s.*, CONCAT(l.Name, ' - ', l.Type) AS lab_fullname 
    FROM Lab_Schedule s
    JOIN Lab l ON s.Lab_ID = l.Lab_ID
    WHERE s.Status = 'approved' 
    AND s.Remaining_Capacity > 0
    AND s.Schedule_ID NOT IN (
        SELECT Schedule_ID FROM Lab_Booking WHERE Student_ID = '$student_id'
    )
");

// Get booked labs for this student
$booked_schedules = $conn->query("
    SELECT s.*, CONCAT(l.Name, ' - ', l.Type) AS lab_fullname, b.Booking_ID, b.Booking_Date
    FROM Lab_Booking b
    JOIN Lab_Schedule s ON b.Schedule_ID = s.Schedule_ID
    JOIN Lab l ON s.Lab_ID = l.Lab_ID
    WHERE b.Student_ID = '$student_id'
    ORDER BY s.Date ASC
");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Student Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .logout-btn {
            position: absolute;
            right: 32px;
            top: 28px;
        }
        .welcome-message {
            background: linear-gradient(45deg, #003366, #0055a5);
            color: white;
            padding: 15px 25px;
            border-radius: 8px;
            margin-bottom: 25px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .card {
            transition: transform 0.3s, box-shadow 0.3s;
            margin-bottom: 20px;
        }
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.12);
        }
        .booked-card {
            background: #f8f9fa;
            border-left: 4px solid #28a745;
        }
        .booked-card:hover {
            transform: none;
        }
        body {
            background: #fff;
        }
    </style>
</head>
<body>
    <?php include 'header.php'; ?>
    <div class="container mt-4 position-relative">
        <a href="logout.php" class="btn btn-outline-danger logout-btn">Logout</a>
        
        <div class="welcome-message">
            <h2>
                <?php if ($student_id): ?>
                    Welcome <span class="badge bg-light text-dark"><?= htmlspecialchars($student_id) ?></span>
                <?php else: ?>
                    Welcome Student
                <?php endif; ?>
            </h2>
            <p class="mb-0">Book available lab sessions below</p>
        </div>
        
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success"><?= $_SESSION['success'] ?></div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger"><?= $_SESSION['error'] ?></div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>
        
        <div class="row">
            <!-- Left Side - Available Lab Sessions -->
            <div class="col-md-8">
                <h3>Available Lab Sessions</h3>
                
                <?php if ($available_schedules && $available_schedules->num_rows === 0): ?>
                    <div class="alert alert-info">No available lab sessions at this time</div>
                <?php elseif ($available_schedules && $available_schedules->num_rows > 0): ?>
                    <div class="row">
                        <?php while($schedule = $available_schedules->fetch_assoc()): ?>
                            <div class="col-md-6 mb-3">
                                <div class="card h-100">
                                    <div class="card-header bg-success text-white">
                                        <?= htmlspecialchars($schedule['lab_fullname']) ?>
                                    </div>
                                    <div class="card-body">
                                        <p><strong>Date:</strong> <?= htmlspecialchars($schedule['Date']) ?></p>
                                        <p><strong>Time:</strong> <?= htmlspecialchars($schedule['Start_Time']) ?> - <?= htmlspecialchars($schedule['End_Time']) ?></p>
                                        <p><strong>Slots Available:</strong> <?= htmlspecialchars($schedule['Remaining_Capacity']) ?></p>
                                    </div>
                                    <div class="card-footer">
                                        <form method="POST">
                                            <input type="hidden" name="schedule_id" value="<?= $schedule['Schedule_ID'] ?>">
                                            <button type="submit" class="btn btn-primary w-100">Book This Lab</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    </div>
                <?php else: ?>
                    <div class="alert alert-danger">Error loading schedules. Please try again later.</div>
                <?php endif; ?>
            </div>
            
            <!-- Right Side - Your Booked Labs -->
            <div class="col-md-4">
                <h3>Your Booked Labs</h3>
                
                <?php if ($booked_schedules && $booked_schedules->num_rows === 0): ?>
                    <div class="alert alert-info">You haven't booked any labs yet</div>
                <?php elseif ($booked_schedules && $booked_schedules->num_rows > 0): ?>
                    <?php while($booked = $booked_schedules->fetch_assoc()): ?>
                        <div class="card booked-card mb-3">
                            <div class="card-header bg-primary text-white">
                                <?= htmlspecialchars($booked['lab_fullname']) ?>
                            </div>
                            <div class="card-body">
                                <p><strong>Date:</strong> <?= htmlspecialchars($booked['Date']) ?></p>
                                <p><strong>Time:</strong> <?= htmlspecialchars($booked['Start_Time']) ?> - <?= htmlspecialchars($booked['End_Time']) ?></p>
                                <p><strong>Booking ID:</strong> <?= htmlspecialchars($booked['Booking_ID']) ?></p>
                                <p><strong>Status:</strong> 
                                    <span class="badge bg-success">Confirmed</span>
                                </p>
                                <small class="text-muted">
                                    Booked on: <?= date('M d, Y', strtotime($booked['Booking_Date'])) ?>
                                </small>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="alert alert-warning">Error loading your bookings</div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>
