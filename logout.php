<?php
// Initialize the session
session_start();

// Unset all session variables
$_SESSION = array();

// Destroy the session
session_destroy();

// Clear the remember_me cookie
if (isset($_COOKIE['remember_me'])) {
    unset($_COOKIE['remember_me']);
    setcookie('remember_me', '', time() - 3600, '/', '', true, true); // set the expiration date in the past
}

// Redirect to the login page or any other desired page
header("Location: login.php");
exit();
?>
