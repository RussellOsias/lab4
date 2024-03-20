<?php
session_start();

include('config/db_conn.php');

// Check if the verification form is submitted and the verification code is set
if (isset($_POST['verify']) && isset($_SESSION['verification_code'])) {
    $verification_code = $_POST['verification_code'];
    $expected_code = $_SESSION['verification_code'];

    // Check if entered code matches the expected code
    if ($verification_code == $expected_code) {
        // Verification successful, redirect to home.php
        $_SESSION['message'] = "Verification successful";
        $_SESSION['alert_type'] = "success";
        header("Location: home.php");
        exit();
    } else {
        // Verification failed, show error message
        $_SESSION['message'] = "Incorrect verification code.";
        $_SESSION['alert_type'] = "error";
        header("Location: verify.php");
        exit();
    }
}

// Check if the user is not authenticated
if (!isset($_SESSION['user_id'])) {
    // Verification code is not set or form is not submitted, redirect to login page
    $_SESSION['message'] = "Please log in to access this page.";
    $_SESSION['alert_type'] = "error";
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Verification</title>
    <!-- Linking Bootstrap CSS for styling -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
   <!-- Linking your custom CSS file -->
    <link href="Stylesheet.css" rel="stylesheet">
    <style>
        /* Additional styles here */
        /* You can add custom styles if needed */
    </style>
</head>
<body>
    <div class="container">
        <div class="verification-container">
            <h2>Verification</h2>
            <?php if (isset($_SESSION['message'])): ?>
                <div class="alert alert-<?php echo $_SESSION['alert_type']; ?>">
                    <?php 
                    echo $_SESSION['message']; 
                    unset($_SESSION['message']); // Clear the session message after displaying
                    ?> 
                </div>
                <?php unset($_SESSION['alert_type']); // Unsetting session variable after displaying ?>
            <?php endif; ?>
            <form action="verify.php" method="post"> <!-- Change action to verify.php -->
                <input type="text" name="verification_code" class="form-control" placeholder="Verification Code" required><br>
                <button type="submit" name="verify" class="btn btn-primary">Verify</button>
            </form>
            <div class="btn-container">
                <p>Back to Login <a href="login.php" class="btn btn-primary">Login</a></p>
            </div>
        </div>
    </div>
</body>
</html>