<?php 

session_start();
include "connect.php";

$search     = isset($_GET['search']) && $_GET['search'] != null?$_GET['search']:null;

$hotel_id   = isset($_GET['hotel_id']) && $_GET['hotel_id'] != null?$_GET['hotel_id']:null;
$check_in   = isset($_GET['check_in']) && $_GET['check_in'] != null?$_GET['check_in']:null;
$check_out  = isset($_GET['check_out']) && $_GET['check_out'] != null?$_GET['check_out']:null;
$rooms      = isset($_GET['rooms']) && $_GET['rooms'] != null?$_GET['rooms']:null;
$guests     = isset($_GET['guests']) && $_GET['guests'] != null?$_GET['guests']:null;

$min  = isset($_GET['min']) && $_GET['min'] != null ? $_GET['min'] : null;
$max  = isset($_GET['max']) && $_GET['max'] != null ? $_GET['max'] : null;
$popular_filters    = isset($_GET['popular_filters']) && $_GET['popular_filters'] != null ? $_GET['popular_filters'] : null;
$sort_by            = isset($_GET['sort_by']) && $_GET['sort_by'] != null ? $_GET['sort_by'] : "lowest-price";

//get hotel id from name 
// $stmt = $con->prepare('SELECT * FROM hotels WHERE hotels.name = ? OR hotels.location = ?');
// $stmt = $con->prepare('SELECT * FROM hotels WHERE name LIKE %h%');
// $stmt->execute(array($search, $search));
// $hotel = $stmt->fetch();
// if($hotel == null) {
//   $hotel['id'] = 0;
// }
//get offers
$likes ="'%";
if($popular_filters != null) {
    foreach(explode(';',$popular_filters) as $filter) {
        $likes .=  $filter .'%';
    }
}
$likes .="'";
// var_dump($popular_filters);
$query = 'SELECT * FROM offers WHERE hotel_id = ? AND number_of_rooms >= ? AND price_per_room >= ? AND price_per_room <= ? ';
// if( AND features LIKE . $likes;)
if( $popular_filters != null) {
  $query .= 'AND features LIKE '. $likes;
}
$stmt = $con->prepare($query);
$stmt->execute(array($hotel_id, $rooms, $min, $max));
$offers = $stmt->fetchAll();
// var_dump($offers);

$bookingUrl = 'search=' . $search . '&check_in=' . $check_in . '&check_out='. $check_out;
$bookingUrl .= '&rooms=' . $rooms . '&guests=' . $guests .'&price_1=' . $min .'&price_2=' . $max;
if( $popular_filters != null) {
  $bookingUrl .= '&popular_filters='. $popular_filters;
}
$bookingUrl .= '&sort_by=' . $sort_by;

$stmt= $con->prepare('SELECT * FROM hotels WHERE id= ?');
$stmt->execute(array($hotel_id));
$rank = $stmt->fetch();

$bookingUrl .= '&star=' . $rank['stars'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="auther" content="">
    <meta name="description" content="">
    <title>Search Results</title>
    
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
<a href="profile-customer.php"><i class="fa fa-user" style="color: #6a5496;border-radius: 50%;border: 2px solid;padding: 10px;font-size: 35px;"></i> Customer</a>
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
      <div class="d-flex">
      <?php if( isset($_SESSION["customers"])) {?>
            <a href="favorite-list.php" class="pr-2 favorite mr-4"><i class="fa fa-heart" ></i>Favorite</a>      
        <?php }?>      
        <a href="logout.php"><i class="fa fa-user" ></i>Log out</a>      
      </div>
      <?php } ?>
    </nav>
    
    <div class="nav-search  ">
      <form action="booking.php" method="GET">
        <div class="container d-flex justify-content-between align-items-center">
          <div class="search-check-input">
            <input type="text" name="search" id="" placeholder="Enter a Hotel Name Or Destination"  value="<?php echo isset($search) && $search !=null ? $search :"" ?>">
            <i class="fa fa-search"></i>
          </div>
          <div class="search-check-date">
            <label>Check-In</label>
            <input type="date" name="check_in" id="" required  value="<?php echo isset($check_in) && $check_in !=null ? $check_in :"" ?>">
          </div>
          <div class="search-check-date">
            <label>Check-Out</label>
            <input type="date" name="check_out" id="" required  value="<?php echo isset($check_out) && $check_out !=null ? $check_out :"" ?>">
          </div>
          <div class="search-check-select">
            <label>Rooms</label>
            <select name="rooms" id="" required>
              <option value="1" <?php echo isset($rooms) && $rooms !=null && $rooms == 1 ? "selected" :"" ?> >1</option>
              <option value="2" <?php echo isset($rooms) && $rooms !=null && $rooms == 2 ? "selected" :"" ?> >2</option>
              <option value="3" <?php echo isset($rooms) && $rooms !=null && $rooms == 3 ? "selected" :"" ?> >3</option>
              <option value="4" <?php echo isset($rooms) && $rooms !=null && $rooms == 4 ? "selected" :"" ?> >4</option>
              <option value="5+" <?php echo isset($rooms) && $rooms !=null && $rooms >= 5 ? "selected" :"" ?> >5+</option>
            </select>
          </div>
          <div class="search-check-select">
            <label>Guests</label>
            <select name="guests" id="" required>
              <option value="1" <?php echo isset($guests) && $guests !=null && $guests == 1 ? "selected" :"" ?> >1 person</option>
              <option value="2" <?php echo isset($guests) && $guests !=null && $guests == 2 ? "selected" :"" ?> >2 persons</option>
              <option value="3" <?php echo isset($guests) && $guests !=null && $guests == 3 ? "selected" :"" ?> >3 persons</option>
              <option value="4" <?php echo isset($guests) && $guests !=null && $guests == 4 ? "selected" :"" ?> >4 persons</option>
              <option value="5+" <?php echo isset($guests) && $guests !=null && $guests >= 5 ? "selected" :"" ?> >5+ persons</option>
            </select>
          </div>
          <div class="do-search">
            <input type="submit" value="Search">
          </div>
        </div>
      </form>
    </div>
        <a href="booking.php?<?php echo $bookingUrl ?>" style="color: #6a5496;font-size: 17px;font-weight: bold;margin: 13px;display: block;"><i class="fa fa-arrow-left pr-2"></i>Back to Booking</a>
    <div class="nav-search-heads">
      <div class="container">
        <div class="row">
          <div class="col-md-4"><h2>Room Photo</h2></div>
          <div class="col-md-5"><h2 class="pl-5">Room Info</h2></div>
          <div class="col-md-3"><h2 class="text-center">Price Per Night</h2></div>
        </div>
      </div>
    </div>

    <div class="rooms-results">
      <div class="container-fluid">
        
        <?php if($offers != null ) { ?>
          <!-- start form -->
        <form action="new-reservation.php" method="POST">
          <!-- start hidden inputs -->
          <input type="hidden" name="check_in" value="<?php echo $check_in !=null ? $check_in:"" ?>">
          <input type="hidden" name="check_out" value="<?php echo $check_out !=null ? $check_out:"" ?>">
          <input type="hidden" name="guests" value="<?php echo $guests !=null ? $guests:"" ?>">
          <input type="hidden" name="hotel_id" value="<?php echo $hotel_id !=null ? $hotel_id:"" ?>">
          <input type="hidden" name="search" value="<?php echo $search !=null ? $search:"" ?>">
          <input type="hidden" name="rooms_search" value="<?php echo $rooms !=null ? $rooms:"" ?>">
          <input type="hidden" name="min" value="<?php echo $min !=null ? $min:"" ?>">
          <input type="hidden" name="max" value="<?php echo $max !=null ? $max:"" ?>">
          <input type="hidden" name="popular_filters" value="<?php echo $popular_filters !=null ? $popular_filters:"" ?>">
          <!-- end hidden inputs -->
          <?php $counter = 0;  ?>
          <?php foreach($offers as $offer) {   ?>
            <?php $counter ++;  ?>
            <div class="row my-4 align-items-center">
              <div class="col-md-4 room-photo">

                  <div id="carouselExampleControls<?php echo $counter ?>" class="carousel slide" data-ride="carousel" >
                    <div class="carousel-inner">
                      <?php $first = true; ?>
                      <?php foreach(explode("|", $offer['photo']) as $photo) { ?>
                        <div class="carousel-item <?php echo $first == true?"active":""; ?>">
                          <img class="d-block fit fit-200 "  src="dist/images/rooms/<?php echo $photo ?>" alt="First slide">
                        </div>
                        <?php $first = false ?>
                      <?php } ?>
                    </div>
                    <?php if(count(explode("|", $offer['photo'])) > 1) { ?> 
                      <a class="c-controls carousel-control-prev" href="#carouselExampleControls<?php echo $counter ?>" role="button" data-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="sr-only">Previous</span>
                      </a>
                      <a class="c-controls carousel-control-next" href="#carouselExampleControls<?php echo $counter ?>" role="button" data-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="sr-only">Next</span>
                      </a>
                    <?php } ?>    
                  </div>
                
                <!-- <div id="carouselExampleControls1" class="carousel slide" data-ride="carousel">
                  <div class="carousel-inner">
                    <div class="carousel-item active">
                      <img class="d-block w-100" src="dist/images/rooms/1672963071-4.jpg" alt="First slide">
                    </div>
                    <div class="carousel-item">
                      <img class="d-block w-100" src="dist/images/rooms/1672963143-4.jpg" alt="Second slide">
                    </div>
                    <div class="carousel-item">
                      <img class="d-block w-100" src="dist/images/rooms/1673117078-4.jpg" alt="Third slide">
                    </div>
                  </div>
                  <a class="carousel-control-prev" href="#carouselExampleControls1" role="button" data-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="sr-only">Previous</span>
                  </a>
                  <a class="carousel-control-next" href="#carouselExampleControls1" role="button" data-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="sr-only">Next</span>
                  </a>
                </div> -->

              </div>
            
              <div class="col-md-5 room-details d-flex justify-content-between">
                  <h2><?php echo $offer["type_of_rooms"] ?></h2>
                  <ul>
                      <?php foreach(explode(";", $offer["features"]) as $feature) { ?>
                          <li><?php echo $feature ?></li>
                      <?php } ?>
                  </ul>
              </div>
              <div class="col-md-3 ">
                  <div class="room-price">
                      <h2>
                        
                          <?php echo $offer["price_per_room"] - $rank["discount"]  ?> <small> SR per room</small>
                      </h2>
                    
                  
                      <input type="hidden" name="offers[]" value="<?php echo $offer["id"] ?>">  
                      <Select name="rooms_number[]">
                        <option value="0" selected>0</option>
                        <?php
                          for($i = 1; $i <= $offer["number_of_rooms"]; $i++ ) {
                        ?>
                          <option value="<?php echo $i ?>" ><?php echo $i ?></option>
                        <?php    
                          }
                        ?>
                      </Select>      
                      <!-- <input type="submit" value="Booking Now"> -->
                      <!-- <a href="#"></a> -->
                    
                  </div>
              </div>
          </div>
          <?php } ?>
          <input type="submit" value="Booking Now">
        </form>
        <?php }else{ ?>
            <h2>There are no results to show</h2>
        <?php } ?>
        
        
        <!-- <div>
          <a href="#" id="back">Back</a>
        </div> -->
      </div>
    </div>


    <script src="dist/js/jquery-3.4.1.slim.min.js"></script>
    <script src="dist/js/bootstrap.bundle.min.js"></script>
    <script src="dist/js/main.js"></script>

    <script>
     /*  $("a.c-controls").on("click", function(e) {

        e.preventDefault();
      }); */
      /* $('.carousel').carousel({
          interval: 2000,
          wrap: true,
          keyboard: true
      }); */
    </script>
</body>
</html>