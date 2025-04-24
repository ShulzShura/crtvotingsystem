<?php
session_start();
include 'db_connect.php';

// Ensure admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: index.php");
    exit();
}

// Handle search
$search_query = "";
if (isset($_GET['query'])) {
    $search_query = $_GET['query'];
    $stmt = $conn->prepare("SELECT * FROM voters WHERE id LIKE ? OR username LIKE ?");
    $search_term = "%" . $search_query . "%";
    $stmt->bind_param("ss", $search_term, $search_term);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $result = $conn->query("SELECT * FROM voters");
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Voters</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h2>Search Voters</h2>
    
    <form method="GET">
        <input type="text" name="query" placeholder="Enter Voter ID or Username" value="<?= htmlspecialchars($search_query) ?>">
        <button type="submit">Search</button>
    </form>

    <table border="1">
        <tr>
            <th>Voter ID</th>
            <th>Username</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($row['id']) ?></td>
                <td><?= htmlspecialchars($row['username']) ?></td>
            </tr>
        <?php endwhile; ?>
    </table>

    <br>
    <a href="admin_dashboard.php">Back to Dashboard</a>
</body>
</html>
