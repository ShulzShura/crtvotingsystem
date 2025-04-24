<?php
session_start();
include 'db_connect.php'; // Required to access the database

// Remove this session from active sessions
$session_id = session_id();
$conn->query("DELETE FROM admin_sessions WHERE session_id = '$session_id'");

// Clear all session data
session_unset();
session_destroy();

// Prevent browser back button from loading cached login
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

// Redirect to login page
header("Location: login.php");
exit();
?>
