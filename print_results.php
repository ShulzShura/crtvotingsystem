<?php
session_start();
include 'db_connect.php';

// Restrict access to only logged-in admins
if (!isset($_SESSION['admin_id'])) {
    echo "<script>alert('Access Denied! Please log in first.'); window.location.href='login.php';</script>";
    exit();
}

date_default_timezone_set('Asia/Manila');

// Fetch results including the party list
$query = "SELECT candidates.name, candidates.position, candidates.party, COUNT(votes.id) AS vote_count 
          FROM candidates 
          LEFT JOIN votes ON candidates.id = votes.candidate_id 
          GROUP BY candidates.id 
          ORDER BY candidates.position, vote_count DESC";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print Voting Results</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        @media print {
            .no-print { display: none; }
        }
    </style>
</head>
<body class="bg-light">
    <div class="container mt-4">
        <h2 class="text-center">Voting Results</h2>
        <p class="text-center"><?php echo date("F d, Y H:i:s"); ?></p>

        <table class="table table-bordered">
            <thead class="table-dark">
                <tr>
                    <th>Candidate</th>
                    <th>Position</th>
                    <th>Party List</th>
                    <th>Votes</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()) : ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['name']); ?></td>
                        <td><?php echo htmlspecialchars($row['position']); ?></td>
                        <td><?php echo htmlspecialchars($row['party']); ?></td>
                        <td><?php echo $row['vote_count']; ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <div class="text-center no-print">
            <button class="btn btn-primary" onclick="window.print()">Print</button>
            <a href="results.php" class="btn btn-secondary">Back</a>
        </div>
    </div>
</body>
</html>
