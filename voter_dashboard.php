<?php
session_start();
include 'db_connect.php';

// Prevent caching to block back button after logout
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

if (!isset($_SESSION['voter_id'])) {
    echo "<script>alert('Access Denied! Please log in first.'); window.location.href='login.php';</script>";
    exit();
}

$voter_id = $_SESSION['voter_id'];

// Get voter's course
$voterQuery = $conn->prepare("SELECT course FROM voters WHERE id = ?");
$voterQuery->bind_param("i", $voter_id);
$voterQuery->execute();
$voterResult = $voterQuery->get_result();
$voter = $voterResult->fetch_assoc();
$voter_course = $voter['course'] ?? "";

// Check if already voted
$checkVote = $conn->prepare("SELECT COUNT(*) AS total FROM votes WHERE voter_id = ?");
$checkVote->bind_param("i", $voter_id);
$checkVote->execute();
$voteResult = $checkVote->get_result()->fetch_assoc();
$hasVoted = $voteResult['total'] > 0;

// Get current time
date_default_timezone_set('Asia/Manila');
$current_time = date("Y-m-d H:i:s");

// Check voting schedule
$statusQuery = "SELECT status, close_time FROM schedule ORDER BY id DESC LIMIT 1";
$statusResult = $conn->query($statusQuery);
$schedule = $statusResult->fetch_assoc();
$votingStatus = ($statusResult->num_rows > 0) ? $schedule['status'] : "closed";
$close_time = $schedule['close_time'] ?? "0000-00-00 00:00:00";

if ($current_time >= $close_time) {
    unset($_SESSION['voter_id']);
    echo "<script>window.location.href = 'logout.php';</script>";
    exit();
}

// Fetch candidates
$candidatesQuery = $conn->prepare("
    SELECT * FROM candidates 
    WHERE course = ? OR position IN ('Governor', 'Vice Governor', 'Secretary', 'Treasurer', 'Auditor', 'PIO', 'PO', 
                                     'Sergeant at Arms (1)', 'Sergeant at Arms (2)', 'Muse', 'Escort', ?)
    ORDER BY FIELD(position, 'Governor', 'Vice Governor', 'Secretary', 'Treasurer', 'Auditor', 'PIO', 'PO', 
                            'Sergeant at Arms (1)', 'Sergeant at Arms (2)', 'Muse', 'Escort', 
                            'ACT Representative', 'FSM Representative', 'HRS Representative')
");
$course_rep_position = $voter_course . " Representative";
$candidatesQuery->bind_param("ss", $voter_course, $course_rep_position);
$candidatesQuery->execute();
$candidatesResult = $candidatesQuery->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Voter Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .candidate-photo {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 50%;
        }
    </style>
    <script>
        function checkVotingStatus() {
            fetch("check_voting_status.php")
                .then(response => response.json())
                .then(data => {
                    if (data.status === "closed") {
                        window.location.href = "logout.php";
                    }
                });
        }

        setInterval(checkVotingStatus, 10000);

        window.addEventListener("pageshow", function (event) {
            if (event.persisted || (window.performance && window.performance.navigation.type === 2)) {
                window.location.reload();
            }
        });
    </script>
</head>
<body class="bg-light">

    <nav class="navbar navbar-dark bg-success">
        <div class="container">
            <a class="navbar-brand" href="#">Voter Dashboard</a>
            <a href="logout.php" class="btn btn-danger">Logout</a>
        </div>
    </nav>

    <div class="container mt-4">
        <h2 class="text-center">Welcome, Voter</h2>
        <p class="text-center"><strong>Your Course:</strong> <?php echo htmlspecialchars($voter_course); ?></p>

        <div class="row">
            <div class="col-md-6 offset-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <h5 class="card-title">Voting Status</h5>
                        <p class="fw-bold text-uppercase">
                            <?php 
                                echo ($current_time >= $close_time) ? '<span class="text-danger">CLOSED</span>' : '<span class="text-success">OPEN</span>'; 
                            ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <?php if ($votingStatus == 'open' && !$hasVoted): ?>
            <div class="row mt-4">
                <div class="col-md-8 offset-md-2">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="text-center">Vote for Your Candidates</h4>
                            <form action="submit_vote.php" method="POST">
                                <?php
                                $currentPosition = "";
                                while ($row = $candidatesResult->fetch_assoc()) {
                                    if ($row['position'] !== $currentPosition) {
                                        if ($currentPosition !== "") {
                                            echo "
                                            <div class='form-check'>
                                                <input class='form-check-input' type='radio' name='vote[" . htmlspecialchars($currentPosition) . "]' value='' checked>
                                                <label class='form-check-label'>Skip</label>
                                            </div>";
                                        }
                                        echo "<h5 class='mt-3'>" . htmlspecialchars($row['position']) . "</h5>";
                                        $currentPosition = $row['position'];
                                    }
                                    $photo = !empty($row['photo']) && file_exists($row['photo']) ? $row['photo'] : 'default.jpg';
                                    echo "
                                    <div class='form-check'>
                                        <input class='form-check-input' type='radio' name='vote[" . htmlspecialchars($row['position']) . "]' value='" . $row['id'] . "'>
                                        <label class='form-check-label'>
                                            <img src='$photo' class='candidate-photo' alt='" . htmlspecialchars($row['name']) . "'>
                                            " . htmlspecialchars($row['name']) . " (" . htmlspecialchars($row['party']) . ")
                                        </label>
                                    </div>";
                                }
                                if ($currentPosition !== "") {
                                    echo "
                                    <div class='form-check'>
                                        <input class='form-check-input' type='radio' name='vote[" . htmlspecialchars($currentPosition) . "]' value='' checked>
                                        <label class='form-check-label'>Skip</label>
                                    </div>";
                                }
                                ?>
                                <div class="text-center mt-3">
                                    <button type="submit" class="btn btn-success w-100">Submit Vote</button>
                                </div>
                            </form>

                            <form action="submit_vote.php" method="POST" class="mt-2">
                                <input type="hidden" name="skip_all" value="1">
                                <button type="submit" class="btn btn-warning w-100">Skip Voting All Candidates</button>
                            </form>

                            <div class="text-center mt-3">
                                <a href="logout.php" class="btn btn-secondary w-100">Cancel Voting</a>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        <?php elseif ($hasVoted): ?>
            <div class="alert alert-info text-center mt-4">You have already voted. Contact an admin to reset your vote!</div>
        <?php else: ?>
            <div class="alert alert-danger text-center mt-4">Voting is currently closed.</div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
