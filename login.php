<?php
// Start the session to manage user authentication
session_start();

// Include the header file
include('includes/header.php');

// Include file for database connection
include('config/db_conn.php');

// Include PHPMailer classes for sending emails
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Include the required PHPMailer files
require 'phpmailer/src/Exception.php';
require 'phpmailer/src/PHPMailer.php';
require 'phpmailer/src/SMTP.php';

// Check if the user is already authenticated and redirect to index page if so
if (isset($_SESSION['auth'])) {
    $_SESSION['status'] = "You are already logged In"; // Set status message
    header('Location: registration.php'); // Redirect to the index page
    exit(0); // Stop further script execution
}

// Check if the login button is clicked
if (isset($_POST['login_btn'])) { // Check if the login button is clicked

    // Function to sanitize input data
    function validate($data)
    {
        $data = trim($data); // Remove whitespace from the beginning and end of the input
        $data = stripslashes($data); // Remove backslashes
        $data = htmlspecialchars($data); // Convert special characters to HTML entities
        return $data; // Return the sanitized data
    }

    // Sanitize username and password from the form submission
    $email = validate($_POST['email']); // Sanitize the email address
    $password = validate($_POST['password']); // Sanitize the password

    // Query the database for user credentials
    $sql = "SELECT * FROM user_profile WHERE email='$email' AND password='$password' LIMIT 1"; // Construct SQL query
    $result = mysqli_query($conn, $sql); // Execute the query

    if ($result) { // Check if the database query was successful

        // Check if a row is found with matching credentials
        if (mysqli_num_rows($result) == 1) {

            // Fetch user details from the database
            $row = mysqli_fetch_assoc($result);

            // Check if the user is verified
            if ($row['verify'] !== 'verified') { // Check if the user is not verified
                // Generate verification code
                $verification_code = rand(100000, 999999); // Generate a random 6-digit verification code

                // Store verification code in session
                $_SESSION['verification_code'] = $verification_code;

                // Store user ID in session
                $_SESSION['user_id'] = $row['user_id'];

                // Send verification email
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
                $mail->Subject = 'Email Verification'; // Set email subject
                $mail->Body = 'Your verification code is: ' . $verification_code; // Set email body

                if ($mail->send()) { // If email sent successfully
                    $_SESSION['status'] = "Please check your email for the code"; // Set status message
                    header('Location: verify.php'); // Redirect to verify page
                    exit(0); // Stop further script execution
                } else {
                    $_SESSION['status'] = "Failed to send verification email"; // Set error message
                    header('Location: login.php'); // Redirect to login page
                    exit(0); // Stop further script execution
                }
            }

            // Set session variables for authentication
            $_SESSION['auth'] = true; // Set authentication status in the session to indicate successful login

            // Update user status to "active" in the database
            $user_id = $row['user_id']; // Retrieve the user ID from the database
            $update_status_query = "UPDATE user_profile SET status = 'active' WHERE user_id = '$user_id'"; // Prepare SQL query to update user status
            mysqli_query($conn, $update_status_query); // Execute the SQL query to update user status

            // Update session variables with user information
            $_SESSION['user_id'] = $row['user_id']; // Update session variable for user ID
            $_SESSION['full_name'] = $row['full_name']; // Update session variable for full name
            $_SESSION['email'] = $row['email']; // Update session variable for email
            // Add more session variables as needed

            // Redirect to the home page after successful login
            header("Location: registration.php"); // Redirect to the home page
            exit(0); // Stop further execution of the script
        } else {
            // If no matching credentials found, set error message and redirect to login page
            $_SESSION['status'] = "Invalid email or password"; // Set error message in the session
            header("Location: login.php"); // Redirect to the login page
            exit(0); // Stop further execution of the script
        }
    } else {
        // If database query fails, set error message with error details and redirect to login page
        $_SESSION['status'] = "Error: " . mysqli_error($conn); // Set error message with error details
        header("Location: login.php"); // Redirect to the login page
        exit(0); // Stop further execution of the script
    }
}
?>

<!-- HTML section -->
<div class="section">
    <div class="container">
        <div class="row justify-content-center">
            <
            <div class="col-md-5 my-5">
                <div class="card my-5">
                    <div class="card-header bg-light">
                        <!-- Login Form Header -->
                        <h5>Login Form</h5>
                    </div>
                    <div class="card-body">
                        <!-- Display authentication status message if set -->
                        <?php
                        if (isset($_SESSION['auth_status'])) {
                            ?>
                            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                                <strong>Hey!</strong> <?php echo $_SESSION['auth_status']; ?>
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <?php
                            // Unset the authentication status message
                            unset($_SESSION['auth_status']);
                        }
                        ?>

                        <!-- Include any other message file ifneeded -->
<?php
                     include('message.php');
                     ?>
                                         <!-- Login Form -->
                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
                        <div class="form-group">
                            <label for="">Email</label>
                            <span></span>
                            <!-- Email input field -->
                            <input type="text" name="email" class="form-control" placeholder="Email" required>
                        </div>

                        <div class="form-group">
                            <label for="">Password</label>
                            <!-- Password input field -->
                            <input type="password" name="password" class="form-control" placeholder="Password" required>
                        </div>

                        <hr>

                        <div class="modal-footer">
                            <!-- Submit button for form -->
                            <button type="submit" name="login_btn" class="btn btn-primary btn-block">Login</button>
                        </div>
                    </form>

                    <!-- Verification link -->
                    <div class="text-center">
                        <p>Don't have an account? <a href="signup.php" class="btn-sm">Sign Up</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
<!-- Include footer and script files -->
<?php include('includes/script.php'); ?>
<?php include('includes/footer.php'); ?>
