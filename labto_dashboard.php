<?php
// Prevent caching
header('Expires: Thu, 1 Jan 1970 00:00:00 GMT');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Cache-Control: post-check=0, pre-check=0', false);
header('Pragma: no-cache');

session_start();
if ($_SESSION['role'] !== 'lab_to') header("Location: login.php");
include 'config.php';

// Get current Lab TO ID
$user_id = $_SESSION['user_id'];
$labto_result = $conn->query("SELECT Lab_TO_ID FROM Lab_TO WHERE user_id = '$user_id'");
if (!$labto_result || $labto_result->num_rows === 0) {
    die("Lab TO profile not found for this user");
}
$labto = $labto_result->fetch_assoc();
$labto_id = $labto['Lab_TO_ID'];

// Handle schedule approval
if (isset($_GET['approve'])) {
    $schedule_id = $_GET['approve'];
    $capacity = $_GET['capacity'];
    $sql = "UPDATE Lab_Schedule 
            SET Status = 'approved', Remaining_Capacity = $capacity 
            WHERE Schedule_ID = '$schedule_id'";
    if ($conn->query($sql) === TRUE) {
        $_SESSION['success'] = "Schedule approved successfully!";
    } else {
        $_SESSION['error'] = "Error updating schedule: " . $conn->error;
    }
    header("Location: labto_dashboard.php");
    exit();
}

// Handle schedule rejection
if (isset($_GET['reject'])) {
    $schedule_id = $_GET['reject'];
    $sql = "UPDATE Lab_Schedule SET Status = 'rejected' WHERE Schedule_ID = '$schedule_id'";
    if ($conn->query($sql) === TRUE) {
        $_SESSION['success'] = "Schedule rejected successfully!";
    } else {
        $_SESSION['error'] = "Error rejecting schedule: " . $conn->error;
    }
    header("Location: labto_dashboard.php");
    exit();
}

// Get only pending schedules for labs assigned to this Lab TO
$sql = "SELECT s.*, CONCAT(l.Name, ' - ', l.Type) AS lab_fullname 
        FROM Lab_Schedule s
        JOIN Lab l ON s.Lab_ID = l.Lab_ID
        WHERE s.Status = 'pending' 
        AND l.Lab_TO_ID = '$labto_id'";
$schedules = $conn->query($sql);
if ($schedules === false) {
    die("SQL Error: " . $conn->error . "<br>Query: " . $sql);
}
?>
<?php include 'header.php'; ?>
<!DOCTYPE html>
<html>
<head>
    <title>Lab TO Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .equipment-table { font-size: 0.9em; }
        .lab-info-card { border-left: 4px solid #0d6efd; }
        .equipment-item { background-color: #f8f9fa; border-radius: 5px; padding: 8px; margin-bottom: 5px; }
        .btn-space { margin-right: 0.5em; }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="#">Lab TO Dashboard</a>
            <div class="navbar-nav ms-auto">
                <span class="navbar-text text-white me-3">
                    Lab TO ID: <?= htmlspecialchars($labto_id) ?>
                </span>
                <a class="nav-link text-white" href="logout.php">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container-fluid mt-4">
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success"><?= $_SESSION['success'] ?></div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger"><?= $_SESSION['error'] ?></div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>
        <div class="row">
            <!-- Left Side - Pending Schedules -->
            <div class="col-md-7">
                <h2>Pending Lab Schedules</h2>
                <p class="text-muted">Showing schedules for labs assigned to you</p>
                <?php if ($schedules && $schedules->num_rows === 0): ?>
                    <div class="alert alert-info">No pending schedules for your assigned labs</div>
                <?php elseif ($schedules && $schedules->num_rows > 0): ?>
                    <?php while($schedule = $schedules->fetch_assoc()): ?>
                        <div class="card mb-4">
                            <div class="card-header bg-warning">
                                <div class="row">
                                    <div class="col">
                                        <strong>Schedule ID:</strong> <?= htmlspecialchars($schedule['Schedule_ID']) ?>
                                    </div>
                                    <div class="col text-end">
                                        <small>Your Lab</small>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <h5 class="card-title"><?= htmlspecialchars($schedule['lab_fullname']) ?></h5>
                                <div class="row">
                                    <div class="col-md-6">
                                        <p><strong>Date:</strong> <?= htmlspecialchars($schedule['Date']) ?></p>
                                        <p><strong>Time:</strong> <?= htmlspecialchars($schedule['Start_Time']) ?> - <?= htmlspecialchars($schedule['End_Time']) ?></p>
                                    </div>
                                    <div class="col-md-6">
                                        <h6>Required Equipment:</h6>
                                        <div class="small">
                                            <?php 
                                            $equipment_sql = "SELECT * FROM Lab_Equipment WHERE Lab_ID = '{$schedule['Lab_ID']}'";
                                            $equipment = $conn->query($equipment_sql);
                                            if ($equipment && $equipment->num_rows > 0) {
                                                while($eq = $equipment->fetch_assoc()): ?>
                                                    <div class="equipment-item">
                                                        <strong><?= htmlspecialchars($eq['Name']) ?></strong><br>
                                                        <small>Available: <?= $eq['Quantity'] ?> units</small>
                                                    </div>
                                                <?php endwhile;
                                            } else {
                                                echo "<div class='text-muted'>No equipment registered for this lab</div>";
                                            }
                                            ?>
                                        </div>
                                    </div>
                                </div>
                                <hr>
                                <form method="GET" action="labto_dashboard.php" class="d-inline">
                                    <input type="hidden" name="approve" value="<?= htmlspecialchars($schedule['Schedule_ID']) ?>">
                                    <div class="row align-items-end">
                                        <div class="col-md-5">
                                            <label class="form-label">Set Capacity</label>
                                            <input type="number" name="capacity" class="form-control" min="1" max="100" value="30" required>
                                            <small class="text-muted">Consider equipment availability</small>
                                        </div>
                                        <div class="col-md-7 mt-3 mt-md-0">
                                            <button type="submit" class="btn btn-success btn-space">Approve Schedule</button>
                                </form>
                                <form method="GET" action="labto_dashboard.php" class="d-inline">
                                    <input type="hidden" name="reject" value="<?= htmlspecialchars($schedule['Schedule_ID']) ?>">
                                    <button type="submit" class="btn btn-danger">Reject</button>
                                </form>
                                        </div>
                                    </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="alert alert-danger">Error loading schedules. Please check database connection.</div>
                <?php endif; ?>
            </div>
            <!-- Right Side - Lab Information and Equipment -->
            <div class="col-md-5">
                <!-- My Assigned Labs with Equipment -->
                <div class="card lab-info-card mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">My Assigned Labs & Equipment</h5>
                    </div>
                    <div class="card-body">
                        <?php
                        $my_labs = $conn->query("
                            SELECT Lab_ID, CONCAT(Name, ' - ', Type) AS lab_fullname, Name, Type, Capacity 
                            FROM Lab 
                            WHERE Lab_TO_ID = '$labto_id'
                        ");
                        if ($my_labs && $my_labs->num_rows > 0): 
                            while($lab = $my_labs->fetch_assoc()): ?>
                                <div class="mb-4">
                                    <h6 class="text-primary"><?= htmlspecialchars($lab['lab_fullname']) ?></h6>
                                    <p class="small text-muted mb-2">
                                        Lab ID: <?= htmlspecialchars($lab['Lab_ID']) ?> | Max Capacity: <?= htmlspecialchars($lab['Capacity']) ?>
                                    </p>
                                    <div class="table-responsive">
                                        <table class="table table-sm equipment-table">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>Equipment</th>
                                                    <th>Quantity</th>
                                                    <th>ID</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                $lab_equipment = $conn->query("
                                                    SELECT * FROM Lab_Equipment 
                                                    WHERE Lab_ID = '{$lab['Lab_ID']}'
                                                    ORDER BY Name
                                                ");
                                                if ($lab_equipment && $lab_equipment->num_rows > 0) {
                                                    while($equipment = $lab_equipment->fetch_assoc()): ?>
                                                        <tr>
                                                            <td><?= htmlspecialchars($equipment['Name']) ?></td>
                                                            <td>
                                                                <span class="badge bg-<?= $equipment['Quantity'] > 0 ? 'success' : 'danger' ?>">
                                                                    <?= $equipment['Quantity'] ?>
                                                                </span>
                                                            </td>
                                                            <td><small><?= htmlspecialchars($equipment['Equipment_ID']) ?></small></td>
                                                        </tr>
                                                    <?php endwhile;
                                                } else { ?>
                                                    <tr>
                                                        <td colspan="3" class="text-center text-muted">
                                                            <em>No equipment registered</em>
                                                        </td>
                                                    </tr>
                                                <?php } ?>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="alert alert-info py-2">
                                        <small>
                                            <strong>Capacity Guide:</strong> 
                                            Consider equipment quantity when setting capacity. 
                                            Recommended: <?= min($lab['Capacity'], 30) ?> students max.
                                        </small>
                                    </div>
                                </div>
                                <hr>
                            <?php endwhile;
                        else: ?>
                            <div class="alert alert-warning">No labs assigned to you</div>
                        <?php endif; ?>
                    </div>
                </div>
                <!-- Recent Approved Schedules -->
                <div class="card">
                    <div class="card-header bg-success text-white">
                        <h6 class="mb-0">Recent Approved Schedules</h6>
                    </div>
                    <div class="card-body">
                        <?php
                        $recent_approved = $conn->query("
                            SELECT s.*, CONCAT(l.Name, ' - ', l.Type) AS lab_fullname 
                            FROM Lab_Schedule s
                            JOIN Lab l ON s.Lab_ID = l.Lab_ID
                            WHERE s.Status = 'approved' 
                            AND l.Lab_TO_ID = '$labto_id'
                            ORDER BY s.Schedule_ID DESC
                            LIMIT 5
                        ");
                        if ($recent_approved && $recent_approved->num_rows > 0): ?>
                            <div class="list-group list-group-flush">
                                <?php while($approved = $recent_approved->fetch_assoc()): ?>
                                    <div class="list-group-item px-0">
                                        <h6 class="mb-1"><?= htmlspecialchars($approved['lab_fullname']) ?></h6>
                                        <small class="text-muted">
                                            <?= htmlspecialchars($approved['Date']) ?> | <?= htmlspecialchars($approved['Start_Time']) ?>-<?= htmlspecialchars($approved['End_Time']) ?><br>
                                            <span class="badge bg-success">Approved</span>
                                            Capacity: <?= htmlspecialchars($approved['Remaining_Capacity']) ?>
                                        </small>
                                    </div>
                                <?php endwhile; ?>
                            </div>
                        <?php else: ?>
                            <p class="text-muted mb-0">No approved schedules yet</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
