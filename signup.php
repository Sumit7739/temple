<?php
session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require 'PHPMailer-master/src/PHPMailer.php';
require 'PHPMailer-master/src/SMTP.php';
require 'PHPMailer-master/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    include('config.php');

    $token = bin2hex(random_bytes(16));
    // Check if the user already exists
    $sql = "SELECT * FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // User already exists
        $error = "User already exists";
    } else {
        // Insert the new user into the database with role and access
        $sql = "INSERT INTO users (name, email, password, tokenn) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssss", $name, $email, $hashed_password, $token);
        $stmt->execute();

        // Check if the user was successfully inserted
        if ($stmt->affected_rows > 0) {
            // User created successfully
            $_SESSION['id'] = $stmt->insert_id;
            $stmt->close();

            // Function to generate a random 6-digit OTP
            function generateOTP()
            {
                $otp = "";
                for ($i = 0; $i < 6; $i++) {
                    $otp .= mt_rand(0, 9);
                }
                return $otp;
            }

            // Retrieve the recipient email from the form
            $recipientEmail = $_POST['email'];

            // Generate OTP
            $otp = generateOTP();

            // Initialize PHPMailer
            $mail = new PHPMailer();

            // SMTP configuration
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->Port = 587;
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->SMTPAuth = true;
            $mail->Username = 'srisinhasumit10@gmail.com'; // Your Gmail email address
            $mail->Password = 'ggtbuofjfdmqcohr'; // Your Gmail password

            // Sender and recipient
            $mail->setFrom('templecare@gmail.com', 'Temple Foundation'); // Sender email and name
            $mail->addAddress($recipientEmail); // Recipient email

            // Save the OTP in the database
            $sql = "UPDATE users SET otp = ? WHERE email = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ss", $otp, $recipientEmail);

            if ($stmt->execute()) {
                $mail->isHTML(true);
                $mail->Subject = 'Verify Your Account'; // Personalized subject line

                $body = "
                <html>
                <head>
                  <title>Verify Your Account</title>
                </head>
                <body>
                  <p>Hi there,</p>
                  <p>Thank you for creating an account! To complete your registration and ensure the security of your account, please verify your email address using the following One-Time Password (OTP):</p>
                  <p style='font-weight: bold;'>" . $otp . "</p>
                  <p>This code is valid for 5 minutes. Please enter it in the designated field on our website to complete your registration.</p>
                  <p>If you didn't request this verification, please ignore this email. Your account remains secure.</p>
                  <p>Thanks,<br />The Temple Foundation Team</p>
                </body>
                </html>";

                $mail->Body = $body;

                if ($mail->send()) {
                    // Redirect to OTP verification page
                    header('Location: otp_verification.php?email=' . $recipientEmail);
                    exit();
                } else {
                    $error = 'Error sending email: ' . $mail->ErrorInfo;
                }
            } else {
                $error = 'Error updating OTP: ' . $stmt->error;
            }

            $stmt->close();
        } else {
            $error = "Failed to create user";
        }
    }

    $stmt->close(); // Close the statement
    $conn->close(); // Close the database connection
}
?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <title>Signup</title>
    <link rel="stylesheet" href="stylesignup.css">
</head>

<body>
    <a href="index.html" class="toggle-buttons">
        <i id="homeIcon" class="fas fa-home"></i>
    </a>
    <section>
        <div class="login-box">
            <div id="loaderOverlay">
                <div id="loader" class="loader"></div>
            </div>
            <form method="POST">
                <h2>Signup</h2>
                <?php if (isset($error)) { ?>
                    <p class="error-msg">
                        <?php echo $error; ?>
                    </p>
                <?php } ?>
                <div class="input-box">
                    <input type="text" id="name" name="name" required>
                    <label for="name">Name</label>
                </div>
                <div class="input-box">
                    <input type="email" id="email" name="email" required>
                    <label for="email">Email</label>
                </div>
                <div class="input-box">
                    <input type="password" id="password" name="password" required maxlength="8">
                    <label for="password">Password</label>
                </div>
                <div class="input-box">
                    <input type="password" id="confirm_password" name="password" required maxlength="8">
                    <label for="confirm_password">Confirm Password</label>
                </div>
                <p id="password-error" style="color: red;"></p>
                <div class="checkbox">
                    <input type="checkbox"> I agree to the terms and condition.
                </div>
                <button type="submit" id="submit" name="submit">Signup</button>

                <div class="log">
                    <h4>Already have an account?
                        <a href="login.php">SignIn</a>
                    </h4>
                </div>
            </form>
        </div>
    </section>

    <script src="signup.js"></script>
</body>

</html>