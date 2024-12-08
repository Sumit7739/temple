<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Debugging: Output received data
error_log("Received data: " . print_r($_POST, true));

// Include database configuration
include('config.php');

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Collect form data
    $category = $_POST['category'];
    $amount = $_POST['amount'];
    $description = $_POST['description'];

    // Validate and sanitize date
    if (isset($_POST['date']) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $_POST['date'])) {
        $expense_date = $_POST['date'];
    } else {
        echo "Invalid date format. Please use YYYY-MM-DD.";
        exit;
    }

    // Debugging: Log date
    error_log("Validated date: " . $expense_date);

    // Get current timestamps
    $current_timestamp = date('Y-m-d H:i:s');

    // Handle attachment upload
    $attachment = null;
    if (isset($_FILES['attachment']) && $_FILES['attachment']['error'] == 0) {
        $upload_dir = 'uploads/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        $file_name = uniqid() . '_' . basename($_FILES['attachment']['name']);
        $target_file = $upload_dir . $file_name;
        if (move_uploaded_file($_FILES['attachment']['tmp_name'], $target_file)) {
            $attachment = $file_name;
        } else {
            echo "Error uploading file.";
            exit;
        }
    }

    // Prepare SQL query
    $sql = "INSERT INTO expenses (category, amount, description, expense_date, attachment, created_at, updated_at)
            VALUES (?, ?, ?, ?, ?, ?, ?)";

    // Debugging: Log query
    error_log("SQL Query: $sql");

    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        echo "Error preparing statement: " . $conn->error;
        exit;
    }

    // Bind parameters and execute
    $stmt->bind_param('sdsssss', $category, $amount, $description, $expense_date, $attachment, $current_timestamp, $current_timestamp);

    if ($stmt->execute()) {
        // Success response with redirection
        echo "
        <div style='padding: 15px; background-color: #d4edda; border: 1px solid #c3e6cb; border-radius: 5px; color: #155724;'>
            <strong>Success:</strong> Expense added successfully! You will be redirected in 1 seconds...
        </div>
        <script>
            setTimeout(function() {
                window.location.href = 'add_expenses.php'; // Change to the desired redirect URL
            },1000);
        </script>
        ";
    } else {
        // Debugging: Log errors
        error_log("Execution Error: " . $stmt->error);
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>
