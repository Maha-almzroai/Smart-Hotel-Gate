<?php
session_start();
include "connect.php";

if( isset($_SESSION['admins'])) {
  header('Location: profile-admin.php');
  exit();
}
if( isset($_SESSION['customers'])) {
  header('Location: profile-customer.php');
  exit();
}
if( isset($_SESSION['managers'])) {
  header('Location: profile-manager.php');
  exit();
}

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    $errors = [];

    $firstname      = $_POST['firstname'] != null ? $_POST['firstname'] : null;
    $lastname       = $_POST['lastname'] != null ? $_POST['lastname'] : null;
    $email          = $_POST['email'] != null ? $_POST['email'] : null;
    $phone          = $_POST['phone'] != null ? $_POST['phone'] : null;
    $password       = trim($_POST['password']) != null ? $_POST['password'] : null;

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
    }elseif( strlen((string)$phone) != 10) {
        $errors[] = 'Phone number must be <strong> 10 NUMBERS </strong>';
    }
    if($password == null) {
        $errors[] = 'Password is <strong> REQUIRED </strong>';
    }else {
      $uppercase = preg_match('@[A-Z]@', $password);
      $lowercase = preg_match('@[a-z]@', $password);
      $number    = preg_match('@[0-9]@', $password);
      $specialChars = preg_match('@[^\w]@', $password);

      if(!$uppercase || !$lowercase || !$number || !$specialChars || strlen($password) < 8) {
        $errors[] = 'Password should be at least 8 characters in length and should include at least one upper case letter, one number, and one special character.';
      }
    }

    $query = "SELECT * FROM customers WHERE email = ?";
    $stmt = $con->prepare($query);
    $stmt->execute(array($email));
    $results = $stmt->fetchall();
    if($results) {
        $errors[] = 'This Email is <strong> exist </strong> try another one';
    }    
    
    if(empty($errors)) {
        $query_insert = "INSERT INTO customers (firstname, lastname, email, phone, password) VALUES(:firstname, :lastname, :email, :phone, :password)";
        $stmt = $con->prepare($query_insert);
        $data = [
            ':firstname'  => $firstname,
            ':lastname'   => $lastname,
            ':email'      => $email,
            ':phone'      => $phone,
            ':password'   => password_hash($password, PASSWORD_DEFAULT),
        ];
        $stmt->execute($data);

        $query = "SELECT * FROM customers WHERE id = ?";
        $stmt = $con->prepare($query);
        $stmt->execute(array($con->lastInsertId()));
        $user = $stmt->fetch();
        $_SESSION['customers'] = [
          'id' => $user['id'],
          'name' => $user['firstname'],
      ];            
      header('Location: profile-customer.php'); // redirect to dashboard page
      exit();
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
    <title>New Register</title>
    
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
       
    </div>
    <nav>
      <!-- <a href="#">Profile</a> -->
      <a href="booking.php">Booking</a>
      <a href="about-us.php">About us</a>
      <a href="login.php"><i class="fa fa-user" ></i>Log in</a>
    </nav>
    <!-- start welcome -->
    <div class="welcome admin-profile-welcome">
      <span>New Register</span>
      <i class="fa fa-user"></i>
    </div>

    <div class="container-fluid">
      <div class="row justify-content-center">
        <div class="col-md-6">
          <div class="new-register-form">
            <form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="POST">
              <div class="input-container">
                <label for="firstname">First Name</label>
                <input type="text" name="firstname" id="firstname">
              </div>
              <div class="input-container">
                <label for="lastname">Last Name</label>
                <input type="text" name="lastname" id="lastname">
              </div>
              <div class="input-container">
                <label for="email">Email</label>
                <input type="email" name="email" id="email">
              </div>
              <div class="input-container">
                <label for="phone">Phone number</label>
                <input type="number"  name="phone" id="phone" >
              </div>
              <div class="input-container">
                <label for="password">Password</label>
                <input type="password" name="password" id="password">
              </div>
              <input type="submit" value="Sign In">
              <?php if(isset($errors) && $errors != null) { ?> 
                <?php foreach($errors as $error) {?>
                    <div class="text-center p-1"><span class="text-danger" ><?php echo $error; ?></span></div>
                <?php } ?>
              <?php } ?>
            </form>
          </div>
        </div>
      </div>
    </div>

    <script src="dist/js/jquery-3.4.1.slim.min.js"></script>
    <script src="dist/js/bootstrap.bundle.min.js"></script>
    <script src="dist/js/main.js"></script>
</body>
</html>