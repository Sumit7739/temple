<?php
include('config.php');

$query = "SELECT expense_id, category, amount, description, expense_date FROM expenses ORDER BY expense_id DESC";
$result = $conn->query($query);

if ($result) {
    $expenses = [];
    while ($row = $result->fetch_assoc()) {
        $expenses[] = $row;
    }
    echo json_encode(['success' => true, 'data' => $expenses]);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to fetch expenses']);
}
$conn->close();
?>
