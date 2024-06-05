<?php
// Check if session is not already active
if (session_status() === PHP_SESSION_NONE) {
    session_start(); // Start the session
}

// Check if the user is not authenticated and redirect to login page
if (!isset($_SESSION['auth'])) {
    $_SESSION['status'] = "Please log in to access this page"; // Set error message
    header('Location: login.php'); // Redirect to login page
    exit(); // Stop further execution
}

// Check if the user is trying to edit or delete an account
if (isset($_GET['user_id'])) {
    // Get the ID of the account
    $user_id = $_GET['user_id'];

    // Get the ID of the currently logged-in user
    $logged_in_user_id = $_SESSION['user_id'];

    // Check if the user is authorized to edit or delete the account
    if ($user_id != $logged_in_user_id) {
        $_SESSION['status'] = "You are not authorized to edit this account"; // Set error message
        header('Location: registration.php'); // Redirect to registration page or appropriate page
        exit(); // Stop further execution
    }
}
?>
