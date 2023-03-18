<?php

session_start();
include "connect.php";

$hotel_id = isset($_GET["hotel_id"]) && $_GET["hotel_id"] != null?intval($_GET["hotel_id"]):null;
if($hotel_id == null) {
  header("Location: index.php");
}
//add offer to favorite list
$offer_id = isset($_GET["offer_id"]) && $_GET["offer_id"] != null?intval($_GET["offer_id"]):null;
if($offer_id != null && isset($_SESSION["customers"]) ) {
  $query_insert = "INSERT INTO favorite_list (customer_id, offer_id) VALUES(:customer_id, :offer_id)";
  $stmt = $con->prepare($query_insert);
  $data = [
      ':customer_id' => $_SESSION["customers"]["id"],
      ':offer_id'   => $offer_id
  ];
  $stmt->execute($data);
}
// hotel info
$query = "SELECT * FROM hotels WHERE id = ? ";
$stmt = $con->prepare($query);
$stmt->execute(array($hotel_id));
$hotel = $stmt->fetch();
// all offers 
$query = "SELECT * FROM offers WHERE hotel_id = ? ORDER BY id DESC ";
$stmt = $con->prepare($query);
$stmt->execute(array($hotel_id));
$offers = $stmt->fetchAll();
// var_dump(explode(";", $offers[0]["features"]));
// user favorite list
if( isset($_SESSION["customers"])) {
  $query = "SELECT * FROM favorite_list WHERE customer_id = ? ORDER BY id DESC ";
  $stmt = $con->prepare($query);
  $stmt->execute(array($_SESSION["customers"]["id"]));
  $favorits = $stmt->fetchAll();
  $list = [];
  if(!empty($favorits)) {    
    foreach($favorits as $favorite ) {
      $list[] = intval($favorite["offer_id"]);
    }
    // var_dump($list);
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
    <title><?php echo $hotel["name"]; ?></title>
    
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
    

    <div class="nav-search  ">
      <form action="booking.php" method="GET">
        <div class="container d-flex justify-content-between align-items-center">
          <div class="search-check-input">
            <input type="text" name="search" id="" placeholder="Enter a Hotel Name Or Destination" >
            <i class="fa fa-search"></i>
          </div>
          <div class="search-check-date">
            <label>Check-In</label>
            <input type="date" name="check_in" id="" required>
          </div>
          <div class="search-check-date">
            <label>Check-Out</label>
            <input type="date" name="check_out" id="" required>
          </div>
          <div class="search-check-select">
            <label>Rooms</label>
            <select name="rooms" id="" required>
              <option value="1">1</option>
              <option value="2" >2</option>
              <option value="3">3</option>
              <option value="4">4</option>
              <option value="5+">5+</option>
            </select>
          </div>
          <div class="search-check-select">
            <label>Guests</label>
            <select name="guests" id="" required>
              <option value="1">1 person</option>
              <option value="2" >2 persons</option>
              <option value="3">3 persons</option>
              <option value="4">4 persons</option>
              <option value="5+">5+ persons</option>
            </select>
          </div>
          <div class="do-search">
            <input type="submit" value="Search">
          </div>
        </div>
      </form>
    </div>

    <section id="hotel-offers">
      <h1 class="text-center my-5" ><?php echo $hotel["name"]; ?></h1>
    
      <div class="container-fluid ">
        <div class="row align-items-center mb-5">
          <?php if(!empty($offers)) { ?>
            <?php foreach($offers as $offer) { ?>
              
              <div class="col-md-6">
                <div class="hotel-offer">
                  <h2><?php echo $offer["type_of_rooms"] ?></h2>
                  <div class="d-flex">
                    <div class="offer-photo ">
                      <img class="fit fit-250" src="dist/images/rooms/<?php echo explode('|',$offer['photo'])[0] ?>" alt="">
                    </div>
                    <div class="offer-info">  
                      <span class="f-bold">Features</span>                
                      <ul>
                        <?php foreach(explode(";", $offer["features"]) as $feature) { ?>
                          <li><?php echo $feature ?></li>
                        <?php } ?>
                      </ul>
                      <div class="prices">
                        <div class="details">
                            <div> <span class="f-bold">Price per room</span> <span><?php echo $offer["price_per_room"] ?> SR </span> </div>
                            <div> <span class="f-bold">Number of rooms</span> <span><?php echo $offer["number_of_rooms"] ?> </span> </div>
                        </div>         
                                 
                      </div>

                      <div class="add-to-favoraite">
                        <?php if( isset($_SESSION["customers"])) {?>
                            <?php if(in_array($offer["id"], $list) ){ ?>
                              <a target="_blank" href="favorite-list.php" id="added"><i class="fa fa-check"></i><span>Added to favorite list</span></a>
                            <?php }else{ ?>
                              <a href="hotel-rooms.php?hotel_id=<?php echo $hotel_id ?>&offer_id=<?php echo $offer["id"] ?>"><i class="fa fa-heart"></i><span>Add to favorite</span></a>
                            <?php } ?>
                        <?php }else{?>  
                            <a href="login.php?hotel_id=<?php echo $hotel_id  ?>"><i class="fa fa-heart"></i><span>Login to <br> add to favorite</span></a>                        
                        <?php }?>
                      </div>
                    </div>
                  </div>                
                </div>
              </div>
            <?php } ?>
          <?php }else { ?>
            <div class="col-md-12">
              <p style="font-size: 25px; padding:20px; text-align:center">There are no rooms to show</p>  
            </div>
          <?php } ?>
        </div>
      </div>
    </section>
    
    

    <script src="dist/js/jquery-3.4.1.slim.min.js"></script>
    <script src="dist/js/bootstrap.bundle.min.js"></script>
    <script src="dist/js/main.js"></script>
</body>
</html>