<?php 
session_start();
include "connect.php";

if(!isset($_SESSION['customers'])) {
    header('Location: index.php');
    exit();
}

$cancel     = isset($_GET['cancel']) && $_GET['cancel'] != null?$_GET['cancel']:null;
if($cancel != null) {
  $stmt = $con->prepare("DELETE FROM reservations WHERE customer_id = ?");
  $stmt->execute(array($_SESSION['customers']['id']));
  header('Location: profile-customer.php');
  exit();
}

$stmt = $con->prepare('SELECT * FROM reservations WHERE customer_id = ? ORDER BY id DESC LIMIT 1');
$stmt->execute(array($_SESSION['customers']['id']));
$reservation = $stmt->fetch();
if($reservation != null) {

    //get hotel id from name 
    $stmt = $con->prepare('SELECT * FROM hotels WHERE id = ?');
    $stmt->execute(array($reservation['hotel_id']));
    $hotel = $stmt->fetch();

    $offers = explode('-', $reservation['offers']);
    $type = '';
    $rooms_number = explode('-', $reservation['rooms_number']);
    $sum = 0;
    $conter = 0;
    foreach($offers as $offer) {
        $stmt = $con->prepare('SELECT * FROM offers WHERE id = ?');
        $stmt->execute(array($offer));
        $offer = $stmt->fetch();
        $type .=   $offer['type_of_rooms'] .' - ';    
        $sum += $rooms_number[$conter];
        $conter++;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="auther" content="">
    <meta name="description" content="">
    <title>Successful Reservation</title>
    
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
                <a href="profile-customer.php"><i class="fa fa-user" style="color: #6a5496;border-radius: 50%;border: 2px solid;padding: 10px;font-size: 35px;" ></i> Customer</a>
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
      <a href="booking.php">BooKing</a>
      <a href="about-us.php">About us</a>
      <?php if(!isset($_SESSION["admins"]) && !isset($_SESSION["managers"]) && !isset($_SESSION["customers"])) { ?>
      
      <a href="login.php"><i class="fa fa-user" ></i>Log In</a>      
      <?php }else { ?>  
      <div class="d-flex">
      <?php if( isset($_SESSION["customers"])) {?>
            <a href="favorite-list.php" class="pr-2 favorite mr-4"><i class="fa fa-heart" ></i>Favorite</a>      
        <?php }?>      
        <a href="logout.php"><i class="fa fa-user" ></i>Log out</a>      
      </div>
      <?php } ?>
    </nav>
    <div class=" ">

        <?php if($reservation == null) { ?>
          <h3 class="my-5 p-5 text-center">There is no reservation to show</h3>
        <?php }else { ?>
            <div class=" ">
                <div class=" ">
                  <a href="check-in.php?reservation_id=<?php echo $reservation["id"] ?>">
                    <img src="dist/images/nafaz.jpg" alt="" style="margin: 54px 0;width: 100%;">
                  </a>
                </div>
            </div>
        <?php } ?>

    </div>


    <script src="dist/js/jquery-3.4.1.slim.min.js"></script>
    <script src="dist/js/bootstrap.bundle.min.js"></script>
    <script src="dist/js/main.js"></script>
    <?php if(isset($_SESSION['success']) && $_SESSION['success'] != null) { ?>
        <script>
            alert("<?php echo $_SESSION['success']  ?>");
        </script>
    <?php } ?>
    <?php if(isset($_SESSION['success'])) { unset($_SESSION['success']); } ?>
</body>
</html>