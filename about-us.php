<?php

session_start();
include "connect.php";

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="auther" content="">
    <meta name="description" content="">
    <title>About Us</title>
    
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
       <?php if( isset($_SESSION["customers"])) {?>
            <a href="profile-customer.php">Customer</a>
        <?php }?>
         
       </div>
    </div>
    <nav>
        <?php if( isset($_SESSION["admins"])) {?>
            <a href="profile-admin.php">Profile</a>
        <?php }elseif( isset($_SESSION["managers"])) {?>
            <a href="profile-manager.php">Profile</a>
        <?php }elseif( isset($_SESSION["customers"])) {?>
            <a href="profile-customer.php">Profile</a>
        <?php }?>
      <a href="booking.php">Booking</a>
      <a href="about-us.php">About us</a>
      <?php if(!isset($_SESSION["admins"]) && !isset($_SESSION["managers"]) && !isset($_SESSION["customers"])) { ?>
      
      <a href="login.php"><i class="fa fa-user" ></i>Log In</a>      
      <?php }else { ?>  
      <!-- <div class="d-flex"> -->
        <!-- <a href="#" class="pr-2 favorite mr-4"><i class="fa fa-heart" ></i>Favorite</a>       -->
        <a href="logout.php"><i class="fa fa-user" ></i>Log out</a>      
      <!-- </div> -->
      <?php } ?>
    </nav>
    
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-6">
          <div class="contact-us">
            <span class="left-border"></span>
            <h2>Contact Us</h2>
            <div class="contact-us-info">
              <span>Email Address</span>
              <p>smarthotelgate@gmail.com</p>
              <span>Phone Number</span>
              <p>+966 556 677700</p>
            </div>
            <div class="contact-us-links">
              <a href="#"><i class="fab fa-facebook-f "></i>@Smart_Hotel_Gate</a>
              <a href="#"><i class="fab fa-instagram"></i>@Smart_Hotel_Gate</a>
              <a href="#"><i class=" fab fa-twitter"></i>@Smart_Hotel_Gate</a>
            </div>
          </div>
        </div>
        <div class="col-md-6">
          <div class="about-us">
            <span>About Us</span>
            <p>Our proposed website provides reservations for various hotels in one website and provides many advantages , In our journey to achieve this, we practice strong beliefs and actions that respect the diversity of people, the community, ethics and the planet.</p>
          </div>
        </div>
      </div>
    </div>


    <script src="dist/js/jquery-3.4.1.slim.min.js"></script>
    <script src="dist/js/bootstrap.bundle.min.js"></script>
    <script src="dist/js/main.js"></script>
</body>
</html>