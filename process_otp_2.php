<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include('config.php');


// Check if email and OTP are provided
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['email']) && isset($_POST['otp'])) {
    $email = $_POST['email'];
    $otp = $_POST['otp'];

    // Check if the email and OTP match in the users table
    $sql = "SELECT * FROM users WHERE email = '$email' AND otp = '$otp'";
    $result = $conn->query($sql);

    if ($result->num_rows === 1) {
        // OTP is correct, generate a token
        $token = bin2hex(random_bytes(16));

        // Store the token in the database along with the user's email
        $sql = "UPDATE users SET tokenn = '$token' WHERE email = '$email'";
        if ($conn->query($sql) === TRUE) {
            echo '<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Success</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
            text-align: center;
            padding-top: 50px;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        h1 {
            color: #333;
        }

        a {
            text-decoration: none;
            color: #007bff;
            font-weight: bold;
        }

        a:hover {
            text-decoration: underline;
        }

        .countdown {
            margin-top: 20px;
            font-size: 18px;
            color: #777;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>Success!</h1>
        <p>Otp Verification Successful.</p>
        <p><a href="forgot.php?email=' . urlencode($email) . '&tokenn=' . urlencode($token) . '">Go back to forgot password</a></p>
        <div class="countdown" id="countdown">Redirecting in 1 seconds...</div>
    </div>

    <script>
        // Countdown timer for redirection
        var countdownElement = document.getElementById("countdown");
        var countdown = 1;

        var timer = setInterval(function () {
            countdown--;
            countdownElement.textContent = "Redirecting in " + countdown + " seconds...";
            if (countdown <= 0) {
                clearInterval(timer);
                window.location.href = "forgot.php?email=' . urlencode($email) . '&tokenn=' . urlencode($token) . '";
            }
        }, 1000);
    </script>
</body>

</html>';
        } else {
            echo 'Error updating record: ' . $conn->error;
        }
    } else {
        echo '<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Error</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
            text-align: center;
            padding-top: 50px;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        h1 {
            color: #dc3545;
        }

        p {
            color: #dc3545;
        }

        a {
            text-decoration: none;
            color: #007bff;
            font-weight: bold;
        }

        a:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>Error!</h1>
        <p class="error-message">Invalid OTP.</p>
        <p><a href="otp_verification_2.php?email=' . urlencode($email) . '">Go back to forgot password</a></p>
    </div>
</body>

</html>';
    }
}
$conn->close();
