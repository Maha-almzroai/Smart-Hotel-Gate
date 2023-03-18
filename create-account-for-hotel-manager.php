<?php 
session_start();
include "connect.php";
if( !isset($_SESSION['admins'])) {
    header('Location: login.php');
    exit();
}
$query = "SELECT * FROM hotels ORDER BY id DESC";
$stmt = $con->prepare($query);
$stmt->execute();
$hotels = $stmt->fetchall();

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    // $errors     = [];
    $name       =  $_POST['name'] != null ? $_POST['name'] : null;
    $hotel_id   =  $_POST['hotel_id'] != null ? $_POST['hotel_id'] : null;
    $email      =  $_POST['email'] != null ? $_POST['email'] : null;
    $password   =  $_POST['password'] != null ? $_POST['password'] : null;
    $phone      =  $_POST['phone'] != null ? $_POST['phone'] : null;

    if($name == null) {
        $errors[] = 'Name manager is <strong> REQUIRED </strong>';
    }
    if($hotel_id == null) {
        $errors[] = 'Hotel Name is <strong> REQUIRED </strong>';
    }
    if($email == null) {
        $errors[] = 'Email is <strong> REQUIRED </strong>';
    }
    if($password == null) {
        $errors[] = 'Password is <strong> REQUIRED </strong>';
    }
    if($phone == null) {
        $errors[] = 'Phone is <strong> REQUIRED </strong>';
    }

    $query = "SELECT * FROM managers WHERE email = ?";
    $stmt = $con->prepare($query);
    $stmt->execute(array($email));
    $results = $stmt->fetchall();
    if($results) {
        $errors[] = 'This Email is <strong> exist </strong> try another one';
    }   

    if(empty($errors)) {
        $query_insert = "INSERT INTO managers (name, hotel_id, email, phone, password) VALUES(:name, :hotel_id, :email, :phone, :password)";
        $stmt = $con->prepare($query_insert);
        $data = [
            ':name'       => $name,
            ':hotel_id'   => $hotel_id,
            ':email'      => $email,
            ':phone'      => $phone,
            ':password'   => password_hash($password, PASSWORD_DEFAULT),
        ];
        $stmt->execute($data);
        if($stmt){
            $_SESSION['success'] = 'You have creatred a new manager account Successfully';
            header('Location: create-account-for-hotel-manager.php');
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
    <title>Create Account For Hotel Manager</title>
    
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
         <a href="profile-admin.php"><i class="fa fa-user"  style="color: #6a5496;border-radius: 50%;border: 2px solid;padding: 10px;font-size: 35px;"></i> Admin</a>       </div>
    </div>
    <nav>
      <a href="profile-admin.php">Profile</a>
      <a href="services.php">Services</a>
      <a href="about-us.php">About us</a>
      <a href="logout.php"><i class="fa fa-user" ></i>Log out</a>      
    </nav>
    <!-- start welcome -->
    <div class="welcome">
      <span>Hello, <?php echo $_SESSION['admins']['name'] ?></span>
      <i class="fa fa-user"></i>
    </div>


    <h2 class="h2-profile my-4">Create Account For Hotel Manager</h2>
    <div class="profile-info">
      <form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="POST">
        <div class="input-container">
          <label for="name">Name Manager</label>
          <input type="text" name="name" id="name">
        </div>
        <div class="input-container">
          <label for="hotel_name">Hotel Name</label>
          <select name="hotel_id" id="hotel_name">
            <?php foreach($hotels as $hotel) { ?>
              <option value="<?php echo $hotel['id'] ?>"><?php echo $hotel['name'] ?></option>
            <?php } ?>
          </select>
        </div>
        <div class="input-container">
          <label for="email">Email</label>
          <input type="email" name="email" id="email">
        </div>
        <div class="input-container">
          <label for="password">Password</label>
          <input type="password" name="password" id="password">
        </div>
        <div class="input-container">
          <label for="phone">Phone number for hotel</label>
          <input type="number" name="phone" id="phone">
        </div>
        
        <input type="submit" value="Save" id="save">
      </form>
    </div>
    <?php if(isset($errors) && $errors != null) { ?> 
            <?php foreach($errors as $error) {?>
                <div class="mx-5 my-2 p-1"><span class="text-danger" ><?php echo $error; ?></span></div>
            <?php } ?>
          <?php } ?>
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