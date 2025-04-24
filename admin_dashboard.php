<?php
session_start();
include 'db_connect.php';

// Prevent caching
header("Expires: Tue, 01 Jan 2000 00:00:00 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

// Restrict access to only logged-in admins
if (!isset($_SESSION['admin_id'])) {
    echo "<script>alert('Access Denied! Please log in first.'); window.location.href='login.php';</script>";
    exit();
}
$admin_id = $_SESSION['admin_id'];
// Count how many active sessions this admin has
$count_query = $conn->query("SELECT COUNT(*) as device_count FROM admin_sessions WHERE admin_id = $admin_id");
$device_count = 0;
if ($count_query && $row = $count_query->fetch_assoc()) {
    $device_count = $row['device_count'];
}

// Automatically update voting status based on time
date_default_timezone_set('Asia/Manila');
$current_time = date("Y-m-d H:i:s");

$scheduleQuery = "SELECT * FROM schedule ORDER BY id DESC LIMIT 1";
$scheduleResult = $conn->query($scheduleQuery);

if ($scheduleResult->num_rows > 0) {
    $schedule = $scheduleResult->fetch_assoc();
    $close_time = $schedule['close_time'];

    if ($current_time >= $close_time && $schedule['status'] !== 'closed') {
        $conn->query("UPDATE schedule SET status = 'closed' WHERE id = " . $schedule['id']);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="cache-control" content="no-store" />
    <meta http-equiv="pragma" content="no-cache" />
    <meta http-equiv="expires" content="0" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Reload page if accessed from back/forward cache -->
    <script>
        window.addEventListener('pageshow', function (event) {
            if (event.persisted || (window.performance && window.performance.navigation.type === 2)) {
                window.location.reload();
            }
        });
    </script>
</head>
<body class="bg-light">

    <nav class="navbar navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="#">Admin Dashboard</a>
            <a href="logout.php" class="btn btn-danger">Logout</a>
        </div>
    </nav>

    <div class="container mt-4">
        <h2 class="text-center">Welcome, Admin</h2>

        <!-- Voting Status -->
        <div class="row">
            <div class="col-md-6 offset-md-3">
                <div class="card">
                    <div class="card-body text-center">
                        <h5 class="card-title">Voting Status</h5>
                        <?php
                        $statusResult = $conn->query("SELECT status FROM schedule ORDER BY id DESC LIMIT 1");
                        if ($statusResult->num_rows > 0) {
                            $statusRow = $statusResult->fetch_assoc();
                            echo "<p class='fw-bold text-uppercase'>" . htmlspecialchars($statusRow['status']) . "</p>";
                        } else {
                            echo "<p>Status not set.</p>";
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Manage Buttons -->
        <div class="row text-center mt-4">
            <div class="col-md-4">
                <a href="manage_voters.php" class="btn btn-primary w-100">Manage Voters</a>
            </div>
            <div class="col-md-4">
                <a href="manage_candidates.php" class="btn btn-primary w-100">Manage Candidates</a>
            </div>
            <div class="col-md-4">
                <a href="manage_admins.php" class="btn btn-primary w-100">Manage Admins</a>
            </div>
        </div>

        <div class="row text-center mt-3">
            <div class="col-md-6">
                <a href="schedule.php" class="btn btn-secondary w-100">Voting Schedule</a>
            </div>
            <div class="col-md-6">
                <a href="reset_votes.php" class="btn btn-danger w-100" onclick="return confirm('Are you sure you want to reset all votes?');">Reset All Votes</a>
            </div>
        </div>

        <div class="row text-center mt-3">
            <div class="col-md-6">
                <a href="results.php" class="btn btn-success w-100">View Results</a>
            </div>
            <div class="col-md-6">
                <a href="print_results.php" class="btn btn-success w-100" target="_blank">Print Results</a>
            </div>
        </div>

        <!-- Voter and Candidate Counts -->
        <div class="row mt-4">
            <div class="col-md-6">
                <h5 class="text-center">Voter Count by Course</h5>
                <ul class="list-group">
                    <?php
                    $courses = ['ACT', 'FSM', 'HRS'];
                    foreach ($courses as $course) {
                        $voterCountQuery = "SELECT COUNT(*) AS count FROM voters WHERE course = '$course'";
                        $voterCountResult = $conn->query($voterCountQuery);
                        $count = $voterCountResult->fetch_assoc()['count'];
                        echo "<li class='list-group-item'>$course: <strong>$count voters</strong></li>";
                    }
                    ?>
                </ul>
            </div>

            <div class="col-md-6">
                <h5 class="text-center">Candidate Count by Course</h5>
                <ul class="list-group">
                    <?php
                    foreach ($courses as $course) {
                        $candidateCountQuery = "SELECT COUNT(*) AS count FROM candidates WHERE course = '$course'";
                        $candidateCountResult = $conn->query($candidateCountQuery);
                        $count = $candidateCountResult->fetch_assoc()['count'];
                        echo "<li class='list-group-item'>$course: <strong>$count candidates</strong></li>";
                    }
                    ?>
                </ul>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
