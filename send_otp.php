<?php

use PHPMailer\PHPMailer\PHPMailer;

session_start();
require 'PHPMailer-master/src/PHPMailer.php';
require 'PHPMailer-master/src/SMTP.php';
require 'PHPMailer-master/src/Exception.php';


// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userEmail = $_POST['email']; // Retrieve the entered email

    include('config.php');

    // Check if the email is present in the database and has verification status 0
    $sql = "SELECT * FROM users WHERE email = ? AND verification_status = 1";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $userEmail);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        // User email found and verification status is 0

        // Generate a random 6-digit OTP
        $otp = mt_rand(100000, 999999);

        // Update the database with the new OTP
        $updateSql = "UPDATE users SET otp = ? WHERE email = ?";
        $updateStmt = $conn->prepare($updateSql);
        $updateStmt->bind_param("ss", $otp, $userEmail);
        if ($updateStmt->execute()) {
            // OTP updated successfully

            // Send the OTP to the entered email

            $mail = new PHPMailer();

            // SMTP configuration
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->Port = 587;
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->SMTPAuth = true;
            $mail->Username = 'srisinhasumit10@gmail.com'; // Your email address
            $mail->Password = 'ggtbuofjfdmqcohr'; // Your email password

            $mail->setFrom('PPWALA@gmail.com', 'PPWALA'); // Sender email and name

            $mail->addAddress($userEmail); // Recipient's email

            $mail->isHTML(true);
            $mail->isHTML(true);
            $mail->Subject = 'Reset Your PPWALA Account Password'; // Personalized subject line

            $body = "
<html>
<head>
  <title>Reset Your PPWALA Account Password</title>
</head>
<body>
  <p>Hi there,</p>
  <p>We received a request to reset your password for your PPWALA account. To complete the reset process, please use the following One-Time Password (OTP):</p>
  <p style='font-weight: bold;'>" . $otp . "</p>
  <p>This code is valid for 5 minutes and will allow you to create a new password for your account.</p>
  <p>If you didn't request a password reset, please ignore this email. Your account remains secure.</p>
  <p>Thanks,<br />The PPWALA Team</p>
</body>
</html>";

            $mail->Body = $body;


            if ($mail->send()) {
                // OTP sent successfully
                $_SESSION['email'] = $userEmail; // Store the email in the session for further verification
                header('Location: otp_verification_2.php?email=' . $userEmail); // Redirect to OTP verification page
                exit();
            } else {
                $error = 'Error sending email: ' . $mail->ErrorInfo;
            }
        } else {
            $error = 'Error updating OTP: ' . $conn->error;
        }

        // Close the update statement
        $updateStmt->close();
    } else {
        // User not found or verification status is not 0
        $error = "Email Not verified.";
    }

    // Close database connections and statements
    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="image/icon.ico" type="image/x-icon" sizes="32x32">
    <link rel="stylesheet" href="send_otp.css">
    <title>Email Entry</title>
</head>

<body>
    <div class="container">
        <h1>Forgot Your Password</h1>
        <h2>Enter Your Registered Email</h2>
        <?php if (isset($error)) : ?>
            <p class="error-msg">
                <?php echo $error; ?>
            </p>
        <?php endif; ?>
        <form method="POST">
            <input type="email" id="email" name="email" placeholder="Email" required>
            <button type="submit">Send OTP</button>
        </form>
    </div>
</body>

</html>