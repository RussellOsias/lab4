<?php
session_start();

// Include file for database connection
include('config/db_conn.php');
// Include file for authentication check


use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'phpmailer/src/Exception.php';
require 'phpmailer/src/PHPMailer.php';
require 'phpmailer/src/SMTP.php';

if (isset($_POST['logout_btn'])) {
    // Unset authentication session variables
    unset($_SESSION['auth']);
    unset($_SESSION['auth_user']);
    // Set logout status message
    $_SESSION['status'] = "Logged out successfully";
    // Redirect to login page
    header('Location: login.php');
    exit(0);
}

// Check if the request method is POST
if (isset($_POST['AddUser'])) {
    function validate($data)
    {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }

    // Sanitize input data obtained from the POST request
    $full_name = validate($_POST['full_name']);
    $email = validate($_POST['email']);
    $phone_number = validate($_POST['phone_number']);
    $address = validate($_POST['address']);
    $password = validate($_POST['password']);
    $confirm_password = validate($_POST['confirm_password']);

    if ($password == $confirm_password) {

        // Validate email format
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['error'] = "Invalid email format";
            header("Location: registration.php");
            exit();
        }

        // Check if email already exists in the database
        $email_check_query = "SELECT * FROM user_profile WHERE email='$email' LIMIT 1";
        $result = mysqli_query($conn, $email_check_query);
        $user = mysqli_fetch_assoc($result);

        // If email already exists
        if ($user) {
            $_SESSION['error'] = "Email already exists";
            header("Location: registration.php");
            exit();
        }

        // SQL query to insert user data into the database
        $sql = "INSERT INTO user_profile (full_name,email,phone_number,address,password)
                    VALUES ('$full_name','$email','$phone_number','$address','$password')";

        if (mysqli_query($conn, $sql)) {
            // Redirect with a success message
            $_SESSION['status'] = "User Added Successfully";

            // Send email notification
            $mail = new PHPMailer(true);
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'uchihareikata@gmail.com'; // Your Gmail email address
            $mail->Password = 'qyki jszw moov wvhz'; // Your Gmail password
            $mail->SMTPSecure = 'ssl';
            $mail->Port = 465;
            $mail->setFrom('uchihareikata@gmail.com', 'Russell Osias'); // Your email address and name
            $mail->addAddress($email); // Recipient's email address
            $mail->isHTML(true);
            $mail->Subject = 'Registration Confirmation';
            $mail->Body = 'Dear ' . $full_name . ',<br><br>Thank you for registering on our website.<br><br>Sincerely,<br>Your Name';

            if ($mail->send()) {
                // Email sent successfully
                $_SESSION['status'] .= ". Email sent successfully";
            } else {
                // Email sending failed
                $_SESSION['status'] .= ". Email sending failed. Error: " . $mail->ErrorInfo;
            }

            header("Location: registration.php");
        } else {
            // Display an error message if the query fails
            $_SESSION['status'] = "User Registration Failed";
            header("Location: registration.php");
        }
    } else {
        // Display an error message if passwords do not match
        $_SESSION['status'] = "Password and Confirm Password do not match.!";
        header("Location: registration.php");
    }
}

// Check if the request method is POST
if (isset($_POST['UpdateUser'])) {
    function validate($data)
    {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }

    // Ensure fields is initialized
    $user_id = $_POST['user_id'];
    $full_name = ($_POST['full_name']);
    $email = ($_POST['email']);
    $phone_number = ($_POST['phone_number']);
    $address = ($_POST['address']);
    $password = ($_POST['password']);

    // Construct SQL query for updating user profile
    $sql = "UPDATE user_profile SET full_name = '$full_name', email = '$email', phone_number = '$phone_number', address = '$address', password = '$password' WHERE user_id = '$user_id' ";

    if (mysqli_query($conn, $sql)) {
        // Redirect with a success message
        $_SESSION['status'] = "User Update Successfully";
        header("Location: registration.php");
    } else {
        // Display an error message if the query fails
        $_SESSION['status'] = "User Updating Failed";
        header("Location: registration.php");
    }
}

// Check if the request method is POST
if (isset($_POST['DeleteUserbtn'])) {
    function validate($data)
    {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }

    $user_id = $_POST['delete_id'];

    $sql = "DELETE FROM user_profile WHERE user_id = '$user_id' ";
    if (mysqli_query($conn, $sql)) {
        // Redirect with a success message
        $_SESSION['status'] = "User Deleted Successfully";
        header("Location: registration.php");
    } else {
        // Display an error message if the query fails
        $_SESSION['status'] = "User Deleting Failed";
        header("Location: registration.php");
    }
}
?>
