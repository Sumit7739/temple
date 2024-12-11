<?php
header('Content-Type: application/json');

// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Include database configuration
include('config.php');

// Query to calculate total expenses
$query = "SELECT SUM(amount) AS total_expenses FROM expenses";
$result = $conn->query($query);

$response = [];

if ($result) {
    $row = $result->fetch_assoc();
    $response['success'] = true;
    $response['total_expenses'] = $row['total_expenses'] ?? 0; // Default to 0 if null
} else {
    $response['success'] = false;
    $response['message'] = "Error fetching data: " . $conn->error;
}

// Return JSON response
echo json_encode($response);
$conn->close();
?>
