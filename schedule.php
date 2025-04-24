<?php
session_start();
include 'db_connect.php';

// Restrict access to only logged-in admins
if (!isset($_SESSION['admin_id'])) {
    echo "<script>alert('Access Denied! Please log in first.'); window.location.href='login.php';</script>";
    exit();
}

// Set timezone to Philippine Standard Time (PHT)
date_default_timezone_set('Asia/Manila');

// Fetch the latest schedule
$scheduleQuery = "SELECT * FROM schedule ORDER BY id DESC LIMIT 1";
$scheduleResult = $conn->query($scheduleQuery);
$scheduleData = $scheduleResult->fetch_assoc();

// Get current time
$current_time = date("Y-m-d H:i:s");
$votingStatus = "closed"; // Default status

if ($scheduleData) {
    $open_time = $scheduleData['open_time'];
    $close_time = $scheduleData['close_time'];

    // Check if current time is within the voting period
    if ($current_time >= $open_time && $current_time <= $close_time) {
        $votingStatus = "open";
    }

    // Update schedule table only if the status has changed
    if ($scheduleData['status'] !== $votingStatus) {
        $conn->query("UPDATE schedule SET status='$votingStatus' WHERE id=" . $scheduleData['id']);
    }

    // If voting closes, log out voters (but keep admins logged in)
    if ($votingStatus == "closed" && isset($_SESSION['voter_id'])) {
        unset($_SESSION['voter_id']);
        echo "<script>window.location.href='login.php';</script>";
        exit();
    }
}

// Handle form submission (Updating schedule)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $new_open_time = $_POST['open_time'];
    $new_close_time = $_POST['close_time'];

    if ($new_open_time >= $new_close_time) {
        echo "<script>alert('Close time must be after open time.');</script>";
    } else {
        // Delete old schedule and insert a new one
        $conn->query("DELETE FROM schedule");
        $conn->query("INSERT INTO schedule (open_time, close_time, status) VALUES ('$new_open_time', '$new_close_time', 'closed')");
        echo "<script>alert('Schedule updated successfully!'); window.location.href='schedule.php';</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Voting Schedule</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

    <nav class="navbar navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="#">Manage Schedule</a>
            <a href="admin_dashboard.php" class="btn btn-secondary">Back</a>
        </div>
    </nav>

    <div class="container mt-4">
        <h2 class="text-center">Voting Schedule</h2>

        <div class="row">
            <div class="col-md-6 offset-md-3">
                <div class="card mb-3">
                    <div class="card-body text-center">
                        <h5 class="card-title">Current Voting Status</h5>
                        <p class="fw-bold text-uppercase">
                            <?php echo ($votingStatus == 'open') ? '<span class="text-success">OPEN</span>' : '<span class="text-danger">CLOSED</span>'; ?>
                        </p>
                    </div>
                </div>

                <!-- Showcase Section -->
                <?php if ($scheduleData): ?>
                    <div class="card mb-3">
                        <div class="card-body text-center">
                            <h5 class="card-title">Voting Period</h5>
                            <p><strong>Opens:</strong> <?php echo date("F d, Y h:i A", strtotime($scheduleData['open_time'])); ?></p>
                            <p><strong>Closes:</strong> <?php echo date("F d, Y h:i A", strtotime($scheduleData['close_time'])); ?></p>
                            <p><strong>Current Time:</strong> <?php echo date("F d, Y h:i A", strtotime($current_time)); ?></p>
                        </div>
                    </div>
                <?php endif; ?>

                <div class="card">
                    <div class="card-body">
                        <h4 class="text-center">Set New Schedule</h4>
                        <form action="schedule.php" method="POST">
                            <div class="mb-3">
                                <label for="open_time" class="form-label">Open Time</label>
                                <input type="datetime-local" class="form-control" id="open_time" name="open_time" required>
                            </div>
                            <div class="mb-3">
                                <label for="close_time" class="form-label">Close Time</label>
                                <input type="datetime-local" class="form-control" id="close_time" name="close_time" required>
                            </div>
                            <div class="text-center">
                                <button type="submit" class="btn btn-primary w-100">Update Schedule</button>
                            </div>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
