<?php
session_start();

include('config.php');

$error = ''; // Initialize the error variable

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $enteredPassword = $_POST['password'];
    $rememberMe = isset($_POST['remember']);

    $sql = "SELECT * FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $storedHashedPassword = $row['password'];
        $verificationStatus = $row['verification_status'];

        if ($verificationStatus == 0) {
            header('Location: otp_verification.php?email=' . $row['email']);
            exit();
        }

        if (password_verify($enteredPassword, $storedHashedPassword)) {
            session_regenerate_id(true);
            $_SESSION['id'] = $row['id'];

            if ($rememberMe) {
                $token = bin2hex(random_bytes(16)); // Generate a secure token
                $expiryTime = time() + (86400 * 30); // cookie for 30 days
                setcookie('remember_me', $token, $expiryTime, "/", "", true, true); // Secure, HttpOnly flag

                // Store the token in the database
                $updateTokenSql = "UPDATE users SET remember_token = ? WHERE id = ?";
                $updateStmt = $conn->prepare($updateTokenSql);
                $updateStmt->bind_param("si", $token, $row['id']);
                $updateStmt->execute();
                $updateStmt->close();
            }

            $stmt->close();
            $conn->close();
            header("Location: welcome.php");
            exit();
        } else {
            $error = "Invalid password"; // Set the error message
        }
    } else {
        $error = "Invalid email"; // Set the error message
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/8.0.1/normalize.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="login.css">
    <title>Login</title>
</head>

<body>
    <a href="index.html" class="home-icon">
        <i class="fas fa-home"></i>
    </a>
    <div class="login-container">
        <h2>Login</h2>
        <?php if ($error !== '') : ?>
            <div class="error-message"><?php echo $error; ?></div>
        <?php endif; ?>
        <form method="POST" action="login.php">
            <div class="input-box">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" placeholder="Enter your email" required>
            </div>
            <div class="input-box">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="Enter your password" required maxlength="8">
            </div>
            <div class="checkbox-container">
                <input type="checkbox" id="remember" name="remember">
                <label for="remember">Remember Me</label>
            </div>
            <br>
            <div class="reset">
                <a href="send_otp.php">Forgot Password</a>
            </div>
            <br>
            <br>
            <button type="submit" name="submit">Login</button>
        </form>
    </div>
</body>

</html>
