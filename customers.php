<?php 
session_start();
include "connect.php";
if( !isset($_SESSION['admins'])) {
    header('Location: login.php');
    exit();
}


$query = "SELECT * FROM customers ORDER BY id DESC";
$stmt = $con->prepare($query);
$stmt->execute();
$customers = $stmt->fetchall();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="auther" content="">
    <meta name="description" content="">
    <title>List Hotels</title>
    
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
    
    <h2 class="h2-profile my-4">List Customers</h2>

    <table id="admin-customer">
        <thead>
            <tr>
                <th>Account Name</th>
                <th>E-mail</th>
                <th>Phone</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($customers as $customer) { ?>
                <tr>
                    <td><i class="fa fa-user"></i> <?php echo $customer["firstname"] ." ". $customer["lastname"] ?></td>
                    <td><?php echo $customer["email"]  ?></td>
                    <td><?php echo $customer["phone"]  ?></td>
                    <td><a href="customer-info.php?id=<?php echo $customer["id"]  ?>">View More</a></td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
    <script src="dist/js/jquery-3.4.1.slim.min.js"></script>
    <script src="dist/js/bootstrap.bundle.min.js"></script>
    <script src="dist/js/main.js"></script>

</body>
</html>