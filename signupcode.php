<?php
// Start the session
session_start();

// Include database connection
include('config/db_conn.php');

// Include PHPMailer library
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'phpmailer/src/Exception.php';
require 'phpmailer/src/PHPMailer.php';
require 'phpmailer/src/SMTP.php';

// Check if the signup button is clicked
if (isset($_POST['signup_btn'])) {
    // Get user input data
    $full_name = $_POST['full_name']; // Retrieve full name from the form
    $email = $_POST['email']; // Retrieve email from the form
    $phone_number = $_POST['phone_number']; // Retrieve phone number from the form
    $address = $_POST['address']; // Retrieve address from the form
    $password = $_POST['password']; // Retrieve password from the form
    $age = $_POST['age']; // Retrieve age from the form

    // Check if the user is at least 13 years old
    if ($age < 13) {
        $_SESSION['status'] = "You must be 13 years old or above to use this website."; // Set error message
        header('Location: signup.php'); // Redirect to signup page
        exit(); // Stop further execution
    }

    // Check if the email already exists in the database
    $check_email_query = "SELECT * FROM user_profile WHERE email='$email' LIMIT 1 "; // SQL query to check if email exists
    $check_email_query_run = mysqli_query($conn, $check_email_query); // Execute the query

    if(mysqli_num_rows($check_email_query_run) > 0) {
        // Email already exists
        $_SESSION['status'] = "Email already exists"; // Set error message
        header('Location: login.php'); // Redirect to login page
        exit(); // Stop further execution
    } else {
        // Insert user data into the database
        $insert_query = "INSERT INTO user_profile (full_name, email, phone_number, address, password, age) 
                         VALUES ('$full_name', '$email', '$phone_number', '$address', '$password', '$age')"; // SQL query to insert user data
        
        if(mysqli_query($conn, $insert_query)) {
            // Account created successfully
            // Send email verification
            $mail = new PHPMailer(true); // Create a new PHPMailer instance
            $mail->isSMTP(); // Set mailer to use SMTP
            $mail->Host = 'smtp.gmail.com'; // SMTP server address
            $mail->SMTPAuth = true; // Enable SMTP authentication
            $mail->Username = 'reikatauchiha@gmail.com'; // SMTP username (your Gmail address)
            $mail->Password = 'rhlt zyks rwyc mzpf';  // Your Gmail password
            $mail->SMTPSecure = 'tls'; // Enable TLS encryption
            $mail->Port = 587; // TCP port to connect to
            $mail->setFrom('reikatauchiha@gmail.com', 'Russell Osias'); // Set sender's email address and name
            $mail->addAddress($email); // Add recipient's email address
            $mail->isHTML(true); // Set email format to HTML
            $mail->Subject = 'Welcome to Our Website'; // Set email subject
            $mail->Body = 'Dear ' . $full_name . ',<br><br>Welcome to my website!<br><br>You can now log in using your email address.<br><br>Best regards,<br>Russells Website'; // Set email body

            if ($mail->send()) { // If email sent successfully
                $_SESSION['status'] = "Account created successfully. Please check your email for any further emails in the future."; // Set success message
                header('Location: login.php'); // Redirect to login page
                exit(); // Stop further execution
            } else {
                $_SESSION['status'] = "Failed to send email verification. Please try again."; // Set error message
                header('Location: signup.php'); // Redirect to signup page
                exit(); // Stop further execution
            }
        } else {
            // Failed to create account
            $_SESSION['status'] = "Failed to create account. Please try again."; // Set error message
            header('Location: signup.php'); // Redirect to signup page
            exit(); // Stop further execution
        }
    }
} else {
    // Access denied
    $_SESSION['status'] = "Access Denied"; // Set error message
    header('Location: signup.php'); // Redirect to signup page
    exit(); // Stop further execution
}
?>
