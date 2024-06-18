<?php
// Start session
session_start();

// Include authentication check
include('authentication.php');

// Include header, topbar, sidebar, and database connection
include('includes/header.php');
include('includes/topbar.php');
include('includes/sidebar.php');
include('config/db_conn.php');

// Include PHPMailer library
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'phpmailer/src/Exception.php';
require 'phpmailer/src/PHPMailer.php';
require 'phpmailer/src/SMTP.php';

// Fetch user profile data from the database based on the logged-in user's ID
$user_id = $_SESSION['user_id']; // Get the user ID from the session.
$query = "SELECT * FROM user_profile WHERE user_id = '$user_id'"; // Prepare a query to select all columns from the user_profile table where the user_id matches the current user's ID.
$result = mysqli_query($conn, $query); // Execute the query and store the result.
$userProfile = mysqli_fetch_assoc($result); // Fetch the result as an associative array and store it in the $userProfile variable.

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") { // Check if the form has been submitted via POST method.
    // Initialize variables with empty strings if they do not exist
    $fullName = isset($_POST['fullName']) ? $_POST['fullName'] : ''; // Get the full name from the form or set it to an empty string if not available.
    $email = isset($_POST['email']) ? $_POST['email'] : ''; // Get the email from the form or set it to an empty string if not available.
    $phoneNumber = isset($_POST['phoneNumber']) ? $_POST['phoneNumber'] : ''; // Get the phone number from the form or set it to an empty string if not available.
    $address = isset($_POST['address']) ? $_POST['address'] : ''; // Get the address from the form or set it to an empty string if not available.
    $birthdate = isset($_POST['birthdate']) ? $_POST['birthdate'] : ''; // Get the birthdate from the form or set it to an empty string if not available.
    $bio = isset($_POST['bio']) ? $_POST['bio'] : ''; // Get the bio from the form or set it to an empty string if not available.
    $gender = isset($_POST['gender']) ? $_POST['gender'] : ''; // Get the gender from the form or set it to an empty string if not available.
    $citizenship = isset($_POST['citizenship']) ? $_POST['citizenship'] : ''; // Get the citizenship from the form or set it to an empty string if not available.
    $nationality = isset($_POST['nationality']) ? $_POST['nationality'] : ''; // Get the nationality from the form or set it to an empty string if not available.
    $religion = isset($_POST['religion']) ? $_POST['religion'] : ''; // Get the religion from the form or set it to an empty string if not available.
    $maritalStatus = isset($_POST['maritalStatus']) ? $_POST['maritalStatus'] : ''; // Get the marital status from the form or set it to an empty string if not available.

    // Update user profile in the database
    if (empty($bio)) { // Check if the bio is empty.
        $bio = "What's on your mind? Be creative!"; // If empty, set a default bio message.
    }
    
    $updateQuery = "UPDATE user_profile SET full_name='$fullName', email='$email', phone_number='$phoneNumber', address='$address', birthdate='$birthdate', bio='$bio', gender='$gender', citizenship='$citizenship', nationality='$nationality', religion='$religion', marital_status='$maritalStatus' WHERE user_id='$user_id'"; // Prepare an SQL query to update the user profile with the new data.
    
    $updateResult = mysqli_query($conn, $updateQuery); // Execute the update query.
    
    if ($updateResult) { // Check if the update was successful.
        echo "<script>alert('Profile updated successfully');</script>"; // If successful, show an alert message.
    } else {
        // You can add an else condition here to handle errors if needed.
    }
    
    // Handle profile picture upload
    if (!empty($_FILES["photo"]["name"])) { // Check if a file was uploaded.
        $targetDirectory = "uploads/"; // Set the directory where the file will be saved.
        $targetFile = $targetDirectory . basename($_FILES["photo"]["name"]); // Set the complete path for the uploaded file.
        $uploadOk = 1; // Initialize upload status as okay.
        $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION)); // Get the file extension of the uploaded file.
    
        // Check if image file is an actual image or fake image
        $check = getimagesize($_FILES["photo"]["tmp_name"]); // Check if the file is an actual image.
        if ($check !== false) { // If the file is an image,
            $uploadOk = 1; // Set upload status as okay.
        } else { // If the file is not an image,
            echo "<script>alert('File is not an image.');</script>"; // Show an alert message.
            $uploadOk = 0; // Set upload status as not okay.
        }
    
        // Check file size
        if ($_FILES["photo"]["size"] > 50000000) { // Check if the uploaded file is larger than 50MB.
            echo "<script>alert('Sorry, your file is too large.');</script>"; // Show an alert if the file is too large.
            $uploadOk = 0; // Set upload status as not okay.
        }
        
        // Allow certain file formats
        if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") { // Check if the file type is not one of the allowed types.
            echo "<script>alert('Sorry, only JPG, JPEG, PNG & GIF files are allowed.');</script>"; // Show an alert if the file type is not allowed.
            $uploadOk = 0; // Set upload status as not okay.
        }
        
        // Check if $uploadOk is set to 0 by an error
        if ($uploadOk == 0) { // Check if there was an error with the upload.
            echo "<script>alert('Sorry, your file was not uploaded.');</script>"; // Show an alert that the file was not uploaded.
        // if everything is ok, try to upload file
        } else {
            if (move_uploaded_file($_FILES["photo"]["tmp_name"], $targetFile)) { // Try to move the uploaded file to the target directory.
                $photo = $targetFile; // Save the file path in the $photo variable.
        
                // Update user profile with new profile picture in the database
                $updateQuery = "UPDATE user_profile SET photo='$photo' WHERE user_id=" . $userProfile['user_id']; // Prepare an SQL query to update the profile picture in the database.
                $updateResult = mysqli_query($conn, $updateQuery); // Execute the update query.
        
                if ($updateResult) { // Check if the update was successful.
                    echo "<script>alert('Profile picture updated successfully');</script>"; // Show an alert if the profile picture was updated successfully.
                } else {
                    echo "<script>alert('Failed to update profile picture');</script>"; // Show an alert if the profile picture update failed.
                }
            } else {
                echo "<script>alert('Sorry, there was an error uploading your file.');</script>"; // Show an alert if
            }
        }
    }        

   // Check if the form submission is for changing the password
if (isset($_POST['change_password'])) { // Check if the 'change_password' form was submitted.
    // Retrieve passwords from form
    $newPassword = isset($_POST['newPassword']) ? $_POST['newPassword'] : ''; // Get the new password from the form or set it to an empty string if not available.
    $confirmNewPassword = isset($_POST['confirmNewPassword']) ? $_POST['confirmNewPassword'] : ''; // Get the confirmation of the new password from the form or set it to an empty string if not available.

    // Validate new password
    if ($newPassword === $confirmNewPassword) { // Check if the new password and confirmation match.
        // Update the password in the database
        $updatePasswordQuery = "UPDATE user_profile SET password='$newPassword' WHERE user_id=" . $userProfile['user_id']; // Prepare an SQL query to update the password in the database.
        $updatePasswordResult = mysqli_query($conn, $updatePasswordQuery); // Execute the update query.

        if ($updatePasswordResult) { // Check if the update was successful.
            echo "<script>alert('Password changed successfully');</script>"; // Show an alert if the password was changed successfully.
        } else {
            echo "<script>alert('Failed to change password');</script>"; // Show an alert if the password change failed.
        }
    } else {
        echo "<script>alert('New password and confirm password do not match');</script>"; // Show an alert if the new password and confirmation do not match.
    }
}
}

// Refresh user profile data after update
$result = mysqli_query($conn, $query);
$userProfile = mysqli_fetch_assoc($result);
?>



<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">User Profile</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item active">User Profile</li>
                    </ol>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <section class="content">
        <div class="container">
            <div class="row">
                <!-- User Profile -->
                <div class="col-md-4">
                    <div class="card mb-4">
                        <div class="card-body">
                            <h5 class="card-title mb-4"></h5>
                            <div class="border-bottom mb-4 pb-3">
                                <img id="user-photo" src="<?php echo $userProfile['photo']; ?>" class="rounded-circle mx-auto d-block mb-3" alt="Profile Picture" style="max-width: 200px;">
                                <h4 class="card-title mb-2"><?php echo $userProfile['full_name']; ?></h4>
                                <p class="card-text mb-0"><?php echo $userProfile['bio'] ? $userProfile['bio'] : "What's on your mind? Be creative!"; ?></p>
                            </div>
                            <div class="mb-4">
                                <p class="card-text"><strong>Email:</strong> <?php echo $userProfile['email']; ?></p>
                                <p class="card-text"><strong>Phone Number:</strong> <?php echo $userProfile['phone_number']; ?></p>
                                <p class="card-text"><strong>Address:</strong> <?php echo $userProfile['address']; ?></p>
                                <p class="card-text"><strong>Birthdate:</strong> <?php echo date('F j, Y', strtotime($userProfile['birthdate'])); ?></p>
                                <p class="card-text"><strong>Gender:</strong> <?php echo $userProfile['gender']; ?></p>
                                <p class="card-text"><strong>Citizenship:</strong> <?php echo $userProfile['citizenship']; ?></p>
                                <p class="card-text"><strong>Nationality:</strong> <?php echo $userProfile['nationality']; ?></p>
                                <p class="card-text"><strong>Religion:</strong> <?php echo $userProfile['religion']; ?></p>
                                <p class="card-text"><strong>Marital Status:</strong> <?php echo $userProfile['marital_status']; ?></p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Editable Profile -->
                <div class="col-md-4">
                    <div class="card mb-4">
                        <div class="card-body">
                            <h5 class="card-title mb-4"></h5>
                            <form id="profile-form" action="" method="POST" enctype="multipart/form-data">
                                <input type="hidden" name="update_profile" value="1">
                                <div class="form-group">
                                    <label for="fullName">Full Name</label>
                                    <input type="text" class="form-control" id="fullName" name="fullName" value="<?php echo $userProfile['full_name']; ?>">
                                </div>
                                <div class="form-group">
                                    <label for="email">Email</label>
                                    <input type="email" class="form-control" id="email" name="email" value="<?php echo $userProfile['email']; ?>">
                                </div>
                                <div class="form-group">
                                    <label for="phoneNumber">Phone Number</label>
                                    <input type="tel" class="form-control" id="phoneNumber" name="phoneNumber" value="<?php echo $userProfile['phone_number']; ?>">
                                </div>
                                <div class="form-group">
                                    <label for="address">Address</label>
                                    <input type="text" class="form-control" id="address" name="address" value="<?php echo $userProfile['address']; ?>">
                                </div>
                                <div class="form-group">
                                    <label for="photo">Profile Picture</label>
                                    <input type="file" class="form-control-file" id="photo" name="photo">
                                    <small class="form-text text-muted">Select a new photo if you want to update your profile picture.</small>
                                </div>
                                <div class="form-group">
                                    <label for="birthdate">Birthdate</label>
                                    <input type="date" class="form-control" id="birthdate" name="birthdate" value="<?php echo $userProfile['birthdate']; ?>" max="<?php echo date('Y-m-d', strtotime('-13 years')); ?>">
                                    <small class="form-text text-muted">You must be at least 13 years old.</small>
                                </div>
                                <div class="form-group">
                                    <label for="bio">Bio</label> 
                                    <textarea class="form-control" id="bio" name="bio" rows="3"><?php echo $userProfile['bio']; ?></textarea> 
                                </div>
                                <div class="form-group">
                                    <label for="gender">Gender</label>
                                    <select class="form-control" id="gender" name="gender">
                                        <option value="Male" <?php if($userProfile['gender'] == 'Male') echo 'selected'; ?>>Male</option>
                                        <option value="Female" <?php if($userProfile['gender'] == 'Female') echo 'selected'; ?>>Female</option>
                                        <option value="Others" <?php if($userProfile['gender'] == 'Others') echo 'selected'; ?>>Others</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="citizenship">Citizenship</label>
                                    <input type="text" class="form-control" id="citizenship" name="citizenship" value="<?php echo $userProfile['citizenship']; ?>">
                                </div>
                                <div class="form-group">
                                    <label for="nationality">Nationality</label>
                                    <input type="text" class="form-control" id="nationality" name="nationality" value="<?php echo $userProfile['nationality']; ?>">
                                </div>
                                <div class="form-group">
                                    <label for="religion">Religion</label>
                                    <input type="text" class="form-control" id="religion" name="religion" value="<?php echo $userProfile['religion']; ?>">
                                </div>
                                <div class="form-group">
                                    <label for="maritalStatus">Marital Status</label>
                                    <select class="form-control" id="maritalStatus" name="maritalStatus">
                                        <option value="Single" <?php if($userProfile['marital_status'] == 'Single') echo 'selected'; ?>>Single</option>
                                        <option value="Married" <?php if($userProfile['marital_status'] == 'Married') echo 'selected'; ?>>Married</option>
                                        <option value="Divorced" <?php if($userProfile['marital_status'] == 'Divorced') echo 'selected'; ?>>Divorced</option>
                                        <option value="Widowed" <?php if($userProfile['marital_status'] == 'Widowed') echo 'selected'; ?>>Widowed</option>
                                    </select>
                                </div>
                                <!-- Submit button -->
                                <button type="submit" class="btn btn-primary">Save Changes</button>
                            </form>
                        </div>
                    </div>
                </div>
            <!-- Password change fields -->
<div class="col-md-4">
    <div class="card mb-4">
        <div class="card-body">
            <h5 class="card-title mb-4"></h5>
            <form id="password-form" action="" method="POST">
                <input type="hidden" name="change_password" value="1">
                
                <div class="form-group">
                    <label for="newPassword">New Password</label>
                    <input type="password" class="form-control" id="newPassword" name="newPassword" placeholder="New Password" required>
                </div>
                <div class="form-group">
                    <label for="confirmNewPassword">Confirm New Password</label>
                    <input type="password" class="form-control" id="confirmNewPassword" name="confirmNewPassword" placeholder="Confirm New Password" required>
                </div>
                <!-- Submit button -->
                <button type="submit" class="btn btn-primary">Change Password</button>
            </form>
        </div>
    </div>
</div>






<?php include('includes/script.php'); ?>
<?php include('includes/footer.php'); ?>

<script>
    // JavaScript to display selected profile picture
    document.getElementById('photo').addEventListener('change', function() {
        var reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('user-photo').src = e.target.result;
        }
        reader.readAsDataURL(this.files[0]);
    });
</script>
