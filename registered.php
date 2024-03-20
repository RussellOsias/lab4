<?php
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

if (isset($_POST['AddUser'])) {
    // Your existing code to handle form submission

    // Check if registration was successful
    $registration_successful = true; // Placeholder for the condition to check if registration was successful
    if ($registration_successful) {
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
        $mail->addAddress($_POST['email']); // Recipient's email address
        $mail->isHTML(true);
        $mail->Subject = 'Registration Confirmation';
        $mail->Body = 'Dear ' . $_POST['full_name'] . ',<br><br>Thank you for registering on our website.<br><br>Sincerely,<br>Your Name';
        $mail->send();
    }
}
?> 


<!-- Your existing HTML content -->

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">

    <!-- User Modal-->
    <div class="modal fade" id="AddUserModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Add User</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <form action="home.php" method="POST" enctype="multipart/form-data">
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="">Profile Picture</label>
                            <input type="file" name="profile_picture" class="form-control-file" accept="image/*">
                        </div>

                        <div class="form-group">
                            <label for="">Full Name</label>
                            <input type="text" name="full_name" class="form-control" placeholder="Full Name" required>
                        </div>

                        <div class="form-group">
                            <label for="">Email</label>
                            <span></span>
                            <input type="text" name="email" class="form-control" placeholder="Email" required>
                        </div>

                        <div class="form-group">
                            <label for="">Phone Number</label>
                            <input type="text" name="phone_number" class="form-control" placeholder="Phone Number" required>
                        </div>

                        <div class="form-group">
                            <label for="">Address</label>
                            <input type="text" name="address" class="form-control" placeholder="Address" required>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="">Password</label>
                                    <input type="password" name="password" class="form-control" placeholder="Password" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="">Confirm Password</label>
                                    <input type="password" name="confirm_password" class="form-control" placeholder="Confirm Password" required>
                                </div>
                            </div>
                        </div>


                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" name="AddUser" class="btn btn-primary">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete User -->
    <div class="modal fade" id="DeletModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Delete User</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="home.php" method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="delete_id" class="delete_user_id">
                        <p>
                            Are you sure, you want to delete this data?
                        </p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" name="DeleteUserbtn" class="btn btn-primary">Yes, Delete.!</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Delete User -->

    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Dashboard</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">Registered Users</li>
                    </ol>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <section class="content">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <?php
                    // Display session status message if set
                    if (isset($_SESSION['status'])) {
                        echo "<h4>" . $_SESSION['status'] . "<h4>";
                        unset($_SESSION['status']); // Clear the status message after displaying it
                    }
                    ?>

                    <!-- Display error message if set -->
                    <?php
                    if (isset($_SESSION['error'])) {
                        echo "<div class='alert alert-danger'>" . $_SESSION['error'] . "</div>";
                        unset($_SESSION['error']); // Clear the error message after displaying it
                    }
                    ?>

                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Registered User</h3>
                            <a href="#" data-toggle="modal" data-target="#AddUserModal" class="btn btn-primary btn-sm float-right">Add User</a>
                        </div>
                        <!-- /.card-header -->

                        <div class="card-body">
                            <table id="example1" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Full Name</th>
                                        <th>Email</th>
                                        <th>Phone Number</th>
                                        <th>Address</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    // Query to retrieve user data from the database
                                    $query = "SELECT * FROM user_profile";
                                    $query_run = mysqli_query($conn, $query);

                                    if (mysqli_num_rows($query_run) > 0) {
                                        foreach ($query_run as $row) {
                                    ?>
                                            <tr>
                                                <td><?php echo $row['user_id']; ?></td>
                                                <td><?php echo $row['full_name']; ?></td>
                                                <td><?php echo $row['email']; ?></td>
                                                <td><?php echo $row['phone_number']; ?></td>
                                                <td><?php echo $row['address']; ?></td>
                                                <td>
                                                    <!-- Edit and delete buttons -->
                                                    <a href="registered-edit.php?user_id=<?php echo $row['user_id']; ?>" class="btn btn-info btn-sm">Edit</a>
                                                    <button type="button" value="<?php echo $row['user_id']; ?>" class="btn btn-danger btn-sm deletebtn">Delete</button>
                                                </td>
                                            </tr>
                                        <?php
                                        }
                                    } else {
                                        ?>
                                        <tr>
                                            <td>No Record Found</td>
                                        </tr>
                                    <?php
                                    }
                                    ?>

                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </section>

</div>

<?php include('includes/script.php'); ?>

<!-- JavaScript to handle delete button click event -->
<script>
    $(document).ready(function() {
        $('.deletebtn').click(function(e) {
            e.preventDefault();

            var user_id = $(this).val();
            //console.log(user_id);
            $('.delete_user_id').val(user_id);
            $('#DeletModal').modal('show');
        });
    });
</script>

<?php include('includes/footer.php'); ?>
