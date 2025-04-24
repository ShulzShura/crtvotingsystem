<?php
session_start();
include 'db_connect.php';

// Ensure admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Voting Results</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-4">
        <h2 class="text-center">Voting Results</h2>
        <div class="d-flex justify-content-between mb-3">
            <a href="admin_dashboard.php" class="btn btn-secondary">⬅ Back to Dashboard</a>
            <a href="print_results.php" class="btn btn-primary" target="_blank">🖨 Print Results</a>
        </div>

        <table class="table table-bordered table-striped">
            <thead class="table-dark">
                <tr>
                    <th>Candidate</th>
                    <th>Position</th>
                    <th>Party List</th>
                    <th>Votes</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Fetch vote counts with party list
                $query = "SELECT candidates.name, candidates.position, candidates.party, COUNT(votes.id) AS vote_count 
                          FROM candidates 
                          LEFT JOIN votes ON candidates.id = votes.candidate_id 
                          GROUP BY candidates.id 
                          ORDER BY candidates.position, vote_count DESC";
                $result = $conn->query($query);

                while ($row = $result->fetch_assoc()) {
                    echo "<tr>
                            <td>{$row['name']}</td>
                            <td>{$row['position']}</td>
                            <td>{$row['party']}</td>
                            <td>{$row['vote_count']}</td>
                          </tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</body>
</html>
