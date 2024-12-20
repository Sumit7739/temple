<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Include database configuration
include('config.php');

// Fetch the total donation amount and count
$sql = "SELECT SUM(donation_amount) AS total_donations, COUNT(id) AS total_count FROM donations"; // COUNT(id) for the total number of donations
$result = $conn->query($sql);

if ($result) {
    $row = $result->fetch_assoc();
    $total_donations = $row['total_donations'] ?? 0; // Default to 0 if null
    $total_count = $row['total_count'] ?? 0; // Default to 0 if null
    echo json_encode([
        'success' => true,
        'total_donations' => $total_donations,
        'total_count' => $total_count,
    ]);
} else {
    echo json_encode(['success' => false, 'message' => $conn->error]);
}

$conn->close();
?>
