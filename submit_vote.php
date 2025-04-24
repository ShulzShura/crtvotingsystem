<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['voter_id'])) {
    echo "<script>alert('Access Denied! Please log in first.'); window.location.href='login.php';</script>";
    exit();
}

$voter_id = $_SESSION['voter_id'];

// Handle skip all
if (isset($_POST['skip_all'])) {
    // Log voter out after skipping all votes
    $updateVoteStatus = $conn->prepare("UPDATE voters SET has_voted = 1 WHERE id = ?");
    $updateVoteStatus->bind_param("i", $voter_id);
    $updateVoteStatus->execute();

    // Delete all votes for this voter
    $deleteAll = $conn->prepare("DELETE FROM votes WHERE voter_id = ?");
    $deleteAll->bind_param("i", $voter_id);
    $deleteAll->execute();

    echo "<script>alert('You have skipped voting for all candidates.'); window.location.href='logout.php';</script>";
    exit();
}

if (!isset($_POST['vote']) || !is_array($_POST['vote'])) {
    echo "<script>alert('No votes submitted.'); window.location.href='voter_dashboard.php';</script>";
    exit();
}

$votes = $_POST['vote'];

// Process votes by actual submitted keys
foreach ($votes as $position => $candidate_id) {
    if (empty($candidate_id)) {
        // Voter skipped this position; delete previous vote if any
        $deleteVote = $conn->prepare("DELETE FROM votes WHERE voter_id = ? AND position = ?");
        $deleteVote->bind_param("is", $voter_id, $position);
        $deleteVote->execute();
        continue;
    }

    // Validate the candidate belongs to the correct position
    $candidateQuery = $conn->prepare("SELECT position FROM candidates WHERE id = ?");
    $candidateQuery->bind_param("i", $candidate_id);
    $candidateQuery->execute();
    $candidateResult = $candidateQuery->get_result();

    if ($candidateResult->num_rows === 0) {
        continue; // Skip invalid candidate
    }

    $candidate = $candidateResult->fetch_assoc();
    if ($candidate['position'] !== $position) {
        continue; // Candidate position mismatch
    }

    // Delete any existing vote for this position
    $deleteVote = $conn->prepare("DELETE FROM votes WHERE voter_id = ? AND position = ?");
    $deleteVote->bind_param("is", $voter_id, $position);
    $deleteVote->execute();

    // Insert new vote
    $insertVote = $conn->prepare("INSERT INTO votes (voter_id, candidate_id, position) VALUES (?, ?, ?)");
    $insertVote->bind_param("iis", $voter_id, $candidate_id, $position);
    $insertVote->execute();
}

echo "<script>alert('Your vote has been submitted successfully.'); window.location.href='voter_dashboard.php';</script>";
exit();
?>
