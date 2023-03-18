<?php 

session_start();
include "connect.php";

$rooms_search = $_POST["rooms_search"];
$search = $_POST["search"];
$check_in = $_POST["check_in"];
$check_out = $_POST["check_out"];
$guests = $_POST["guests"];
$hotel_id = $_POST["hotel_id"];
$min = $_POST["min"];
$max = $_POST["max"];
$popular_filters = $_POST["popular_filters"];

$offers_with_zeros = $_POST["offers"];
$rooms_number_with_zeros = $_POST["rooms_number"];
// var_dump($hotel_id);
$url = 'hotel_id='.$hotel_id.'&search='.$search.'&check_in='.$check_in.'&check_out='.$check_out.'&rooms='.$rooms_search.'&guests='.$guests;
$url .= '&min='.$min.'&max='.$max.'&popular_filters='.$popular_filters;
$url2 = 'hotel='.$hotel_id.'&check_in='.$check_in.'&check_out='.$check_out;

$counter = 0;
$rooms_number = [];
$offers = [];
foreach ($rooms_number_with_zeros as $room) {
  if($room !== '0') {
    $offers[] = $offers_with_zeros[$counter];
    $rooms_number[] = $room;
  }
  $counter++;
}
if(empty($rooms_number)) {
  header('Location: search-results.php?'.$url);
  exit();
}

$_SESSION['offers']       = $offers;
$_SESSION['rooms_number'] = $rooms_number;
$_SESSION['check_in']     = $check_in;
$_SESSION['check_out']    = $check_out;
$_SESSION['hotel']        = $hotel_id;

// var_dump($_SESSION['offers']);
$conter = 0;
$price = 0;
$details = '';
foreach($offers as $offer) {
    $stmt = $con->prepare('SELECT * FROM offers WHERE id = ?');
    $stmt->execute(array($offer));
    $offer = $stmt->fetch();
    $price += $offer['price_per_room'] * $rooms_number[$conter];
    $details .= $rooms_number[$conter] .'-'.$offer['type_of_rooms'] .' ';
    $conter++;
}

$conter = 0;
$total_number = 0;
foreach($rooms_number as $room_number) {
    $total_number += $room_number;
    $conter++;
}
// var_dump($price);
// var_dump($total_number);

//get hotel id from name 
$stmt = $con->prepare('SELECT * FROM hotels WHERE id = ?');
$stmt->execute(array($hotel_id));
$hotel = $stmt->fetch();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="auther" content="">
    <meta name="description" content="">
    <title>New Reservation</title>
    
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
    <div class="container">
      <div class="row my-5">
        <div class="col-md-7">
          <h1 id="h1-new-reservation">New Reservation !</h1>
          <div class="reservation-details">
            <div class="check-date d-flex justify-content-between align-items-center">
              <div>
                <span>Check-in </span><span><?php echo $check_in ?></span>
              </div>
              <span class="dash"></span>
              <div>
                <span>Check-out </span><span><?php echo $check_out ?></span>
              </div>            
            </div>
            <div class="check-table">
              <table>
                <tr>
                  <th> Name</th>
                  <td class="text-center"><?php echo $hotel["name"] ?></td>
                </tr>
                <tr>
                  <th>Email</th>
                  <td class="text-center"><?php echo $hotel["email"] ?></td>
                </tr>
                <tr>
                  <th>Phone</th>
                  <td class="text-center"><?php echo $hotel["phone"] ?></td>
                </tr>
                <tr>
                  <th>Location</th>
                  <td class="text-center"><?php echo $hotel["location"] ?></td>
                </tr>
              </table>
            </div>
          </div>
          <div class="reservation-actions d-flex justify-content-around align-items-center my-3">
            <a href="search-results.php?<?php echo $url ?>">Edit</a>
            <a href="index.php">Cancel</a>
          </div>
        </div>
        <div class="col-md-5 my-5">
          <div class="new-reservation-summery">
            <span class="summery-title"><?php echo $price ?> SR</span>
            <div>
              <span>Rooms :</span>
              <span><?php echo $total_number ?></span>
            </div>
            <div>
              <span>Rooms Details:</span>
              <span><?php echo $details ?></span>
            </div>
            <!-- <div>
              <span>Extra :</span>
              <span>Breakfast</span>
            </div> -->
          </div>
          <?php if(isset($_SESSION['customers'])) { ?>
            <a href="payment.php" id="confirm-and-pay">Confirm And Pay</a>
          <?php }else { ?>
            <a href="login.php" id="confirm-and-pay">Login First</a>
          <?php } ?>
          
        </div>
      </div>
    </div>


    <script src="dist/js/jquery-3.4.1.slim.min.js"></script>
    <script src="dist/js/bootstrap.bundle.min.js"></script>
    <script src="dist/js/main.js"></script>
</body>
</html>