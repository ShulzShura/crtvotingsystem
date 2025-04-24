<?php
include 'db_connect.php';

date_default_timezone_set('Asia/Manila');
$current_time = date("Y-m-d H:i:s");

// Get voting status
$statusQuery = "SELECT status, close_time FROM schedule ORDER BY id DESC LIMIT 1";
$statusResult = $conn->query($statusQuery);
$schedule = $statusResult->fetch_assoc();

$close_time = $schedule['close_time'] ?? "0000-00-00 00:00:00";
$status = ($current_time >= $close_time) ? "closed" : "open";

// Return JSON response
echo json_encode(["status" => $status]);
?>
