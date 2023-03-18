<?php 
session_start();
include "connect.php";
if( !isset($_SESSION['managers'])) {
    header('Location: login.php');
    exit();
}
// Delete offer
$id = isset($_GET['offer_id']) && $_GET['offer_id'] != null?$_GET['offer_id']:'';
if($id != null) {
  $query = 'SELECT * FROM offers WHERE id =?';
  $stmt = $con->prepare($query);
  $stmt->execute(array($id));
  $offer = $stmt->fetch();
  if($offer != null) {
    $stmt = $con->prepare("DELETE FROM offers WHERE id = ?");
    if($stmt->execute(array($id))) {
        unlink('dist/images/rooms/'.$offer['photo']);
    }
  }
}

// get hotel id
$stmt = $con->prepare('SELECT * FROM managers WHERE id = ?'); 
$stmt->execute(array($_SESSION['managers']['id']));
$manager = $stmt->fetch();
$hotel_id = $manager['hotel_id'];
// get all offers
$query = "SELECT * FROM offers WHERE hotel_id =? ORDER BY id DESC";
$stmt = $con->prepare($query);
$stmt->execute(array($hotel_id));
$offers = $stmt->fetchall();

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
         <a href="profile-manager.php"><i class="fa fa-user" style="color: #6a5496;border-radius: 50%;border: 2px solid;padding: 10px;font-size: 35px;"></i> Manager</a>
       </div>
    </div>
    <nav>
      <a href="profile-manager.php">Profile</a>
      <a href="about-us.php">About us</a>
      <a href="add-offer.php">Add Room</a>
      <a href="offers-list.php">List Rooms</a>
      <a href="logout.php"><i class="fa fa-user" ></i>Log out</a>      
    </nav>
    
    <h2 class="h2-profile my-4">List Rooms</h2>
    <div class="rooms-list">
      <div class="row">
          <?php 
          $counter = 1;
          foreach($offers as $offer) { ?>
            <div class="col-md-3">
                <div class="rome-nav">
                    Room <?php echo $counter ?>
                </div>
                <div class="profile-info">
                    <div  class="input-container">            
                        <label for="type_of_rooms">Type Of Rooms</label>
                        <span id="type_of_rooms"><?php echo $offer['type_of_rooms'] ?></span>
                    </div>
                    <div class="input-container">
                        <label for="number_of_rooms">Number Of Rooms</label>
                        <span  id="number_of_rooms"><?php echo $offer['number_of_rooms'] ?></span>
                    </div>
                    <div class="input-container">
                        <label for="rooms_price">rooms_price</label>
                        <span  id="rooms_price"><?php echo $offer['number_of_rooms'] * $offer['price_per_room'] ?></span>
                    </div>

                    <div class="rome-action">
                    <a href="offers-list.php?offer_id=<?php echo $offer["id"] ?>"  onclick="delete_offer()"><i class="fa fa-trash"></i></a>
                    <a href="add-offer.php?offer_id=<?php echo $offer["id"] ?>"><i class="fa fa-edit"></i></a>
                    </div>
                </div>            
            </div>
          <?php
          $counter += 1;
             } ?>   
             <?php if(empty($offers)) { ?>
                <h2 class="px-5">There are no offers to show</h2>   
             <?php } ?>     
      </div>
    </div>

    <script src="dist/js/jquery-3.4.1.slim.min.js"></script>
    <script src="dist/js/bootstrap.bundle.min.js"></script>
    <script src="dist/js/main.js"></script>
    <script>
        function delete_offer() {
            alert('Are you sure you want delete the offer permanently ?');
        }
    </script>
</body>
</html>