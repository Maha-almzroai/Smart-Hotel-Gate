<?php 
session_start();
include "connect.php";
if( !isset($_SESSION['customers'])) {
    header('Location: login.php');
    exit();
}
$query = "SELECT * FROM customers WHERE id = ?";
$stmt = $con->prepare($query);
$stmt->execute(array($_SESSION['customers']['id']));
$customer = $stmt->fetch();
// var_dump($customer);
if($_SERVER['REQUEST_METHOD'] == 'POST') {
        
    $errors = [];

    $firstname      = $_POST['firstname'] != null ? $_POST['firstname'] : null;
    $lastname       = $_POST['lastname'] != null ? $_POST['lastname'] : null;
    $email          = $_POST['email'] != null ? $_POST['email'] : null;
    $phone          = $_POST['phone'] != null ? $_POST['phone'] : null;
    $password       = trim($_POST['password']) != null ? $_POST['password'] : null;
    $gender     = trim($_POST['gender']);        
    $address    = trim($_POST['address']);        
    $card_number    = trim($_POST['card_number']) != 0 ? $_POST['card_number'] : null;
    $expiration_day = trim($_POST['expiration_day']) != null ? $_POST['expiration_day'] : null;
    $cvc_code       = trim($_POST['cvc_code']);

    if($firstname == null) {
        $errors[] = 'First Name is <strong> REQUIRED </strong>';
    }
    if($lastname == null) {
        $errors[] = 'Last Name is <strong> REQUIRED </strong>';
    }
    if($email == null) {
        $errors[] = 'Email is <strong> REQUIRED </strong>';
    }
    if($phone == null) {
        $errors[] = 'Phone number is <strong> REQUIRED </strong>';
    }


    $query = "SELECT * FROM customers WHERE email = ? AND id != ?";
    $stmt = $con->prepare($query);
    $stmt->execute(array($email, $_SESSION['customers']['id']));
    $results = $stmt->fetchall();
    if($results) {
        $errors[] = 'This Email is <strong> exist </strong> try another one';
    }   
    
    if($errors == null) {
        if($password != null ){
            $stmt = $con->prepare("UPDATE customers SET
                    firstname = ?,
                    lastname = ?,
                    email = ?,
                    phone = ?,
                    password = ?,
                    gender = ?,
                    address = ?,
                    card_number = ?,
                    expiration_day = ?,
                    cvc_code = ?
                WHERE id = ?  ");
            $password =password_hash($password, PASSWORD_DEFAULT);
            $stmt->execute(array($firstname, $lastname, $email, $phone, $password, $gender, $address, $card_number, $expiration_day, $cvc_code, $_SESSION['customers']['id']));
            if($stmt){
                $_SESSION['success'] = $firstname . ' ' . $lastname .' :Your Information Updated Successfully';
                header('Location: profile-customer.php');
                exit();
            } 
        }else {
            $stmt = $con->prepare("UPDATE customers SET
                    firstname = ?,
                    lastname = ?,
                    email = ?,
                    phone = ?,
                    gender = ?,
                    address = ?,
                    card_number = ?,
                    expiration_day = ?,
                    cvc_code = ?
                WHERE id = ?  ");

            $stmt->execute(array($firstname, $lastname, $email, $phone, $gender, $address, $card_number, $expiration_day, $cvc_code, $_SESSION['customers']['id']));
            if($stmt){
                $_SESSION['success'] = $firstname . ' ' . $lastname .' :Your Information Updated Successfully';
                header('Location: profile-customer.php');
                exit();
            } 
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
    <title>Profile Customer</title>
    
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
         <a href="profile-customer.php"><i class="fa fa-user" style="color: #6a5496;border-radius: 50%;border: 2px solid;padding: 10px;font-size: 35px;"></i> Customer</a>
        </div>
    </div>
    <nav>
      <a href="profile-customer.php">Profile</a>
      <a href="booking.php">BooKing</a>
      <a href="about-us.php">About us</a>
      <div class="d-flex">
        <a href="favorite-list.php" class="pr-2 favorite mr-4"><i class="fa fa-heart" ></i>Favorite</a>      
        <a href="logout.php"><i class="fa fa-user" ></i>Log out</a>      
      </div>
    </nav>
    <!-- start welcome -->
    <div class="welcome">
      <span>Hello, <?php echo $customer['firstname'] . ' ' . $customer['lastname'] ?></span>
      <i class="fa fa-user"></i>
      <a href="successful-reservation.php">View my reservation</a>
    </div>
   
    
    <form action="<?php echo $_SERVER['PHP_SELF'] ?>" id="customer-profile"  method="POST">
      <div class="container-fluid">
        <div class="row">
          <div class="col-md-4">
            <div class="profile-info">
              <h2 class="h2-profile my-4">My Profile</h2>  
              <div class="input-container">
                <label for="firstname">First Name</label>
                <input type="text" name="firstname" id="firstname" value="<?php echo $customer['firstname'] ?>">
              </div>
              <div class="input-container">
                <label for="lastname">last Name</label>
                <input type="text" name="lastname" id="lastname" value="<?php echo $customer['lastname'] ?>" >
              </div>
              <div class="input-container">
                <label for="email">Email</label>
                <input type="email" name="email" id="email" value="<?php echo $customer['email'] ?>" >
              </div>
              <div class="input-container">
                <label for="phone">Phone number</label>
                <input type="number" name="phone" id="phone" value="<?php echo $customer['phone'] ?>" >
              </div>
              <div class="input-container">
                <label for="password">Password</label>
                <input type="password" name="password" id="password">
              </div>
              <div class="input-container">
                <label for="gender">Gender</label>
                <select name="gender" id="gender">
                  <?php if($customer['gender'] == null) { ?>
                    <option value="">Select your gender</option>
                  <?php } ?>                  
                  <option value="male" <?php echo $customer['gender']== 'male'?'selected':'';  ?> >Male</option>
                  <option value="female" <?php echo $customer['gender']== 'female'?'selected':'';  ?> >Female</option>
                </select>
              </div>
              <div class="input-container">
                <label for="address">Address</label>
                <input type="text" name="address" id="address" value="<?php echo $customer['address'] ?>" >
              </div>
            </div>
          </div>
          <div class="col-md-5">
            <h2 class="h2-profile my-4">Payment Information</h2>
            <div class="profile-info">
              <div class="input-container">
                <label for="card_number">Card Number</label>
                <input type="number" name="card_number" id="card_number"  value="<?php echo $customer['card_number'] ?>" >
              </div>
              <div class="input-container">
                <label for="expiration_day">Expiration Day</label>
                <input type="date" name="expiration_day" id="expiration_day"  value="<?php echo $customer['expiration_day'] ?>" >
              </div>
              <div class="input-container">
                <label for="cvc_code">CVC Code</label>
                <input type="password" name="cvc_code" id="cvc_code"  value="<?php echo $customer['cvc_code'] ?>" >
              </div>
            </div>
            <?php if(isset($errors) && $errors != null) { ?> 
                <?php foreach($errors as $error) {?>
                    <div class="text-center p-1"><span class="text-danger" ><?php echo $error; ?></span></div>
                <?php } ?>
              <?php } ?>
          </div>
          <div class="col-md-3"></div>
        </div>
      </div>
      <input type="submit" value="Save" id="save">
    </form>

    
 
    <script src="dist/js/jquery-3.4.1.slim.min.js"></script>
    <script src="dist/js/bootstrap.bundle.min.js"></script>
    <script src="dist/js/main.js"></script>

    <?php if(isset($_SESSION['success']) && $_SESSION['success'] != null) { ?>
        <script>
            alert("<?php echo $_SESSION['success']  ?>");
        </script>
    <?php } ?>
</body>
</html>
<?php
    unset($_SESSION['success']);
?>
