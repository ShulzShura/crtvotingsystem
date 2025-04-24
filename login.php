<?php
session_start();
include 'db_connect.php';

$error = "";

// Prevent back-button cached login
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Check if user is an admin
    $adminQuery = "SELECT * FROM admins WHERE username = '$username' AND password = '$password'";
    $adminResult = $conn->query($adminQuery);

    if ($adminResult->num_rows > 0) {
        $admin = $adminResult->fetch_assoc();
        $_SESSION['admin_id'] = $admin['id'];

        // Insert session info into admin_sessions table
        $session_id = session_id();
        $admin_id = $admin['id'];
        $insertSession = "INSERT INTO admin_sessions (session_id, admin_id) VALUES ('$session_id', $admin_id)
                  ON DUPLICATE KEY UPDATE admin_id = VALUES(admin_id)";

        $conn->query($insertSession);

        header("Location: admin_dashboard.php");
        exit();
    }

    // Check voting status before allowing voter login
    $statusQuery = "SELECT status FROM schedule ORDER BY id DESC LIMIT 1";
    $statusResult = $conn->query($statusQuery);
    $votingStatus = ($statusResult->num_rows > 0) ? $statusResult->fetch_assoc()['status'] : 'closed';

    // Check if user is a voter
    $voterQuery = "SELECT * FROM voters WHERE username = '$username' AND password = '$password'";
    $voterResult = $conn->query($voterQuery);

    if ($voterResult->num_rows > 0) {
        if ($votingStatus !== 'open') {
            echo "<script>
                alert('Voting is currently closed!');
                window.location='login.php';
            </script>";
            exit();
        }

        $voter = $voterResult->fetch_assoc();
        if ($voter['has_voted'] == 1) {
            echo "<script>
                alert('You have already voted! Contact the admin to reset your vote.');
                window.location='login.php';
            </script>";
            exit();
        }

        $_SESSION['voter_id'] = $voter['id'];
        header("Location: voter_dashboard.php");
        exit();
    } else {
        $error = "Invalid username or password!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
    <meta http-equiv="Pragma" content="no-cache" />
    <meta http-equiv="Expires" content="0" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Voting System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light d-flex align-items-center justify-content-center" style="height: 100vh;">

    <div class="card p-4 shadow-sm" style="width: 350px;">
        <h3 class="text-center mb-3">Login</h3>
        
        <?php if (!empty($error)) : ?>
            <div class="alert alert-danger text-center p-2"><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="post" autocomplete="off">
            <div class="mb-3">
                <label class="form-label">Username</label>
                <input type="text" name="username" class="form-control" autocomplete="off" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control" autocomplete="new-password" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Login</button>
        </form>
    </div>

    <!-- Optional JS: Reload if accessed via browser history -->
    <script>
        if (window.performance && window.performance.navigation.type === window.performance.navigation.TYPE_BACK_FORWARD) {
            window.location.reload();
        }
    </script>

</body>
</html>
