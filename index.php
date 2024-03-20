<?php
// Start the session to manage user authentication
session_start();

// Check if the user is not authenticated, redirect to login page
if (!isset($_SESSION['auth'])) {
    $_SESSION['status'] = "Please log in to access this page";
    header('Location: login.php');
    exit(0);
}

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
?>

<?php
// Include authentication check
include('authentication.php');
include('includes/header.php');
include('includes/topbar.php'); // Include the topbar.php file here
include('includes/sidebar.php');
?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
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
                        <li class="breadcrumb-item active">Dashboard v1</li>
                    </ol>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <!-- Small boxes (Stat box) -->
            <div class="row">
                <div class="col-md-12">
                    <?php 
                    // Check if session status exists and display it
                    if(isset($_SESSION['status'])) {
                        echo "<div class='alert alert-info'>{$_SESSION['status']}</div>";
                        unset($_SESSION['status']); // Unset the session after displaying
                    }
                    ?>
                </div>

                <!-- Rest of your content goes here -->
                <div class="col-lg-3 col-6">
                    <!-- small box -->
                    <div class="small-box bg-info">
                        <div class="inner">
                            <h3>150</h3>

                            <p>New Orders</p>
                        </div>
                        <div class="icon">
                            <i class="ion ion-bag"></i>
                        </div>
                        <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
                <!-- ./col -->
                <div class="col-lg-3 col-6">
                    <!-- small box -->
                    <div class="small-box bg-success">
                        <div class="inner">
                            <h3>53<sup style="font-size: 20px">%</sup></h3>

                            <p>Bounce Rate</p>
                        </div>
                        <div class="icon">
                            <i class="ion ion-stats-bars"></i>
                        </div>
                        <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
                <!-- ./col -->
                <div class="col-lg-3 col-6">
                    <!-- small box -->
                    <div class="small-box bg-warning">
                        <div class="inner">
                            <h3>44</h3>

                            <p>User Registrations</p>
                        </div>
                        <div class="icon">
                            <i class="ion ion-person-add"></i>
                        </div>
                        <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
                <!-- ./col -->
                <div class="col-lg-3 col-6">
                    <!-- small box -->
                    <div class="small-box bg-danger">
                        <div class="inner">
                            <h3>65</h3>

                            <p>Unique Visitors</p>
                        </div>
                        <div class="icon">
                            <i class="ion ion-pie-graph"></i>
                        </div>
                        <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
                <!-- ./col -->
            </div>
            <!-- /.row -->
        </div><!-- /.container-fluid -->
    </section>

    <!-- Add Email Form -->
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Send Email</h3>
                </div>
                <!-- /.card-header -->
                <!-- form start -->
                <form role="form" action="send.php" method="POST">
                    <div class="card-body">
                        <div class="form-group">
                            <label for="recipient_email">Message someone?</label>
                            <input type="email" class="form-control" id="recipient_email" name="recipient_email" placeholder="Enter recipient email">
                        </div>
                        <div class="form-group">
                            <label for="subject">Whats the subject?</label>
                            <input type="text" class="form-control" id="subject" name="subject" placeholder="Enter email subject">
                        </div>
                        <div class="form-group">
                            <label for="message">Type here the message below</label>
                            <textarea class="form-control" id="message" name="message" rows="3" placeholder="Enter email message"></textarea>
                        </div>
                    </div>
                    <!-- /.card-body -->

                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary" name="send">Send</button>
                    </div>
                </form>
            </div>
            <!-- /.card -->
        </div>
    </div>
</div>

<?php 
include('includes/script.php');
?>
<?php 
include('includes/footer.php');
?>
