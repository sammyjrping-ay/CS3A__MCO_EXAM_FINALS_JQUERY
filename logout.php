<?php
    session_start(); // Start session to access session variables
    unset($_SESSION['username']); // Remove username from session
    unset($_SESSION['usertype']); // Remove usertype from session
    echo header("Location: login.php"); // Redirect to login page
?>
