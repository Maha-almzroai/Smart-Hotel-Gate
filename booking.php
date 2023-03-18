<?php
session_start();
include "connect.php";

$search     = isset($_GET['search']) && $_GET['search'] != null?$_GET['search']:null;
$check_in   = isset($_GET['check_in']) && $_GET['check_in'] != null?$_GET['check_in']:null;
$check_out  = isset($_GET['check_out']) && $_GET['check_out'] != null?$_GET['check_out']:null;
$rooms      = isset($_GET['rooms']) && $_GET['rooms'] != null?$_GET['rooms']:0;
$guests     = isset($_GET['guests']) && $_GET['guests'] != null?$_GET['guests']:null;

$price_1            = isset($_GET['price_1']) && $_GET['price_1'] != null ? $_GET['price_1'] : 45;
$price_2            = isset($_GET['price_2']) && $_GET['price_2'] != null ? $_GET['price_2'] : 2455 ;
$star               = isset($_GET['star']) && $_GET['star'] != null ? $_GET['star'] : null;
$popular_filters    = isset($_GET['popular_filters']) && $_GET['popular_filters'] != null ? $_GET['popular_filters'] : null;
$sort_by            = isset($_GET['sort_by']) && $_GET['sort_by'] != null ? $_GET['sort_by'] : "lowest-price";

$likes ="'%";
if($popular_filters != null) {
    foreach($popular_filters as $filter) {
        $likes .=  $filter .'%';
    }
}
$likes .="'";
// prepare min and max price values
$min = $price_1 > $price_2 ?  $price_2 : $price_1;
$max = $price_1 > $price_2 ?  $price_1 : $price_2;
$same = $price_1 == $price_2 ? 'same':null;

//if the range inputs not equals 
$query = 'SELECT hotel_id FROM offers where number_of_rooms > '. $rooms ;
if($same == null) {
    $query .= ' AND price_per_room >= '. $min .' AND price_per_room <= '. $max ;
}elseif($same == 'same') {
    if($min == null) {
        $query .= ' AND price_per_room > 0';
    }else {
        $query .= ' AND price_per_room = '. $min;
    }    
}
$query .= ' AND features LIKE '. $likes .' GROUP BY hotel_id';

$stmt = $con->prepare($query);
$stmt->execute();
$offers = $stmt->fetchAll();
// var_dump($offers);

$ids = null;
if($offers != null ){
    $ids = '(';
    foreach($offers as $offer ) {
        $ids .= $offer['hotel_id'].",";
    }
    $ids = rtrim($ids, ',');
    $ids .= ')';
}
// var_dump($ids);
if($ids != null ) {
    $query = 'SELECT * FROM hotels WHERE id > 0';
    if($search != null) {
        $query .= ' AND ( name = "'. $search .'" OR location like "%'. $search .'%" )';
    }
    if($star != null) {
        $query .= ' AND stars = ' . $star;
    }
    $query .= ' AND id IN '. $ids ;
    // var_dump($query);
    $stmt = $con->prepare($query);
    $stmt->execute();
    $hotels = $stmt->fetchALL();
    // var_dump($query);
}else {
    $hotels = null;
}

// إذا المستخدم دخل الى صفحة البوكينغ مباشرة
if($check_in == null) {
    $hotels = null;
    $message = 'Please select your check in and check out dates';
}elseif ($check_in > $check_out) {
    $hotels = null;
    $message = '<span class="text-danger">Check out date mast be bigger than check in date</span>';
}
$url = [];
$counter = 0;

if($hotels != null) {
    foreach($hotels as $hotel ){
        $url[$counter] = 'hotel_id=' . $hotel['id'];
        if($search !=  null) {
            $url[$counter] .= '&search=' . $search;
        }
        if($min != null && $max != null){
            $url[$counter] .= '&min=' . $min . '&max=' . $max;
        }
        if($search != null) {
            $url[$counter] .= '&search=' . $search;
        }
        $url[$counter] .= '&check_in=' . $check_in . '&check_out=' . $check_out .'&rooms=' . $rooms . '&guests=' . $guests;
        if($popular_filters != null) {
            $url[$counter] .= '&popular_filters=' . implode(';',$popular_filters);
        }
        $url[$counter] .= '&sort_by=' . $sort_by;
        $counter++;
    }    
}
$counter =0;
/* foreach($hotels as $hotel ){
    echo var_dump($hotel) .'<br><br><br>';
    // echo $offer['hotel_id'] .'<br><br>';
} */
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="auther" content="">
    <meta name="description" content="">
    <title>Booking</title>
    
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
<a href="profile-customer.php"><i class="fa fa-user" style="color: #6a5496;border-radius: 50%;border: 2px solid;padding: 10px;font-size: 35px;"></i> Customer</a>            <?php }?>
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
      <!-- </form> -->
    </div>

    <!-- <form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="GET"> -->
        <div class="filter-container mb-2">
            <div class="container-fluid">
                <div class="row">
                <div class="col-md-3" style=" margin-left: -15px;">
                    <div class="filter-span">
                        <span>Filter</span>
                    </div>
                    <div class="price ">
                        <span>Price</span>
                        <!-- <input type="range" name="price-range" id="" min="145" max="750" value="150"> -->
                        <!-- <h4>Price Range Slider</h4> -->
                        <div class="price-content">
                            <div>
                            <label>Min</label>
                            <p id="min-value">25 SR</p>
                            </div>
                            <div>
                            <label>Max</label>
                            <p id="max-value">2500 SR</p>
                            </div>
                        </div>
                        <div class="range-slider">
                            <input name="price_1" type="range" class="min-price" value="<?php echo $min != null?$min:45 ?>" min="25" max="2500" step="10">
                            <input name="price_2" type="range" class="max-price" value="<?php echo $max != null?$max:2450 ?>" min="25" max="2500" step="10">
                        </div>
                    </div>
                    
                    <div class="hotel-class">
                        <span>Hotel Class</span>
                        <div class="d-flex justify-content-center">
                            <label for="one-star">
                                <i class="fa fa-star mx-1 one-star two-star three-star four-star "></i>
                            </label>
                            <input value="1" type="radio" name="star" id="one-star" <?php echo $star == 1?'checked':'' ?>>
                            <label for="two-star">
                                <i class="fa fa-star mx-1 two-star three-star four-star "></i>
                            </label>
                            <input value="2" type="radio" name="star" id="two-star" <?php echo $star == 2?'checked':'' ?>>
                            <label for="three-star">
                                <i class="fa fa-star mx-1 three-star four-star "></i>
                            </label>
                            <input type="radio" name="star" id="three-star" <?php echo $star == 3?'checked':'' ?>>
                            <label value="3" for="four-star">
                                <i class="fa fa-star mx-1 four-star "></i>
                            </label>
                            <input value="4" type="radio" name="star" id="four-star" <?php echo $star == 4 ?'checked':'' ?>>
                            <label for="five-star">
                                <i class="fa fa-star mx-1 "></i>
                            </label>
                            <input value="5" type="radio" name="star" id="five-star" <?php echo $star == 5?'checked':'' ?>>
                            
                        </div>
                    </div>
                    <div class="popular-filters">
                        <span>Popular Filters</span>
                        <div>                            
                            <input type="checkbox" name="popular_filters[]" id="free-wifi" value="free wifi" <?php echo $popular_filters != null && in_array("free wifi", $popular_filters)?'checked':'' ?>>
                            <label for="free-wifi">Free WiFi</label>
                        </div>
                        <div>
                            <input type="checkbox" name="popular_filters[]" id="free-breakfast" value="free breakfast" <?php echo $popular_filters != null && in_array("free breakfast", $popular_filters)?'checked':'' ?>>
                            <label for="free-breakfast">Free Breakfast</label>
                        </div>
                        <div>
                            <input type="checkbox" name="popular_filters[]" id="free-parking" value="free parking" <?php echo $popular_filters != null && in_array("free parking", $popular_filters)?'checked':'' ?>>
                            <label for="free-parking">Free Parking</label>
                        </div>
                        
                    </div>
                </div>
                <div class="col-md-9">
                    <div class="sort-by">
                        <span>Sort By</span>
                        <label for="most-popular" class="<?php echo $sort_by == "most-popular" ?'active':'' ?>">Most Popular</label>
                        <input type="radio" name="sort_by" id="most-popular" value="most-popular" <?php echo $sort_by == "most-popular" ?'checked':'' ?>>

                        <label for="lowest-price" class="<?php echo $sort_by == "lowest-price" || $sort_by == null?'active':'' ?>">Lowest Price</label>
                        <input type="radio" name="sort_by" id="lowest-price"  value="lowest-price" <?php echo $sort_by == "lowest-price" || $sort_by == null?'checked':'' ?>>

                        <label for="hightest-rated-guest" class="<?php echo $sort_by == "hightest-rated-guest" ?'active':'' ?>">Hightest Rated Guest</label>
                        <input type="radio" name="sort_by" id="hightest-rated-guest" value="hightest-rated-guest" <?php echo $sort_by == "hightest-rated-guest" ?'checked':'' ?>>

                        <!-- <input type="submit" value="Show results" style="display: inline-block;"> -->
                    </div>
                    <div class="filter-result">
                        <?php if(isset($hotels) && $hotels != null) {
                            foreach($hotels as $hotel) {
                            ?>
                            <div class="offer">
                                <div class="offer-image">
                                    <img src="dist/images/hotels/<?php echo $hotel["photo"] ?>" alt="" class="fit fit-300">
                                </div>
                                <div class="offer-info">
                                    <h2 class="text-center"><?php echo $hotel["name"] ?></h2>
                                    <p>Location :  <?php echo $hotel["location"] ?></p>
                                    
                                    <div class="stars" style="color: gold;">
                                        <p><span style="color: #000;">Hotel Class : </span>  
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
                                        </p>                                       
                                    </div>
                                    <a href="search-results.php?<?php echo $url[$counter]; ?>" class="d-block text-center"><i class="fa fa-hotel"></i> View Room </a>
                                    <?php $counter++; ?>
                                </div>
                            </div>
                            <?php
                            }
                        }else {
                            ?>
                            <h2 class="p-3">
                                <?php if(isset($message) && $message != null ) { ?>
                                        <?php echo $message ?>
                                    <?php }else{ ?>
                                        There are no results to show
                                <?php }  ?>
                                
                            </h2>
                            <?php
                        } ?>
                        
                    </div>
                </div>
                </div>
            </div>
        </div>
    </form>

    <script src="dist/js/jquery-3.4.1.slim.min.js"></script>
    <script src="dist/js/bootstrap.bundle.min.js"></script>
    <script src="dist/js/main.js"></script>
</body>
</html>