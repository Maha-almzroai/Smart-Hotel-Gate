<?php 

session_start();
include "connect.php";
$query = "SELECT * FROM hotels ORDER BY id DESC ";
$stmt = $con->prepare($query);
$stmt->execute();
$hotels = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="auther" content="">
    <meta name="description" content="">
    <title>Home</title>
    
    <link rel="stylesheet" href="dist/css/all.css">
    <link rel="stylesheet" href="dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="dist/css/main.css">
</head>
<body>

    <div class="header d-flex justify-content-between align-items-center">       
       <div class="d-flex align-items-center" style="width: 100%;">
        <img src="dist/images/logo.png" class="big-logo" alt="">
        <span id="big-logo">Smart Hotel Gate</span>
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
      <a href="about-us.php" class="pr-2 "><i class="fas fa-phone-alt"></i>Contact us</a> 
      <?php if(!isset($_SESSION["admins"]) && !isset($_SESSION["managers"]) && !isset($_SESSION["customers"])) { ?>
      
        <a href="login.php"><i class="fa fa-user" ></i>Log In</a>      
      <?php }else { ?>  
        <a href="logout.php"><i class="fa fa-user" ></i>Log out</a>      
      <?php } ?>
    </nav>

    <div class="my-5 py-2">
      <h1 class="h1-home">
        Unique hotel booking for you !
      </h1>
      <p class="p-home">
        <span>We offering to you a special hotel booking experience</span>
        <span>The best rate, exclusive discounts and a smart way to check-in into the hotel</span>
      </p>
    </div>
    
    <div class="nav-search mx-3">
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


    <section class="my-5" id="hotels">
      <h1 class="py-5 text-center"  >ALL Hotels </h1>
      <div class="container">
        <div class="row align-items-center justify-content-center">
          <?php foreach($hotels as $hotel) { ?>              
            <div class="col-md-4">
              <div class="hotel m-3">
                <div class="hotel-image">
                  <img src="dist/images/hotels/<?php echo $hotel["photo"]?> " alt="" class="fit fit-300">
                </div>
                <div class="hotel-info">
                  <h3><a href="hotel-rooms.php?hotel_id=<?php echo $hotel["id"] ?>"><?php echo $hotel["name"]?></a></h3>
                  <ul>
                    <li><span>Hotel Location : </span><span><?php echo $hotel["location"]?></span></li>
                    <li><span style="padding-left: 0;">Hotel Rank : </span> 
                      <div class="stars">
                        <?php if($hotel["stars"] == 1) { ?>
                            <i class="fa fa-star"></i>                    
                        <?php }elseif($hotel["stars"] == 2) { ?>
                          <i class="fa fa-star"></i>   
                          <i class="fa fa-star"></i> 
                        <?php }elseif($hotel["stars"] == 3) { ?>
                          <i class="fa fa-star"></i>   
                          <i class="fa fa-star"></i>   
                          <i class="fa fa-star"></i> 
                        <?php }elseif($hotel["stars"] == 4) { ?>
                          <i class="fa fa-star"></i>   
                          <i class="fa fa-star"></i>   
                          <i class="fa fa-star"></i>   
                          <i class="fa fa-star"></i>  
                        <?php }elseif($hotel["stars"] == 5) { ?>
                          <i class="fa fa-star"></i>   
                          <i class="fa fa-star"></i>   
                          <i class="fa fa-star"></i>   
                          <i class="fa fa-star"></i>   
                          <i class="fa fa-star"></i>   
                        <?php } ?>
                      </div>
                    </li>
                  </ul>
                </div>
              </div> 
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