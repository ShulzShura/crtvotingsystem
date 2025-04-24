<?php
session_start();
include 'db_connect.php';

// Restrict access to only logged-in admins
if (!isset($_SESSION['admin_id'])) {
    echo "<script>alert('Access Denied! Please log in first.'); window.location.href='login.php';</script>";
    exit();
}

// Get the logged-in admin ID
$loggedInAdmin = $_SESSION['admin_id'];

// Handle Add Admin
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_admin'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $conn->query("INSERT INTO admins (username, password) VALUES ('$username', '$password')");
}

// Handle Delete Admin (Prevent self-delete)
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    if ($id != $loggedInAdmin) {
        $conn->query("DELETE FROM admins WHERE id = $id");
    }
    header("Location: manage_admins.php");
    exit();
}

// Handle Inline Editing (No Extra File)
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_admin'])) {
    $id = $_POST['id'];
    $column = $_POST['column'];
    $value = $_POST['value'];
    $conn->query("UPDATE admins SET $column = '$value' WHERE id = $id");
    exit();
}

// Fetch Admins
$admins = $conn->query("SELECT * FROM admins ORDER BY id");

// Fetch Active Sessions Count per Admin
$adminSessions = [];
$result = $conn->query("SELECT admin_id, COUNT(*) as device_count FROM admin_sessions GROUP BY admin_id");
while ($row = $result->fetch_assoc()) {
    $adminSessions[$row['admin_id']] = $row['device_count'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Admins</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body class="bg-light">
    <div class="container mt-4">
        <h2 class="text-center mb-4">Manage Admins</h2>

        <!-- Add Admin Form -->
        <div class="card p-3 mb-3 shadow-sm">
            <h5>Add Admin</h5>
            <form method="post" class="row g-2">
                <div class="col-md-5">
                    <input type="text" name="username" class="form-control" placeholder="Username" required>
                </div>
                <div class="col-md-5">
                    <input type="password" name="password" class="form-control" placeholder="Password" required>
                </div>
                <div class="col-md-2">
                    <button type="submit" name="add_admin" class="btn btn-primary w-100">Add</button>
                </div>
            </form>
        </div>

        <!-- Search Bar -->
        <input type="text" id="search" class="form-control mb-3" placeholder="Search Admins...">

        <!-- Admins Table -->
        <div class="card p-3 shadow-sm">
            <table class="table table-bordered text-center">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Username</th>
                        <th>Password</th>
                        <th>Active Devices</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="adminTable">
                    <?php while ($row = $admins->fetch_assoc()) : ?>
                        <tr>
                            <td><?php echo $row['id']; ?></td>
                            <td contenteditable="true" onBlur="updateAdmin(this, 'username', <?php echo $row['id']; ?>)"><?php echo $row['username']; ?></td>
                            <td contenteditable="true" onBlur="updateAdmin(this, 'password', <?php echo $row['id']; ?>)"><?php echo $row['password']; ?></td>
                            <td>
                                <?php echo isset($adminSessions[$row['id']]) ? $adminSessions[$row['id']] . ' device(s)' : '0'; ?>
                            </td>
                            <td>
                                <?php if ($row['id'] != $loggedInAdmin) : ?>
                                    <a href="manage_admins.php?delete=<?php echo $row['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?');">Delete</a>
                                <?php else : ?>
                                    <button class="btn btn-secondary btn-sm" disabled>Can't Delete Self</button>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

        <a href="admin_dashboard.php" class="btn btn-secondary mt-3">Back to Dashboard</a>
    </div>

    <script>
        // Live Search
        $("#search").on("keyup", function () {
            var value = $(this).val().toLowerCase();
            $("#adminTable tr").filter(function () {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
            });
        });

        // Inline Edit & Save
        function updateAdmin(element, column, id) {
            var newValue = element.innerText;
            $.post("manage_admins.php", {
                update_admin: true,
                id: id,
                column: column,
                value: newValue
            }, function (response) {
                console.log("Updated:", response);
            });
        }
    </script>
</body>
</html>
