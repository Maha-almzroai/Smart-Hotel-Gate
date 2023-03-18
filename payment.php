<?php 

session_start();
include "connect.php";


$check_in     = $_SESSION["check_in"];
$check_out    = $_SESSION["check_out"];
$hotel        = $_SESSION["hotel"];
$offers       = $_SESSION["offers"];
$rooms_number = $_SESSION["rooms_number"];

$url = '?hotel='.$hotel.'&check_in='.$check_in.'&check_out='.$check_out;

$conter = 0;
$price = 0;
$details = [];
foreach($offers as $offer) {
    $stmt = $con->prepare('SELECT * FROM offers WHERE id = ?');
    $stmt->execute(array($offer));
    $offer = $stmt->fetch();
    $price += $offer['price_per_room'] * $rooms_number[$conter];
    $details[$conter]['type'] = $offer['type_of_rooms'] ;
    $details[$conter]['number'] = $rooms_number[$conter] ;
    $details[$conter]['price'] = $offer['price_per_room'] * $rooms_number[$conter] ;
    $conter++;
}

//get hotel id from name 
$stmt = $con->prepare('SELECT * FROM hotels WHERE id = ?');
$stmt->execute(array($hotel));
$hotel = $stmt->fetch();

if( isset($_SESSION["customers"])) {
    $stmt = $con->prepare('SELECT * FROM customers WHERE id = ?');
    $stmt->execute(array($_SESSION["customers"]["id"]));
    $customer = $stmt->fetch();
}

if($_SERVER["REQUEST_METHOD"] == "POST") {
  $errors = [];
  $first_name = isset($_POST['first_name']) && $_POST['first_name'] != null ?$_POST['first_name'] :null;
  $last_name = isset($_POST['last_name']) && $_POST['last_name'] != null ?$_POST['last_name'] :null;
  $email = isset($_POST['email']) && $_POST['email'] != null ?$_POST['email'] :null;
  $phone = isset($_POST['phone']) && $_POST['phone'] != null ?$_POST['phone'] :null;
  $card_number = isset($_POST['card_number']) && $_POST['card_number'] != null ?$_POST['card_number'] :null;
  $expiration_day = isset($_POST['expiration_day']) && $_POST['expiration_day'] != null ?$_POST['expiration_day'] :null;
  $cvc_code = isset($_POST['cvc_code']) && $_POST['cvc_code'] != null ?$_POST['cvc_code'] :null;  
  if($first_name == null) {
      $errors[] = 'First name is <strong> REQUIRED </strong>';
  }
  if($last_name == null) {
      $errors[] = 'Last name is <strong> REQUIRED </strong>';
  }
  if($email == null) {
      $errors[] = 'Email is <strong> REQUIRED </strong>';
  }
  if($phone == null) {
      $errors[] = 'Phone is <strong> REQUIRED </strong>';
  }
  if($card_number == null) {
      $errors[] = 'Card_number is <strong> REQUIRED </strong>';
  }
  if($expiration_day == null) {
      $errors[] = 'Expiration day is <strong> REQUIRED </strong>';
  }
  if($cvc_code == null) {
      $errors[] = 'cvc_code is <strong> REQUIRED </strong>';
  }

  $discount_check = isset($_POST['discount_check']) && $_POST['discount_check'] != null ?$_POST['discount_check'] :null;  
  $discount = isset($_POST['discount']) && $_POST['discount'] != null ?$_POST['discount'] :null;  

  if($discount_check == 'true' && $hotel['discount_code'] != $discount) {
      $errors[] = 'Discount code is <strong> Incorrect </strong>';  
  }
  $save_price =$price;
  if($discount_check == 'true' && $hotel['discount_code'] == $discount) {
      $save_price = $price - $hotel['discount'];  
  }
  if(empty($errors)) {
    $query_insert = "INSERT INTO reservations (price,customer_id, hotel_id, first_name, last_name, email, phone, card_number, expiration_day, cvc_code, check_in, check_out, offers, rooms_number) VALUES(:price,:customer_id, :hotel_id, :first_name, :last_name, :email, :phone, :card_number, :expiration_day, :cvc_code, :check_in, :check_out, :offers, :rooms_number)";
    $stmt = $con->prepare($query_insert);
    $data = [
      'price' => $save_price,
      'customer_id' => $_SESSION['customers']['id'],
      'hotel_id' => $hotel['id'],
      'first_name' => $first_name,
      'last_name' => $last_name,
      'email' => $email,
      'phone' => $phone,
      'card_number' => $card_number,
      'expiration_day' => $expiration_day,
      'cvc_code' => $cvc_code,
      'check_in' => $check_in,
      'check_out' => $check_out,
      'offers' => implode('-',$offers),
      'rooms_number' => implode('-',$rooms_number)
    ];
    $stmt->execute($data);
    if($stmt){ 
      // var_dump($con->lastInsertId()); 
        unset($_SESSION['offers']);
        unset($_SESSION['rooms_number']);
        $_SESSION['your_reservation'] = $con->lastInsertId();
        header('Location: successful-reservation.php');
        exit();
    } 
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
    <title>Payment</title>
    
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
<a href="profile-customer.php"><i class="fa fa-user" style="color: #6a5496;border-radius: 50%;border: 2px solid;padding: 10px;font-size: 35px;"></i> Customer</a>        <?php }?>
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
    <div class="container-fluid ">
      <div class="row ">
        <div class="col-md-7">
          <form action="<?php echo $_SERVER["PHP_SELF"].$url?>" method="POST">
            <div class="profile-info">
              <h2 class="h2-profile my-4">Billing Details</h2>
              <div class="input-container">
                <label for="first_name">First Name</label>
                <input type="text" name="first_name" id="first_name" value="<?php echo isset($customer) && $customer != null?$customer["firstname"]:""?>">
              </div>
              <div class="input-container">
                <label for="last_name">Last Name</label>
                <input type="text" name="last_name" id="last_name" value="<?php echo isset($customer) && $customer != null?$customer["lastname"]:""?>">
              </div>
              <div class="input-container">
                <label for="email">Email</label>
                <input type="email" name="email" id="email" value="<?php echo isset($customer) && $customer != null?$customer["email"]:""?>">
              </div>
              <div class="input-container">
                <label for="phone">Phone number</label>
                <input type="number" name="phone" id="phone" value="<?php echo isset($customer) && $customer != null?$customer["phone"]:""?>">
              </div>
            </div>
            <?php if($hotel["discount"] != null) {?> 
              <div id="discount-code">
                <input type="radio" name="discount_check" id="" value="true">
                <label for="discount">Discount Code</label>
                <input type="number" name="discount" id="discount">
              </div>
            <?php } ?>
            <h2 class="h2-profile my-4">Payment Method</h2>
            <div id="payment-method">
              <span>Credit Card</span>
              <div>
                <i class="fab fa-paypal" style="font-size: 20px;"></i>
                <i class="fab fa-cc-visa" style="font-size: 20px;"></i>
              </div>
            </div>
            <div class="profile-info">
              <div class="input-container">
                <label for="card_number">Card Number</label>
                <input type="number" name="card_number" id="card_number" value="<?php echo isset($customer) && $customer != null?$customer["card_number"]:""?>" >
              </div>
              <div class="input-container">
                <div  class="payment-page">            
                  <label for="expiration_day">Expiration Day</label>
                  <input type="date" name="expiration_day" id="expiration_day" value="<?php echo isset($customer) && $customer != null?$customer["expiration_day"]:""?>" >
                </div>
                <div  class="payment-page">            
                  <label for="cvc_code">CVC Code</label>
                  <input type="password" name="cvc_code" id="rome_price" value="<?php echo isset($customer) && $customer != null?$customer["cvc_code"]:""?>" >
                </div>     
              </div>        
            </div>
            <input type="submit" value="Pay" id="do-pay" onclick="confirm_pay()">
          </form>
          <?php if(isset($errors) && $errors != null) { ?> 
              <?php foreach($errors as $error) {?>
                  <div class="mx-5 my-2 p-1"><span class="text-danger" ><?php echo $error; ?></span></div>
              <?php } ?>
          <?php } ?>
        </div>
        <div class="col-md-5">
          <div class="billing-summery">
            <h2>Billing Summery</h2>
            <span class="billing-summery-border"></span>
            <?php foreach($details as $detail) { ?>
              <div class="billing-summery-details">
              <span><?php echo $detail['number']. '-' .$detail['type']?></span>
              <span><?php echo $detail['price']?> SR</span>
            </div>
            <?php } ?>  
            <div class="billing-summery-total">
              <span>total :</span>
              <span><?php echo $price?></span>
            </div>
            <div class="billing-summery-details">
              <span>Discount </span>
              <span><?php echo $hotel['discount'] != 0?$hotel['discount']  :0?> SR</span>
            </div>
            <div class="billing-summery-total">
              <span>With discount :</span>
              <span><?php echo $price - $hotel['discount']?></span>
            </div>
          </div>
        </div>
      </div>
    </div>


    <script src="dist/js/jquery-3.4.1.slim.min.js"></script>
    <script src="dist/js/bootstrap.bundle.min.js"></script>
    <script src="dist/js/main.js"></script>
    <script>
        function confirm_pay() {
            alert('Are you sure you ?');
        }
    </script>
</body>
</html>