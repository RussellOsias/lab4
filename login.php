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

            // Check if the user is verified
            if ($row['verify'] == 'not verified') {
                $_SESSION['status'] = "Please verify your email to login";
                header('Location: login.php');
                exit(0);
            }

            // Set session variables for authentication
            $_SESSION['auth'] = true; // This line is added to indicate successful authentication
            
            // Update user status to "active" in the database
            $user_id = $row['user_id'];
            $update_status_query = "UPDATE user_profile SET status = 'active' WHERE user_id = '$user_id'";
            mysqli_query($conn, $update_status_query);

            // Redirect to the home page after successful login
            header("Location: index.php");
            exit(0);
        } else {
            // If no matching credentials found, set error message and redirect to login page
            $_SESSION['status'] = "Invalid email or password";
            header("Location: login.php");
            exit(0);
        }
    } else {
        // If database query fails, set error message with error details and redirect to login page
        $_SESSION['status'] = "Error: " . mysqli_error($conn);
        header("Location: login.php");
        exit(0);
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

