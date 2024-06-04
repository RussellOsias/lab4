<?php
// Start the session to manage user authentication
session_start();

// Include the header file
include('includes/header.php');

// Include file for database connection
include('config/db_conn.php');

// Check if the user is authenticated
if (!isset($_SESSION['auth'])) {
    $_SESSION['status'] = "You need to log in first";
    header('Location: login.php');
    exit(0);
}

// Check if the verification code is submitted
if (isset($_POST['verify_code'])) {
    // Validate the entered verification code
    $entered_code = $_POST['verify_code'];
    $expected_code = $_SESSION['verification_code'];

    if ($entered_code == $expected_code) {
        // If verification code matches, update user status in the database
        $user_id = $_SESSION['user_id'];
        $update_query = "UPDATE user_profile SET verify = 'Verified' WHERE user_ID = '$user_id'";

        if (mysqli_query($conn, $update_query)) {
            // Successful verification, redirect to index.php
            header("Location: index.php");
            exit();
        } else {
            $_SESSION['status'] = "Error updating verification status: " . mysqli_error($conn);
            header("Location: verify.php");
            exit();
        }
    } else {
        $_SESSION['status'] = "Invalid verification code";
        header("Location: verify.php");
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
                        <!-- Verify Email Form Header -->
                        <h5>Verify Email</h5>
                    </div>
                    <div class="card-body">
                        <!-- Display status messages -->
                        <?php
                        if (isset($_SESSION['status'])) {
                            echo "<div class='alert alert-warning alert-dismissible fade show' role='alert'>" . $_SESSION['status'] . "
                                <button type='button' class='close' data-dismiss='alert' aria-label='Close'>
                                    <span aria-hidden='true'>&times;</span>
                                </button>
                            </div>";
                            unset($_SESSION['status']); // Clear the status message after displaying it
                        }
                        ?>

                        <!-- Verification Code Form -->
                        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
                            <div class="form-group">
                                <label for="">Enter Verification Code</label>
                                <input type="text" name="verify_code" class="form-control" placeholder="Verification Code" required>
                            </div>

                            <!-- Submit button for verification code -->
                            <button type="submit" name="verify_btn" class="btn btn-primary btn-block">Verify</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Include footer and script files -->
<?php include('includes/script.php'); ?>
<?php include('includes/footer.php'); ?>
