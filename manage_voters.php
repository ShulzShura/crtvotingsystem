<?php
session_start();
include 'db_connect.php';

// Restrict access to only logged-in admins
if (!isset($_SESSION['admin_id'])) {
    echo "<script>alert('Access Denied! Please log in first.'); window.location.href='login.php';</script>";
    exit();
}

// Add a new voter
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_voter'])) {
    $username = $_POST['new_username'];
    $password = $_POST['new_password'];
    $course = $_POST['new_course'];

    $stmt = $conn->prepare("INSERT INTO voters (username, password, course) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $username, $password, $course);

    if ($stmt->execute()) {
        echo "<script>alert('Voter added successfully!'); window.location.href='manage_voters.php';</script>";
    } else {
        echo "<script>alert('Error adding voter.');</script>";
    }
}

// Update voter details
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_voter'])) {
    $id = $_POST['id'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $course = $_POST['course'];

    $stmt = $conn->prepare("UPDATE voters SET username = ?, password = ?, course = ? WHERE id = ?");
    $stmt->bind_param("sssi", $username, $password, $course, $id);

    if ($stmt->execute()) {
        echo "<script>alert('Voter updated successfully!'); window.location.href='manage_voters.php';</script>";
    } else {
        echo "<script>alert('Error updating voter.');</script>";
    }
}

// Delete voter
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];

    $stmt = $conn->prepare("DELETE FROM voters WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo "<script>alert('Voter deleted successfully!'); window.location.href='manage_voters.php';</script>";
    } else {
        echo "<script>alert('Error deleting voter.');</script>";
    }
}

// Reset vote for a specific voter
if (isset($_GET['reset_vote'])) {
    $id = $_GET['reset_vote'];

    $deleteVotes = $conn->prepare("DELETE FROM votes WHERE voter_id = ?");
    $deleteVotes->bind_param("i", $id);
    $deleteVotes->execute();

    $resetVoter = $conn->prepare("UPDATE voters SET has_voted = 0 WHERE id = ?");
    $resetVoter->bind_param("i", $id);
    
    if ($resetVoter->execute()) {
        echo "<script>alert('Vote reset successfully!'); window.location.href='manage_voters.php';</script>";
    } else {
        echo "<script>alert('Error resetting vote.');</script>";
    }
}

// Fetch voters
$votersQuery = "SELECT * FROM voters ORDER BY id DESC";
$votersResult = $conn->query($votersQuery);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Voters</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-4">
        <h2 class="text-center mb-4">Manage Voters</h2>

        <div class="d-flex justify-content-start mb-3">
            <a href="admin_dashboard.php" class="btn btn-secondary">⬅ Back to Dashboard</a>
        </div>

        <!-- Add Voter Form -->
        <div class="card mb-3">
            <div class="card-header bg-primary text-white">Add New Voter</div>
            <div class="card-body">
                <form method="post">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <input type="text" name="new_username" class="form-control" placeholder="Username" required>
                        </div>
                        <div class="col-md-4">
                            <input type="text" name="new_password" class="form-control" placeholder="Password" required>
                        </div>
                        <div class="col-md-3">
                            <select name="new_course" class="form-control" required>
                                <option value="" disabled selected>Select Course</option>
                                <option value="ACT">ACT</option>
                                <option value="FSM">FSM</option>
                                <option value="HRS">HRS</option>
                            </select>
                        </div>
                        <div class="col-md-1">
                            <button type="submit" name="add_voter" class="btn btn-success w-100">Add</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Search Bar -->
        <div class="mb-3">
            <input type="text" id="searchInput" class="form-control" placeholder="Search by ID, Username, or Course...">
        </div>

        <!-- Voters Table -->
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Username</th>
                        <th>Password</th>
                        <th>Course</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="votersTable">
                    <?php while ($voter = $votersResult->fetch_assoc()) : ?>
                        <tr>
                            <form method="post">
                                <td><?php echo $voter['id']; ?></td>
                                <td>
                                    <input type="text" name="username" value="<?php echo htmlspecialchars($voter['username']); ?>" class="form-control" required>
                                </td>
                                <td>
                                    <input type="text" name="password" value="<?php echo htmlspecialchars($voter['password']); ?>" class="form-control" required>
                                </td>
                                <td>
                                    <select name="course" class="form-control" required>
                                        <option value="ACT" <?php if ($voter['course'] == 'ACT') echo 'selected'; ?>>ACT</option>
                                        <option value="FSM" <?php if ($voter['course'] == 'FSM') echo 'selected'; ?>>FSM</option>
                                        <option value="HRS" <?php if ($voter['course'] == 'HRS') echo 'selected'; ?>>HRS</option>
                                    </select>
                                </td>
                                <td>
                                    <input type="hidden" name="id" value="<?php echo $voter['id']; ?>">
                                    <button type="submit" name="update_voter" class="btn btn-sm btn-primary">Save</button>
                                    <a href="manage_voters.php?delete=<?php echo $voter['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?');">Delete</a>
                                    <a href="manage_voters.php?reset_vote=<?php echo $voter['id']; ?>" class="btn btn-sm btn-warning" onclick="return confirm('Reset this voter\'s vote?');">Reset Vote</a>
                                </td>
                            </form>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        // Search filter (ID, Username, and Course)
        document.getElementById('searchInput').addEventListener('keyup', function () {
            let filter = this.value.toLowerCase();
            let rows = document.querySelectorAll('#votersTable tr');

            rows.forEach(row => {
                let id = row.cells[0].textContent.toLowerCase();
                let username = row.cells[1].querySelector('input').value.toLowerCase();
                let course = row.cells[3].querySelector('select').value.toLowerCase();

                row.style.display = (id.includes(filter) || username.includes(filter) || course.includes(filter)) ? '' : 'none';
            });
        });
    </script>
</body>
</html>
