<?php
// Database connection
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include("config.php");

// Check connection
if ($conn->connect_error) {
    die(json_encode(["success" => false, "message" => "Database connection failed: " . $conn->connect_error]));
}

// Handle POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Collect and sanitize data
    $donorName = isset($_POST['donorName']) ? trim($_POST['donorName']) : null;
    $mobileNumber = isset($_POST['mobileNumber']) ? trim($_POST['mobileNumber']) : null;
    $email = isset($_POST['email']) ? trim($_POST['email']) : null;
    $address = isset($_POST['address']) ? trim($_POST['address']) : null;
    $donationAmount = isset($_POST['donationAmount']) ? trim($_POST['donationAmount']) : null;
    $paymentMethod = isset($_POST['paymentMethod']) ? trim($_POST['paymentMethod']) : null;
    $transactionReference = isset($_POST['transactionReference']) ? trim($_POST['transactionReference']) : null;
    $donationDate = isset($_POST['donationDate']) ? trim($_POST['donationDate']) : null;
    $remarks = isset($_POST['remarks']) ? trim($_POST['remarks']) : null;

    // Validate required fields
    if (!$donorName || !$mobileNumber || !$donationAmount || !$paymentMethod || !$donationDate) {
        echo json_encode(["success" => false, "message" => "Please fill in all required fields."]);
        exit();
    }

    // Sanitize and format optional fields
    $email = !empty($email) ? $email : null;
    $transactionReference = !empty($transactionReference) ? $transactionReference : null;
    $remarks = !empty($remarks) ? $remarks : null;

    // Ensure donationAmount is a valid number
    if (!is_numeric($donationAmount)) {
        echo json_encode(["success" => false, "message" => "Donation amount must be a valid number."]);
        exit();
    }

    // Prepare and bind statement
    $stmt = $conn->prepare("INSERT INTO donations (donor_name, mobile_number, email, address, donation_amount, payment_method, transaction_reference, donation_date, remarks) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    if ($stmt === false) {
        echo json_encode(["success" => false, "message" => "SQL prepare error: " . $conn->error]);
        exit();
    }

    $stmt->bind_param("ssssdssss", $donorName, $mobileNumber, $email, $address, $donationAmount, $paymentMethod, $transactionReference, $donationDate, $remarks);

    // Execute and send response
    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "Donation record saved successfully."]);
    } else {
        echo json_encode(["success" => false, "message" => "Failed to save record: " . $stmt->error]);
    }

    $stmt->close();
} else {
    echo json_encode(["success" => false, "message" => "Invalid request method."]);
}

$conn->close();
