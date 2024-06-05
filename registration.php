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
?>

<!-- Your existing HTML content -->

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">


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
                        <form action="signup.php" method="GET">
                            <button type="submit" class="btn btn-primary btn-sm float-right" name="create_account">Create account?</button>
                        </form>

                        </div>
                        <!-- /.card-header -->

                        <div class="card-body">
                            <table id="example1" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th> <!-- Table header for user ID -->
                                        <th>Full Name</th> <!-- Table header for user's full name -->
                                        <th>Email</th> <!-- Table header for user's email -->
                                        <th>Phone Number</th> <!-- Table header for user's phone number -->
                                        <th>Address</th> <!-- Table header for user's address -->
                                        <th>Action</th> <!-- Table header for action buttons -->
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
<a href="edit.php?user_id=<?php echo $row['user_id']; ?>" class="btn btn-info btn-sm">Edit</a>
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
