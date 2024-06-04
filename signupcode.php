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
    $age = $_POST['age'];

    // Check if the user is at least 13 years old
    if ($age < 13) {
        $_SESSION['status'] = "You must be 13 years old or above to use this website.";
        header('Location: signup.php');
        exit();
    }

    // Check if the email already exists in the database
    $check_email_query = "SELECT * FROM user_profile WHERE email='$email' LIMIT 1 ";
    $check_email_query_run = mysqli_query($conn, $check_email_query);

    if(mysqli_num_rows($check_email_query_run) > 0) {
        // Email already exists
        $_SESSION['status'] = "Email already exists";
        header('Location: login.php');
        exit();
    } else {
        // Insert user data into the database
        $insert_query = "INSERT INTO user_profile (full_name, email, phone_number, address, password, age) 
                         VALUES ('$full_name', '$email', '$phone_number', '$address', '$password', '$age')";
        
        if(mysqli_query($conn, $insert_query)) {
            // Account created successfully
            // Send email verification
            $mail = new PHPMailer(true);
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'reikatauchiha@gmail.com'; // SMTP username (your Gmail address)
            $mail->Password = 'rhlt zyks rwyc mzpf';  // Your Gmail password
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;
            $mail->setFrom('reikatauchiha@gmail.com', 'Russell Osias'); // Your email address and name
            $mail->addAddress($email); // Recipient's email address
            $mail->isHTML(true);
            $mail->Subject = 'Welcome to Our Website';
            $mail->Body = 'Dear ' . $full_name . ',<br><br>Welcome to our website!<br><br>You can now log in using your email address.<br><br>Best regards,<br>Russells Website';

            if ($mail->send()) {
                $_SESSION['status'] = "Account created successfully. Please check your email for further instructions.";
                header('Location: login.php');
                exit();
            } else {
                $_SESSION['status'] = "Failed to send email verification. Please try again.";
                header('Location: signup.php');
                exit();
            }
        } else {
            // Failed to create account
            $_SESSION['status'] = "Failed to create account. Please try again.";
            header('Location: signup.php');
            exit();
        }
    }
} else {
    // Access denied
    $_SESSION['status'] = "Access Denied";
    header('Location: signup.php');
    exit();
}
?>
