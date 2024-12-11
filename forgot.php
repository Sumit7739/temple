<?php
session_start();

include('config.php');

// Check if the token is provided in the URL
if (isset($_GET['tokenn'])) {
    $token = $_GET['tokenn'];

    // Check the database for the user with this token
    $sql = "SELECT email FROM users WHERE tokenn = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();
        $userEmail = $row['email'];

        // The user is identified by the email, and you can proceed with password change

        // Check if the form is submitted for password change
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['new_password'])) {
            $newPassword = $_POST['new_password'];

            // Update the user's password in the database
            $updateSql = "UPDATE users SET password = ? WHERE email = ?";
            $updateStmt = $conn->prepare($updateSql);

            // Hash the new password
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

            $updateStmt->bind_param("ss", $hashedPassword, $userEmail);

            if ($updateStmt->execute()) {
                echo '
<!DOCTYPE html>
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
        <p>Password Changed Successfull.</p>
        <p><a href="login.php">Go back to forgot password</a></p>
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
                window.location.href = "login.php";
            }
        }, 1000);
    </script>
</body>
</html>';
                exit(); // Make sure to exit after echoing the HTML to prevent further execution
            } else {
                $errorMessage = 'Error updating password: ' . $conn->error;
            }
        }
    } else {
        // Invalid token, show an error message
        $errorMessage = "Invalid token. Please request a new password change link.";
    }
} else {
    // Token not provided in the URL, show an error message
    $errorMessage = "Token not found in the URL.";
}

// After all PHP logic, you can include the HTML for displaying error messages
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="image/icon.ico" type="image/x-icon" sizes="32x32">
    <link rel="stylesheet" href="styles.css">
    <title>Password Change</title>
    <style>
        /* Reset some default styles for consistency */
        body,
        h1,
        h2,
        p,
        ul,
        li,
        input,
        button {
            margin: 0;
            padding: 0;
            list-style: none;
        }

        /* Basic styling for the page */
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
        }

        .container {
            position: absolute;
            left: 3%;
            top: 20%;
            width: 90%;
            max-width: 450px;
            margin: 0 auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
        }

        .header {
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;
        }

        .header h1 {
            font-size: 24px;
            color: #333;
            text-align: center;
        }

        .password-form label {
            display: block;
            margin-top: 10px;
            font-size: 16px;
            color: #555;
        }

        .password-form input[type="password"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 16px;
            margin-top: 5px;
            box-sizing: border-box;
            /* Ensure padding is included in width */
        }

        .password-form button[type="submit"] {
            background-color: #4285f4;
            color: #fff;
            border: none;
            padding: 10px 20px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            margin-top: 20px;
            width: 100%;
            box-sizing: border-box;
            /* Ensure padding is included in width */
        }

        .success-msg {
            color: green;
            margin-top: 10px;
            text-align: center;
        }

        .error-msg {
            color: red;
            margin-top: 10px;
            text-align: center;
        }

        /* Media Queries for responsiveness */
        @media screen and (max-width: 768px) {
            .container {
                max-width: 100%;
                padding: 10px;
            }

            .header h1 {
                font-size: 20px;
            }

            .password-form label,
            .password-form input[type="password"],
            .password-form button[type="submit"] {
                font-size: 14px;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>Password Change</h1>
        </div>
        <?php if (isset($errorMessage)) : ?>
            <p class="error-msg">
                <?php echo $errorMessage; ?>
            </p>
        <?php endif; ?>
        <?php if (isset($successMessage)) : ?>
            <p class="success-msg">
                <?php echo $successMessage; ?>
            </p>
        <?php endif; ?>
        <form class="password-form" method="POST">
            <label for="new_password">New Password:</label>
            <input type="password" id="new_password" name="new_password" required maxlength="8">
            <button type="submit" id="submit">Change Password</button>
        </form>
    </div>
</body>

</html>