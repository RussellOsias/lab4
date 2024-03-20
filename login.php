<?php
// Start the session to manage user authentication
session_start();

// Include the header file
include('includes/header.php');

// Include file for database connection
include('config/db_conn.php');

// Include PHPMailer classes
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'phpmailer/src/Exception.php';
require 'phpmailer/src/PHPMailer.php';
require 'phpmailer/src/SMTP.php';

// Check if the user is already authenticated and redirect to index page if so
if (isset($_SESSION['auth'])) {
    $_SESSION['status'] = "You are already logged In";
    header('Location: index.php');
    exit(0);
}

// Check if the login button is clicked
if (isset($_POST['login_btn'])) {
    // Function to sanitize input data
    function validate($data)
    {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }

    // Sanitize username and password from the form submission
    $email = validate($_POST['email']);
    $password = validate($_POST['password']);

    // Query the database for user credentials
    $sql = "SELECT * FROM user_profile WHERE email='$email' AND password='$password' LIMIT 1";
    $result = mysqli_query($conn, $sql);

    if ($result) {
        // Check if a row is found with matching credentials
        if (mysqli_num_rows($result) == 1) {
            // Fetch user details from the database
            $row = mysqli_fetch_assoc($result);

            // Store user ID in session to indicate successful login
            $_SESSION['user_id'] = $row['user_ID'];

            // Set the authentication session variable
            $_SESSION['auth'] = true; // This line is added to indicate successful authentication

            // Send email notification
            $mail = new PHPMailer(true);
            try {
                // Server settings
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'uchihareikata@gmail.com'; // Your Gmail email address
                $mail->Password = 'qyki jszw moov wvhz'; // Your Gmail password
                $mail->SMTPSecure = 'tls';
                $mail->Port = 587;
                $mail->setFrom('uchihareikata@gmail.com', 'Russell Osias'); // Your email address and name
                $mail->addAddress($email); // Recipient's email address
                $mail->isHTML(true);
                $mail->Subject = 'Login Notification';
                $mail->Body = 'You have successfully logged in to our website.';

                // Send email
                $mail->send();

                // Redirect to index.php after sending the email
                header("Location: index.php");
                exit();
            } catch (Exception $e) {
                // Handle errors if email sending fails
                $_SESSION['status'] = "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
                header("Location: login.php");
                exit();
            }
        } else {
            // If no matching credentials found, set error message and redirect to login page
            $_SESSION['status'] = "Invalid email or password";
            header("Location: login.php");
            exit();
        }
    } else {
        // If database query fails, set error message with error details and redirect to login page
        $_SESSION['status'] = "Error: " . mysqli_error($conn);
        header("Location: login.php");
        exit();
    }
}
?>

<!-- HTML section -->
<div class="section">
    <div class="container">
        <div class="row justify-content-center">
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

                        <!-- Include any other message file if needed -->
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

                        <!-- Sign Up link -->
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
