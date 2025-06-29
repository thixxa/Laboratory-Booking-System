<?php
session_start();
if ($_SESSION['role'] !== 'lecture') header("Location: login.php");
include 'config.php';

// Fetch all scheduled labs with instructor ID and name
$schedule_sql = "SELECT s.*, CONCAT(l.Name, ' - ', l.Type) AS lab_fullname, 
                        i.Instructor_ID, i.Name AS instructor_name
                 FROM Lab_Schedule s
                 JOIN Lab l ON s.Lab_ID = l.Lab_ID
                 LEFT JOIN Instructor i ON s.Instructor_ID = i.Instructor_ID
                 ORDER BY s.Date DESC, s.Start_Time";
$schedule_result = $conn->query($schedule_sql);

$schedules = [];
if ($schedule_result && $schedule_result->num_rows > 0) {
    while($row = $schedule_result->fetch_assoc()) {
        $schedules[$row['Schedule_ID']] = $row;
    }
}

// Fetch student IDs who booked each scheduled lab
$booking_sql = "SELECT Schedule_ID, GROUP_CONCAT(Student_ID) AS student_ids FROM Lab_Booking GROUP BY Schedule_ID";
$booking_result = $conn->query($booking_sql);

$bookings = [];
if ($booking_result && $booking_result->num_rows > 0) {
    while($row = $booking_result->fetch_assoc()) {
        $bookings[$row['Schedule_ID']] = explode(',', $row['student_ids']);
    }
}
?>
<?php include 'header.php'; ?>
<!DOCTYPE html>
<html>
<head>
    <title>Lecture Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container">
        <a class="navbar-brand" href="#">Lecture Dashboard</a>
        <div class="navbar-nav ms-auto">
            <a class="nav-link text-white" href="logout.php">Logout</a>
        </div>
    </div>
</nav>

<div class="container mt-4">
    <h2>All Scheduled Labs</h2>
    <?php if (empty($schedules)): ?>
        <div class="alert alert-info">No scheduled labs found.</div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Schedule ID</th>
                        <th>Lab</th>
                        <th>Date</th>
                        <th>Time</th>
                        <th>Status</th>
                        <th>Capacity</th>
                        <th>Instructor</th>
                        <th>Students Booked</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach($schedules as $schedule_id => $schedule): ?>
                    <tr>
                        <td><?= htmlspecialchars($schedule['Schedule_ID']) ?></td>
                        <td><?= htmlspecialchars($schedule['lab_fullname']) ?></td>
                        <td><?= htmlspecialchars($schedule['Date']) ?></td>
                        <td><?= htmlspecialchars($schedule['Start_Time']) ?> - <?= htmlspecialchars($schedule['End_Time']) ?></td>
                        <td>
                            <span class="badge bg-<?= $schedule['Status'] === 'approved' ? 'success' : ($schedule['Status'] === 'pending' ? 'warning' : 'danger') ?>">
                                <?= ucfirst($schedule['Status']) ?>
                            </span>
                        </td>
                        <td><?= htmlspecialchars($schedule['Remaining_Capacity']) ?></td>
                        <td>
                            <?php
                            if (!empty($schedule['Instructor_ID'])) {
                                echo htmlspecialchars($schedule['Instructor_ID']);
                                // Optionally, also show instructor name:
                                // echo " (" . htmlspecialchars($schedule['instructor_name']) . ")";
                            } else {
                                echo '<span class="text-muted">Unknown</span>';
                            }
                            ?>
                        </td>
                        <td>
                            <?php if (!empty($bookings[$schedule_id])): ?>
                                <?php foreach($bookings[$schedule_id] as $sid): ?>
                                    <span class="badge bg-secondary"><?= htmlspecialchars($sid) ?></span>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <span class="text-muted">None</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>
</body>
</html>
