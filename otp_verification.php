<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, 
    initial-scale=1.0">
    <link rel="icon" href="icon.ico" type="image/x-icon" sizes="32x32">
    <link rel="stylesheet" href="otp_verification.css">
    <title>OTP Verification</title>
</head>

<body>
    <?php

    include('config.php');

    // Check if email is provided as a URL parameter
    if (isset($_GET['email'])) {
        $email = $_GET['email'];
    ?>
        <h1>Account Verification</h1>
        <h2>An OTP is sent to your entered Email Address</h2>
        <form action="process_otp.php" method="POST">
            <input type="hidden" name="email" value="<?php echo $email; ?>">
            <input type="text" id="otp" name="otp" placeholder="Enter OTP" required maxlength="6">
            <br><br>
            <input type="submit" value="Verify OTP">
            <h4>Did not recieved OTP?
                <a href="resend_otp.php">Resend Otp</a>
            </h4>
        </form>
    <?php
    } else {
        echo 'Invalid request.';
    }

    $conn->close();
    ?>
</body>

</html>