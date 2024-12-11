<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Include database configuration
include('config.php');

// Fetch expenses grouped by category
$sql = "SELECT category, SUM(amount) AS total FROM expenses GROUP BY category";
$result = $conn->query($sql);

if ($result) {
    $categories = [];
    $amounts = [];

    while ($row = $result->fetch_assoc()) {
        $categories[] = $row['category'];
        $amounts[] = $row['total'];
    }

    echo json_encode([
        'success' => true,
        'categories' => $categories,
        'amounts' => $amounts
    ]);
} else {
    echo json_encode(['success' => false, 'message' => $conn->error]);
}

$conn->close();
?>
