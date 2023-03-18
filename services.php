<?php 
session_start();
include "connect.php";
if( !isset($_SESSION['admins'])) {
    header('Location: login.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="auther" content="">
    <meta name="description" content="">
    <title>Services</title>
    
    <link rel="stylesheet" href="dist/css/all.css">
    <link rel="stylesheet" href="dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="dist/css/main.css">
</head>
<body>

    <div class="header d-flex justify-content-between align-items-center">       
       <div class="d-flex align-items-center">
        <img src="dist/images/logo.png" class="small-logo" alt="">
        <a href="index.php"><span>Smart Hotel Gate</span></a>
       </div>
       <div>
         <a href="profile-admin.php"><i class="fa fa-user"  style="color: #6a5496;border-radius: 50%;border: 2px solid;padding: 10px;font-size: 35px;"></i> Admin</a>       </div>
    </div>
    <nav>
      <a href="profile-admin.php">Profile</a>
      <a href="services.php">Services</a>
      <a href="about-us.php">About us</a>
      <a href="logout.php"><i class="fa fa-user" ></i>Log out</a>      
    </nav>
    <!-- start welcome -->
    <div class="welcome admin-profile-welcome">
      <span>Hello, <?php echo $_SESSION['admins']['name'] ?></span>
      <i class="fa fa-user"></i>
    </div>

    <div style="position: relative;top: -45px;">
      <h1 class="h1-profile mb-5">
        Admin Services
      </h1>

      <div class="admin-links">
        <div class="link">
          <div class="hide"></div>
          <a href="manage-hotels.php">Manage Hotels</a>
        </div>
        <div class="link">
          <div class="hide"></div>
          <a href="create-account-for-hotel-manager.php">Create Account For Hotel Manager</a>
        </div>
        <div class="link">
          <div class="hide"></div>
          <a href="hotels-list.php">List Hotels</a>
        </div>
        <div class="link">
          <div class="hide"></div>
          <a href="customers.php">View Customer Account</a>
        </div>
        
        
      </div>
    </div>

    <script src="dist/js/jquery-3.4.1.slim.min.js"></script>
    <script src="dist/js/bootstrap.bundle.min.js"></script>
    <script src="dist/js/main.js"></script>
</body>
</html>