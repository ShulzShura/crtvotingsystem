<?php
session_start();
include 'db_connect.php';

// Restrict access to only logged-in admins
if (!isset($_SESSION['admin_id'])) {
    echo "<script>alert('Access Denied! Please log in first.'); window.location.href='login.php';</script>";
    exit();
}

// ✅ 1. Delete all votes from the 'votes' table
$conn->query("DELETE FROM votes");

// ✅ 2. Reset 'has_voted' for all voters so they can vote again
$conn->query("UPDATE voters SET has_voted = 0");

// ✅ 3. Redirect back to the admin dashboard with a success message
echo "<script>alert('All votes have been reset successfully!'); window.location.href='admin_dashboard.php';</script>";
exit();
?>
