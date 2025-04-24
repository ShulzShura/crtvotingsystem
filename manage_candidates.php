<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['admin_id'])) {
    echo "<script>alert('Access Denied! Please log in first.'); window.location.href='login.php';</script>";
    exit();
}

// Add candidate
if (isset($_POST['add_candidate'])) {
    $name = $_POST['candidate_name'];
    $position = $_POST['candidate_position'];
    $course = $_POST['candidate_course'];
    $party = $_POST['candidate_party'] ? $_POST['candidate_party'] : ''; // Make party optional

    if ($position == "Representative") {
        $position = "Representative ($course)";
    } else {
        $course = "";
    }

    // Handle photo upload
    $photo = '';
    if (isset($_FILES['candidate_photo']) && $_FILES['candidate_photo']['error'] === UPLOAD_ERR_OK) {
        $photoName = basename($_FILES['candidate_photo']['name']);
        $targetDir = "candidate_photos/";
        $targetPath = $targetDir . uniqid() . "_" . $photoName;

        if (!file_exists($targetDir)) {
            mkdir($targetDir, 0777, true);
        }

        if (move_uploaded_file($_FILES['candidate_photo']['tmp_name'], $targetPath)) {
            $photo = $targetPath;
        }
    }

    $stmt = $conn->prepare("INSERT INTO candidates (name, position, course, party, photo) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $name, $position, $course, $party, $photo);
    if ($stmt->execute()) {
        echo "<script>alert('Candidate added successfully!'); window.location.href='manage_candidates.php';</script>";
    } else {
        echo "<script>alert('Error adding candidate.');</script>";
    }
}

// Delete candidate
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];

    // Delete photo file
    $photoQuery = $conn->prepare("SELECT photo FROM candidates WHERE id=?");
    $photoQuery->bind_param("i", $id);
    $photoQuery->execute();
    $photoResult = $photoQuery->get_result();
    if ($photoRow = $photoResult->fetch_assoc()) {
        if (!empty($photoRow['photo']) && file_exists($photoRow['photo'])) {
            unlink($photoRow['photo']);
        }
    }

    $stmt = $conn->prepare("DELETE FROM candidates WHERE id=?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        echo "<script>alert('Candidate deleted successfully!'); window.location.href='manage_candidates.php';</script>";
    } else {
        echo "<script>alert('Error deleting candidate.');</script>";
    }
}

$query = "SELECT * FROM candidates ORDER BY id DESC";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Candidates</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .candidate-photo {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 6px;
        }
    </style>
</head>
<body class="bg-light">
<div class="container mt-5">
    <h2 class="text-center mb-4">Manage Candidates</h2>

    <!-- Add Candidate -->
    <div class="card p-4 mb-4">
        <h5>Add New Candidate</h5>
        <form method="post" enctype="multipart/form-data" class="row g-2">
            <div class="col-md-2">
                <input type="text" name="candidate_name" class="form-control" placeholder="Name" required>
            </div>
            <div class="col-md-2">
                <select name="candidate_position" id="positionSelect" class="form-select" required>
                    <option value="">Select Position</option>
                    <option value="Governor">Governor</option>
                    <option value="Vice Governor">Vice Governor</option>
                    <option value="Secretary">Secretary</option>
                    <option value="Treasurer">Treasurer</option>
                    <option value="Auditor">Auditor</option>
                    <option value="PIO">Public Information Officer (PIO)</option>
                    <option value="PO">Peace Officer (PO)</option>
                    <option value="Sergeant at Arms (1)">Sergeant at Arms (1)</option>
                    <option value="Sergeant at Arms (2)">Sergeant at Arms (2)</option>
                    <option value="Muse">Muse</option>
                    <option value="Escort">Escort</option>
                    <option value="Representative">Representative</option>
                </select>
            </div>
            <div class="col-md-2">
                <select name="candidate_course" id="courseSelect" class="form-select" style="display: none;">
                    <option value="">Select Course</option>
                    <option value="ACT">ACT</option>
                    <option value="FSM">FSM</option>
                    <option value="HRS">HRS</option>
                </select>
            </div>
            <div class="col-md-2">
                <input type="text" name="candidate_party" class="form-control" placeholder="Party (Optional)">
            </div>
            <div class="col-md-2">
                <input type="file" name="candidate_photo" accept="image/*" class="form-control">
            </div>
            <div class="col-md-2">
                <button type="submit" name="add_candidate" class="btn btn-primary w-100">Add</button>
            </div>
        </form>
    </div>

    <!-- Search -->
    <input type="text" id="searchInput" class="form-control mb-3" placeholder="Search Candidates...">

    <!-- Candidate Table -->
    <div class="table-responsive">
        <table class="table table-bordered text-center align-middle">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Photo</th>
                    <th>Name</th>
                    <th>Position</th>
                    <th>Course</th>
                    <th>Party</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="candidateTable">
                <?php while ($row = $result->fetch_assoc()) : ?>
                    <tr>
                        <td><?= $row['id'] ?></td>
                        <td>
                            <?php if (!empty($row['photo']) && file_exists($row['photo'])) : ?>
                                <img src="<?= $row['photo'] ?>" class="candidate-photo">
                            <?php else : ?>
                                <span>No Photo</span>
                            <?php endif; ?>
                        </td>
                        <td><?= htmlspecialchars($row['name']) ?></td>
                        <td><?= htmlspecialchars($row['position']) ?></td>
                        <td><?= htmlspecialchars($row['course']) ?></td>
                        <td><?= htmlspecialchars($row['party']) ?></td>
                        <td>
                            <a href="?delete=<?= $row['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Delete this candidate?')">Delete</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <!-- Back -->
    <a href="admin_dashboard.php" class="btn btn-secondary mt-3">⬅ Back to Dashboard</a>
</div>

<script>
document.getElementById('positionSelect').addEventListener('change', function () {
    let courseSelect = document.getElementById('courseSelect');
    if (this.value === 'Representative') {
        courseSelect.style.display = 'block';
        courseSelect.setAttribute('required', 'true');
    } else {
        courseSelect.style.display = 'none';
        courseSelect.removeAttribute('required');
    }
});

document.getElementById('searchInput').addEventListener('keyup', function () {
    let filter = this.value.toLowerCase();
    let rows = document.querySelectorAll('#candidateTable tr');
    rows.forEach(row => {
        let id = row.cells[0].textContent.toLowerCase(); // Get the ID from the first column
        let name = row.cells[2].textContent.toLowerCase(); // Get the name from the third column
        let position = row.cells[3].textContent.toLowerCase(); // Get the position from the fourth column
        let course = row.cells[4].textContent.toLowerCase(); // Get the course from the fifth column
        let party = row.cells[5].textContent.toLowerCase(); // Get the party from the sixth column

        // Check if any of the columns match the search filter
        if (id.includes(filter) || name.includes(filter) || position.includes(filter) || course.includes(filter) || party.includes(filter)) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
});
</script>
</body>
</html>
