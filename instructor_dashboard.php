<?php
// Prevent caching
header('Expires: Thu, 1 Jan 1970 00:00:00 GMT');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Cache-Control: post-check=0, pre-check=0', false);
header('Pragma: no-cache');

session_start();
if ($_SESSION['role'] !== 'instructor') header("Location: login.php");
include 'config.php';

// Handle lab scheduling
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $lab_id = $_POST['lab_id'];
    $date = $_POST['date'];
    $start_time = $_POST['start_time'];
    $end_time = $_POST['end_time'];
    
    // Get instructor ID
    $user_id = $_SESSION['user_id'];
    $instructor_result = $conn->query("SELECT Instructor_ID FROM Instructor WHERE user_id = '$user_id'");
    
    if ($instructor_result && $instructor_result->num_rows > 0) {
        $instructor = $instructor_result->fetch_assoc();
        $instructor_id = $instructor['Instructor_ID'];
        
        // Get Lab TO ID for the selected lab
        $lab_result = $conn->query("SELECT Lab_TO_ID FROM Lab WHERE Lab_ID = '$lab_id'");
        $lab_to_id = null;
        if ($lab_result && $lab_result->num_rows > 0) {
            $lab = $lab_result->fetch_assoc();
            $lab_to_id = $lab['Lab_TO_ID'];
        }
        
        // Generate schedule ID
        $schedule_id = generate_id('SCH', 'Lab_Schedule', 'Schedule_ID');
        
        // Create schedule with both Instructor_ID and Lab_TO_ID
        $sql = "INSERT INTO Lab_Schedule (Schedule_ID, Date, Start_Time, End_Time, Lab_ID, Remaining_Capacity, Status, Instructor_ID, Lab_TO_ID)
                VALUES ('$schedule_id', '$date', '$start_time', '$end_time', '$lab_id', 0, 'pending', '$instructor_id', '$lab_to_id')";
        
        if ($conn->query($sql) === TRUE) {
            $_SESSION['success'] = "Lab scheduled successfully! Schedule ID: $schedule_id (Waiting for Lab TO approval)";
        } else {
            $_SESSION['error'] = "Error creating schedule: " . $conn->error;
        }
    } else {
        $_SESSION['error'] = "Instructor profile not found";
    }
    
    header("Location: instructor_dashboard.php");
    exit();
}
?>
<?php include 'header.php'; ?>
<!DOCTYPE html>
<html>
<head>
    <title>Instructor Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="#">Instructor Dashboard</a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link text-white" href="logout.php">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success"><?= $_SESSION['success'] ?></div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger"><?= $_SESSION['error'] ?></div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>
        
        <div class="row">
            <div class="col-md-6">
                <h2>Schedule a Lab Session</h2>
                <form method="POST">
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label class="form-label">Lab</label>
                            <select name="lab_id" class="form-select" required>
                                <option value="">Select Lab</option>
                                <?php 
                                // Fetch lab with name, type, and assigned Lab TO
                                $labs = $conn->query("
                                    SELECT l.Lab_ID, l.Name, l.Type, lt.Name AS lab_to_name 
                                    FROM Lab l
                                    LEFT JOIN Lab_TO lt ON l.Lab_TO_ID = lt.Lab_TO_ID
                                ");
                                if ($labs) {
                                    while($lab = $labs->fetch_assoc()): 
                                        $displayText = $lab['Name'] . " - " . $lab['Type'];
                                ?>
                                    <option value="<?= $lab['Lab_ID'] ?>"><?= htmlspecialchars($displayText) ?></option>
                                <?php 
                                    endwhile;
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label class="form-label">Date</label>
                            <input type="date" name="date" class="form-control" min="<?= date('Y-m-d') ?>" required>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Start Time</label>
                            <input type="time" name="start_time" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">End Time</label>
                            <input type="time" name="end_time" class="form-control" required>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">Schedule Lab</button>
                </form>
            </div>
            
            <div class="col-md-6">
                <h2>All Lab Schedules</h2>
                <p class="text-muted">Schedules from all instructors</p>
                
                <?php
                // Get ALL recent schedules from ALL instructors (removed filter)
                $all_schedules = $conn->query("
                    SELECT s.*, CONCAT(l.Name, ' - ', l.Type) AS lab_fullname, i.Name AS instructor_name, i.Instructor_ID
                    FROM Lab_Schedule s
                    JOIN Lab l ON s.Lab_ID = l.Lab_ID
                    LEFT JOIN Instructor i ON s.Instructor_ID = i.Instructor_ID
                    ORDER BY s.Date DESC, s.Start_Time DESC
                    LIMIT 10
                ");
                
                if ($all_schedules && $all_schedules->num_rows > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-sm table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Lab</th>
                                    <th>Date</th>
                                    <th>Time</th>
                                    <th>Instructor</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while($sched = $all_schedules->fetch_assoc()): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($sched['lab_fullname']) ?></td>
                                        <td><?= $sched['Date'] ?></td>
                                        <td><?= $sched['Start_Time'] ?>-<?= $sched['End_Time'] ?></td>
                                        <td>
                                            <?php if ($sched['instructor_name']): ?>
                                                <?= htmlspecialchars($sched['instructor_name']) ?><br>
                                                <small class="text-muted"><?= $sched['Instructor_ID'] ?></small>
                                            <?php else: ?>
                                                <span class="text-muted">Unknown</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <span class="badge bg-<?= $sched['Status'] === 'approved' ? 'success' : ($sched['Status'] === 'pending' ? 'warning' : 'danger') ?>">
                                                <?= ucfirst($sched['Status']) ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="alert alert-info">No schedules found</div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>
