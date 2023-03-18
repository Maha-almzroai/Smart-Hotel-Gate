<?php 
session_start();
include "connect.php";
if( !isset($_SESSION['customers'])) {
    header('Location: login.php');
    exit();
}
// delete offer from favorite list 
$offer_id = isset($_GET["offer_id"]) && $_GET["offer_id"] != null?intval($_GET["offer_id"]):null;
if($offer_id != null && isset($_SESSION["customers"]) ) {
    $stmt = $con->prepare("DELETE FROM favorite_list WHERE customer_id = ? AND offer_id = ?");
    $stmt->execute(array($_SESSION["customers"]["id"], $offer_id));
}
// get all Favorite list offers
$query = "SELECT * FROM favorite_list WHERE customer_id = ?";
$stmt = $con->prepare($query);
$stmt->execute(array($_SESSION['customers']['id']));
$all = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="auther" content="">
    <meta name="description" content="">
    <title>Favorite List</title>
    
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
         <a href="profile-customer.php"><i class="fa fa-user" ></i> Customer</a>
       </div>
    </div>
    <nav>
      <a href="profile-customer.php">Profile</a>
      <a href="booking.php">Booking</a>
      <a href="about-us.php">About us</a>
      <div class="d-flex">
        <a href="favorite-list.php" class="pr-2 favorite mr-4"><i class="fa fa-heart" ></i>Favorite</a>      
        <a href="logout.php"><i class="fa fa-user" ></i>Log out</a>      
      </div>
    </nav>
    <div class="container my-5">
      <h2 id="h2-favorite-list">Favorite List</h2>
      <div class="row ">
        <?php
            if(!empty($all)) {
                foreach($all as $favorite) {
                    // get offer information
                    $query = "SELECT * FROM offers WHERE id = ?";
                    $stmt = $con->prepare($query);
                    $stmt->execute(array($favorite['offer_id']));
                    $offer = $stmt->fetch();
                    // get hotel informaion
                    $query = "SELECT * FROM hotels WHERE id = ?";
                    $stmt = $con->prepare($query);
                    $stmt->execute(array($offer['hotel_id']));
                    $hotel = $stmt->fetch();

        ?>
                <div class="col-md-4">
                    <div class="favorite-place my-2">
                        <a href="favorite-list.php?offer_id=<?php echo $offer['id'] ?>"><i class="fa fa-trash"></i></a>
                        <div class="image-container">
                            <img src="dist/images/rooms/<?php echo explode('|',$offer['photo'])[0] ?>" class="fit fit-200" alt="">
                        </div>
                        <div class="d-flex justify-content-between">
                        <span ><span style="font-weight: bold;">Hotel Name</span> : <?php echo $hotel['name'] ?></span>
                        <div class="stars">
                            <?php if($hotel['stars'] == 1) { ?>
                                <span class="fa fa-star gold"></span>
                                <span class="fa fa-star"></span>
                                <span class="fa fa-star"></span>
                                <span class="fa fa-star"></span>
                                <span class="fa fa-star"></span>
                            <?php }elseif($hotel['stars'] == 2) { ?>
                                <span class="fa fa-star gold"></span>
                                <span class="fa fa-star gold"></span>
                                <span class="fa fa-star "></span>
                                <span class="fa fa-star"></span>
                                <span class="fa fa-star"></span>
                            <?php }elseif($hotel['stars'] == 3) { ?>
                                <span class="fa fa-star gold"></span>
                                <span class="fa fa-star gold"></span>
                                <span class="fa fa-star gold"></span>
                                <span class="fa fa-star"></span>
                                <span class="fa fa-star"></span>
                            <?php }elseif($hotel['stars'] == 4) { ?>
                                <span class="fa fa-star gold"></span>
                                <span class="fa fa-star gold"></span>
                                <span class="fa fa-star gold"></span>
                                <span class="fa fa-star gold"></span>
                                <span class="fa fa-star"></span>
                            <?php }elseif($hotel['stars'] == 5) { ?>
                                <span class="fa fa-star gold"></span>
                                <span class="fa fa-star gold"></span>
                                <span class="fa fa-star gold"></span>
                                <span class="fa fa-star gold"></span>
                                <span class="fa fa-star gold"></span>
                            <?php } ?>
                            
                        </div>  
                        </div>
                        <div class="d-flex justify-content-between">
                        <span ><span style="font-weight: bold;">Location :</span> <?php echo $hotel['location'] ?></span>
                        <span class="second"><?php echo $offer['number_of_rooms'] * $offer['price_per_room']?> SR</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span ><span style="font-weight: bold;">Rooms Type</span>: <?php echo $offer['type_of_rooms'] ?></span>                        
                            
                        </div>
                    </div>
                </div>   
        <?php            
                }
            }else {
        ?>
            <h2 class="mx-5">There are no favorite rooms to show</h2>
        <?php        
            }

        ?>

        
      </div>
    </div>


    <script src="dist/js/jquery-3.4.1.slim.min.js"></script>
    <script src="dist/js/bootstrap.bundle.min.js"></script>
    <script src="dist/js/main.js"></script>
</body>
</html>