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
    $full_name = $_POST['full_name'];
    $email = $_POST['email'];
    $phone_number = $_POST['phone_number'];
    $address = $_POST['address'];
    $password = $_POST['password'];
    $birthdate = $_POST['birthdate']; 
    $photo = $_FILES['photo']['name']; 

    // Check if the email already exists in the database
    $check_email_query = "SELECT * FROM user_profile WHERE email='$email' LIMIT 1 ";
    $check_email_query_run = mysqli_query($conn, $check_email_query);

    if (mysqli_num_rows($check_email_query_run) > 0) {
        // Email already exists
        $_SESSION['status'] = "Email already exists";
        header('Location: login.php');
        exit();
    } else {
        // Insert user data into the database
        $insert_query = "INSERT INTO user_profile (full_name, email, phone_number, address, password, birthdate, photo) 
                         VALUES ('$full_name', '$email', '$phone_number', '$address', '$password', '$birthdate', '$photo')";
        
        if (mysqli_query($conn, $insert_query)) {
            // Account created successfully
            move_uploaded_file($_FILES['photo']['tmp_name'], 'uploads/'.$photo); // Upload photo to server

            // Send email verification
            $mail = new PHPMailer(true); // Create a new email object using PHPMailer. 'true' enables error exceptions.

            $mail->isSMTP(); // Set the mailer to use SMTP (Simple Mail Transfer Protocol).
            $mail->Host = 'smtp.gmail.com'; // Set the SMTP server to send through (Gmail's SMTP server).           
            $mail->SMTPAuth = true; // Enable SMTP authentication to make sure the email is sent securely.           
            $mail->Username = 'reikatauchiha@gmail.com'; // The email address you are sending from.           
            $mail->Password = 'rhlt zyks rwyc mzpf'; // The password for the email address you are sending from.          
            $mail->SMTPSecure = 'tls'; // Use TLS encryption to secure the email.          
            $mail->Port = 587; // The port to connect to the SMTP server (587 for TLS).          
            $mail->setFrom('reikatauchiha@gmail.com', 'Russell Osias'); // Set the sender's email address and name.         
            $mail->addAddress($email); // Add the recipient's email address.         
            $mail->isHTML(true); // Set the email format to HTML.        
            $mail->Subject = 'Welcome to Our Website'; // Set the subject of the email.         
            $mail->Body = 'Dear ' . $full_name . ',<br><br>Welcome to my website!<br><br>You can now log in using your email address.<br><br>Best regards,<br>Russells Website'; // Set the body of the email, including the recipient's name and a welcome message.
            
            if ($mail->send()) { // Try to send the email and check if it was successful.
                $_SESSION['status'] = "Account created successfully. Please check your email for any further emails in the future."; // If successful, set a session message indicating account creation success.
                header('Location: login.php'); // Redirect the user to the login page.
                exit(); // Stop further script execution.
            } else {
                $_SESSION['status'] = "Failed to send email verification. Please try again."; // If sending failed, set a session message indicating the failure.
                header('Location: signup.php'); // Redirect the user back to the signup page.
                exit(); // Stop further script execution.
            }
            } else {
                // Failed to create account
                $_SESSION['status'] = "Failed to create account. Please try again."; // If account creation failed, set a session message indicating the failure.
                header('Location: signup.php'); // Redirect the user back to the signup page.
                exit(); // Stop further script execution.
            }
        }            
} else {
    // Access denied
    $_SESSION['status'] = "Access Denied";
    header('Location: signup.php');
    exit();
}
?>
