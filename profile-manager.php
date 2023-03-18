<?php 
session_start();
include "connect.php";
/* if( !isset($_SESSION['managers'])) {
    header('Location: login.php');
    exit();
} */
$query = "SELECT * FROM managers WHERE id = ?";
$stmt = $con->prepare($query);
$stmt->execute(array($_SESSION['managers']['id']));
$manager = $stmt->fetch();
// get all managers
$query = "SELECT * FROM hotels WHERE id =?";
$stmt = $con->prepare($query);
$stmt->execute(array($manager['hotel_id']));
$hotel = $stmt->fetch();

if($_SERVER['REQUEST_METHOD'] == 'POST') {
        
    $errors = [];

    $name      = $_POST['name'] != null ? $_POST['name'] : null;
    $hotel_id       = $_POST['hotel_id'] != null ? $_POST['hotel_id'] : null;
    $email          = $_POST['email'] != null ? $_POST['email'] : null;
    $phone          = $_POST['phone'] != null ? $_POST['phone'] : null;
    $password       = trim($_POST['password']) != null ? $_POST['password'] : null; 

    if($name == null) {
        $errors[] = 'Name manager is <strong> REQUIRED </strong>';
    }
    if($hotel_id == null) {
        $errors[] = 'Hotel Name is <strong> REQUIRED </strong>';
    }
    if($email == null) {
        $errors[] = 'Email is <strong> REQUIRED </strong>';
    }
    if($phone == null) {
        $errors[] = 'Phone number is <strong> REQUIRED </strong>';
    }


    $query = "SELECT * FROM managers WHERE email = ? AND id != ?";
    $stmt = $con->prepare($query);
    $stmt->execute(array($email, $_SESSION['managers']['id']));
    $results = $stmt->fetchall();
    if($results) {
        $errors[] = 'This Email is <strong> exist </strong> try another one';
    }   
    
    if($errors == null) {
        if($password != null ){
            $stmt = $con->prepare("UPDATE managers SET
                    name = ?,
                    hotel_id = ?,
                    email = ?,
                    phone = ?,
                    password = ?                    
                WHERE id = ?  ");
            $password =password_hash($password, PASSWORD_DEFAULT);
            $stmt->execute(array($name, $hotel_id, $email, $phone, $password, $_SESSION['managers']['id']));
            if($stmt){
                $_SESSION['success'] = $name .' :Your Information Updated Successfully';
                header('Location: profile-manager.php');
                exit();
            } 
        }else {
            $stmt = $con->prepare("UPDATE managers SET
                    name = ?,
                    hotel_id = ?,
                    email = ?,
                    phone = ?                    
                WHERE id = ?  ");

            $stmt->execute(array($name, $hotel_id, $email, $phone, $_SESSION['managers']['id']));
            if($stmt){
                $_SESSION['success'] = $name .' :Your Information Updated Successfully';
                header('Location: profile-manager.php');
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
    <title>Profil Manager</title>
    
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
         <a href="profile-manager.php"><i class="fa fa-user" style="color: #6a5496;border-radius: 50%;border: 2px solid;padding: 10px;font-size: 35px;"></i> Manager</a>
       </div>
    </div>
    <nav>
      <a href="profile-manager.php">Profile</a>
      <a href="about-us.php">About us</a>
      <a href="add-offer.php">Add Room</a>
      <a href="offers-list.php">List rooms</a>
      <a href="logout.php"><i class="fa fa-user" ></i>Log out</a>      
    </nav>
    <!-- start welcome -->
    <div class="welcome">
      <span>Hello, <?php echo $manager['name'] ?></span>
      <i class="fa fa-user"></i>
    </div>
   
    <h2 class="h2-profile my-4">Hotel Manager Profile</h2>
    <div class="profile-info">
      <form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="POST">
        <div class="input-container">
          <label for="name">Name Manager</label>
          <input type="text" name="name" id="name" value="<?php echo $manager['name'] ?>" >
        </div>
        <div class="input-container">
          <label for="hotel_name">Hotel Name</label>
          <input type="hidden" name="hotel_id" id="<?php echo $manager['hotel_id'] ?>">
          <input type="text" value="<?php echo $hotel['name'] ?>" disabled='disabled'>
          
        </div>
        <div class="input-container">
          <label for="email">Email</label>
          <input type="email" name="email" id="email" value="<?php echo $manager['email'] ?>" >
        </div>
        <div class="input-container">
          <label for="password">Password</label>
          <input type="password" name="password" id="password">
        </div>
        <div class="input-container">
          <label for="phone">Phone number for hotel</label>
          <input type="number" name="phone" id="phone" value="<?php echo $manager['phone'] ?>" >
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
