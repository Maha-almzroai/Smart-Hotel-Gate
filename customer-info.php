<?php 
session_start();
include "connect.php";
if( !isset($_SESSION['admins'])) {
    header('Location: login.php');
    exit();
}

$id     = isset($_GET['id']) && $_GET['id'] != null?$_GET['id']:null;

$query = "SELECT * FROM customers WHERE id = ?";
$stmt = $con->prepare($query);
$stmt->execute(array($id));
$customer = $stmt->fetch();

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="auther" content="">
    <meta name="description" content="">
    <title>View Customer Profile</title>
    
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
         <a href="profile-admin.php"><i class="fa fa-user" ></i> Admin</a>
       </div>
    </div>
    <nav>
      <a href="profile-admin.php">Profile</a>
      <a href="services.php">Services</a>
      <a href="about-us.php">About us</a>
      <a href="logout.php"><i class="fa fa-user" ></i>Log out</a>      
    </nav>

   
    
    <form action="" id="customer-profile"  method="POST">
      <div class="container-fluid">
        <div class="row">
          <div class="col-md-4">
            <div class="profile-info">
              <h2 class="h2-profile my-4">Customer Profile</h2>  
              <div class="input-container">
                <label for="firstname">First Name</label>
                <span style="width: 300px;"><?php echo $customer['firstname'] ?></span>
              </div>
              <div class="input-container">
                <label for="lastname">last Name</label>
                <span style="width: 300px;"><?php echo $customer['lastname'] ?></span>
              </div>
              <div class="input-container">
                <label for="email">Email</label>
                <span style="width: 300px;"><?php echo $customer['email'] ?></span>
              </div>
              <div class="input-container">
                <label for="phone">Phone number</label>
                <span style="width: 300px;"><?php echo $customer['phone'] ?></span>
              </div>
              <div class="input-container">
                <label for="phone">Gender</label>
                <span style="width: 300px;"><?php echo $customer['gender'] ?></span>
              </div>                          
              <div class="input-container">
                <label for="address">Address</label>
                <span style="width: 300px;"><?php echo $customer['address'] ?></span>
              </div>
            </div>
          </div>
          <div class="col-md-5">
            <h2 class="h2-profile my-4">Payment Information</h2>
            <div class="profile-info">
              <div class="input-container">
                <label for="card_number">Card Number</label>
                <span style="width: 300px;"><?php echo $customer['card_number'] ?></span>
              </div>
              <div class="input-container">
                <label for="expiration_day">Expiration Day</label>
                <span style="width: 300px;"><?php echo $customer['expiration_day'] ?></span>
              </div>
              <div class="input-container">
                <label for="cvc_code">CVC Code</label>
                <span style="width: 300px;"><?php echo $customer['cvc_code'] ?></span>
              </div>
            </div>
            
          </div>
          <div class="col-md-3"></div>
        </div>
      </div>
      
    </form>

    
 
    <script src="dist/js/jquery-3.4.1.slim.min.js"></script>
    <script src="dist/js/bootstrap.bundle.min.js"></script>
    <script src="dist/js/main.js"></script>

    <?php if(isset($_SESSION['success']) && $_SESSION['success'] != null) { ?>
        <script>
            alert("<?php echo $_SESSION['success']  ?>");
        </script>
    <?php } ?>
</body>
</html>
<?php
    unset($_SESSION['success']);
?>
