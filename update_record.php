<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include("config.php");

// Check database connection
if ($conn->connect_error) {
    die(json_encode(["success" => false, "message" => "Database connection failed: " . $conn->connect_error]));
}

// Handle POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Collect and sanitize data
    $id = isset($_POST['id']) ? intval($_POST['id']) : null;
    $donorName = isset($_POST['donorName']) ? trim($_POST['donorName']) : null;
    $mobileNumber = isset($_POST['mobileNumber']) ? trim($_POST['mobileNumber']) : null;
    $email = isset($_POST['email']) ? trim($_POST['email']) : null;
    $address = isset($_POST['address']) ? trim($_POST['address']) : null;
    $donationAmount = isset($_POST['donationAmount']) ? trim($_POST['donationAmount']) : null;
    $paymentMethod = isset($_POST['paymentMethod']) ? trim($_POST['paymentMethod']) : null;
    $transactionReference = isset($_POST['transactionReference']) ? trim($_POST['transactionReference']) : null;
    // $donationDate = isset($_POST['donationDate']) ? trim($_POST['donationDate']) : null;
    $remarks = isset($_POST['remarks']) ? trim($_POST['remarks']) : null;
    $status = isset($_POST['status']) ? trim($_POST['status']) : null;
    $purpose = isset($_POST['purpose']) ? trim($_POST['purpose']) : null;
    $isanonymous = isset($_POST['isanonymous']) ? trim($_POST['isanonymous']) : null;
    $acknowledgment_sent = isset($_POST['acknowledgment_sent']) ? trim($_POST['acknowledgment_sent']) : null;


    // Validate required fields
    if (!$id || !$donorName || !$mobileNumber || !$donationAmount || !$paymentMethod) {
        echo json_encode(["success" => false, "message" => "Please fill in all required fields."]);
        exit();
    }

    // Validate donation amount as a valid number
    if (!is_numeric($donationAmount)) {
        echo json_encode(["success" => false, "message" => "Donation amount must be a valid number."]);
        exit();
    }

    // Sanitize and format optional fields
    $email = !empty($email) ? $email : null;
    $transactionReference = !empty($transactionReference) ? $transactionReference : null;
    $remarks = !empty($remarks) ? $remarks : null;



    // Prepare and bind the SQL statement
    $stmt = $conn->prepare("UPDATE donations SET donor_name = ?, mobile_number = ?, email = ?, address = ?, donation_amount = ?, payment_method = ?, transaction_reference = ?, remarks = ?, status = ?, purpose = ?, isanonymous = ?, acknowledgment_sent = ? WHERE id = ?");
    if ($stmt === false) {
        echo json_encode(["success" => false, "message" => "SQL prepare error: " . $conn->error]);
        exit();
    }

    // Bind parameters to the statement
    $stmt->bind_param("ssssssssssssi",  $donorName, $mobileNumber, $email, $address, $donationAmount, $paymentMethod, $transactionReference, $remarks, $status, $purpose, $isanonymous, $acknowledgment_sent, $id);

    // Execute the statement
    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "Record updated successfully."]);
    } else {
        echo json_encode(["success" => false, "message" => "Failed to update record: " . $stmt->error]);
        error_log("Received date: " . $donationDate);
        error_log("Executing query with date: " . $donationDate);
    }

    // Close the statement and connection
    $stmt->close();
    $conn->close();
} else {
    echo json_encode(["success" => false, "message" => "Invalid request method."]);
}
