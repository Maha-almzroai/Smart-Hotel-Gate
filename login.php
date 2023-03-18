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
// var_dump($_GET['hotel_id']);
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    $errors = '';
    $email = $_POST['email'];
    $password = trim($_POST['password']);
    $table = $_POST['table'];
    
    // check if the user exist in database
    
    $stmt = $con->prepare("SELECT * FROM " . $table . " WHERE email = ? ");
    $stmt->execute(array($email));
    $users = $stmt->fetchall();
    // var_dump($users);
    foreach($users as $user) {
        if(password_verify($password, $user['password'])) {
            $_SESSION[$table] = [
                'id' => $user['id']
            ];  
            if($table == 'admins') {
               $_SESSION[$table]['name'] = $user['name']; 
              header('Location: profile-admin.php'); // redirect to dashboard page
              exit();
            }elseif($table == 'customers')  {
              if(isset($_SESSION["rooms_number"])) {
                // if user came from new reservation page
                header('Location: payment.php'); // redirect to dashboard page
              }elseif(isset($_POST['hotel_id']) && $_POST['hotel_id'] != null){
                header('Location: hotel-rooms.php?hotel_id='. $_POST['hotel_id']);
                // var_dump($_GET['hotel_id']);
              }else {                
                header('Location: profile-customer.php'); // redirect to dashboard page
              }              
              exit();
            }elseif($table == 'managers')  {
              $_SESSION[$table]['name'] = $user['name'];
              header('Location: profile-manager.php'); // redirect to dashboard page
              exit();
            }          

            // $errors =  $user['name'];
        }
    }
    $errors = 'Email or password is <strong>incorrect</strong>';
   
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="auther" content="">
    <meta name="description" content="">
    <title>Log In</title>
    
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
     
      <a href="booking.php">BooKing</a>
      <a href="about-us.php">About us</a>
      <a href="login.php"><i class="fa fa-user" ></i>Log in</a>
    </nav>
    
    <div class="container">
      <div class="row justify-content-center">
        <div class="login">
          <span class="login-span">Login <i class="fa fa-user"></i></span>
          <form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="POST">
            <?php if(isset($_GET['hotel_id']) && $_GET['hotel_id'] != null) { ?>
              <input type="hidden" name="hotel_id" value="<?php echo $_GET['hotel_id'] ?>">
            <?php } ?>
            <input type="email" name="email" id="" placeholder="Email ">
            <input type="password" name="password" id="" placeholder="Password">
            <select name="table" id="">
                <option value="admins">Admin</option>
                <option value="customers">Customer</option>
                <option value="managers">Manager</option>
            </select>
            <input type="submit" value="Log In">
          </form>
          <div class="errors">
              <?php if(isset($errors) && $errors != null) { ?> 
                <span class="text-danger" ><?php echo $errors; ?></span>
              <?php } ?>
          </div>
          <div id="or">
            <span>OR</span>
            <span></span>
          </div>
          <a href="register.php" id="new-register">New Register</a>
        </div>
      </div>
    </div>


    <script src="dist/js/jquery-3.4.1.slim.min.js"></script>
    <script src="dist/js/bootstrap.bundle.min.js"></script>
    <script src="dist/js/main.js"></script>
</body>
</html>