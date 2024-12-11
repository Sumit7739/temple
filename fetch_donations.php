<?php
// Database connection
include("config.php");

// Check connection
if (!$conn) {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'message' => 'Database connection failed: ' . mysqli_connect_error()
    ]);
    exit();
}

// Query to fetch all donation records
$query = "SELECT * FROM donations ORDER BY id DESC";
$result = mysqli_query($conn, $query);

if ($result) {
    $donations = mysqli_fetch_all($result, MYSQLI_ASSOC);

    // Return data as JSON
    header('Content-Type: application/json');
    echo json_encode([
        'success' => true,
        'data' => $donations
    ]);
} else {
    // Handle query failure
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'message' => 'Query error: ' . mysqli_error($conn)
    ]);
}

// Close the database connection
mysqli_close($conn);
