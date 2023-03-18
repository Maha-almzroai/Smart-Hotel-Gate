<?php 
session_start();
include "connect.php";
if( !isset($_SESSION['admins'])) {
    header('Location: login.php');
    exit();
}
$query = "SELECT * FROM admins WHERE id = ?";
$stmt = $con->prepare($query);
$stmt->execute(array($_SESSION['admins']['id']));
$admin = $stmt->fetch();

if($_SERVER['REQUEST_METHOD'] == 'POST') {
        
  $errors = [];

  $name           = $_POST['name'] != null ? $_POST['name'] : null;
  $email          = $_POST['email'] != null ? $_POST['email'] : null;
  $phone          = $_POST['phone'] != null ? $_POST['phone'] : null;
  $password       = trim($_POST['password']) != null ? $_POST['password'] : null; 

  if($name == null) {
      $errors[] = 'Name manager is <strong> REQUIRED </strong>';
  }
  if($email == null) {
      $errors[] = 'Email is <strong> REQUIRED </strong>';
  }
  if($phone == null) {
      $errors[] = 'Phone number is <strong> REQUIRED </strong>';
  }


  $query = "SELECT * FROM admins WHERE email = ? AND id != ?";
  $stmt = $con->prepare($query);
  $stmt->execute(array($email, $_SESSION['admins']['id']));
  $results = $stmt->fetchall();
  if($results) {
      $errors[] = 'This Email is <strong> exist </strong> try another one';
  }   
  
  if($errors == null) {
      if($password != null ){
          $stmt = $con->prepare("UPDATE admins SET
                  name = ?,
                  email = ?,
                  phone = ?,
                  password = ?                    
              WHERE id = ?  ");
          $password =password_hash($password, PASSWORD_DEFAULT);
          $stmt->execute(array($name, $email, $phone, $password, $_SESSION['admins']['id']));
          if($stmt){
              $_SESSION['success'] = $name .' :Your Information Updated Successfully';
              header('Location: profile-admin.php');
              exit();
          } 
      }else {
          $stmt = $con->prepare("UPDATE admins SET
                  name = ?,
                  email = ?,
                  phone = ?                    
              WHERE id = ?  ");

          $stmt->execute(array($name, $email, $phone, $_SESSION['admins']['id']));
          if($stmt){
              $_SESSION['success'] = $name .' :Your Information Updated Successfully';
              header('Location: profile-admin.php');
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
    <title>Profile Admin</title>
    
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
         <a href="profile-admin.php"><i class="fa fa-user"  style="color: #6a5496;border-radius: 50%;border: 2px solid;padding: 10px;font-size: 35px;"></i> Admin</a>
       </div>
    </div>
    <nav>
      <a href="profile-admin.php">Profile</a>
      <a href="services.php">Services</a>
      <a href="about-us.php">About us</a>
      <a href="logout.php"><i class="fa fa-user" ></i>Log out</a>      
    </nav>
    <!-- start welcome -->
    <div class="welcome ">
      <span>Hello, <?php echo $_SESSION['admins']['name'] ?></span>
      <i class="fa fa-user"></i>
    </div>

    <div class="mx-5">
      <h2 class="h2-profile my-4">Admin Profile</h2>
      <div class="profile-info">
        <form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="POST">
          <div class="input-container">
            <label for="name">Admin Name</label>
            <input type="text" name="name" id="name" value="<?php echo $admin['name'] ?>" >
          </div>
          <div class="input-container">
            <label for="email">Email</label>
            <input type="email" name="email" id="email" value="<?php echo $admin['email'] ?>" >
          </div>
          <div class="input-container">
            <label for="password">Password</label>
            <input type="password" name="password" id="password">
          </div>
          <div class="input-container">
            <label for="phone">Phone number </label>
            <input type="number" name="phone" id="phone" value="<?php echo $admin['phone'] ?>" >
          </div>
            
          <input type="submit" value="Save" id="save">
        </form>
      </div>    
            <?php if(isset($errors) && $errors != null) { ?> 
              <?php foreach($errors as $error) {?>
                  <div class="mx-5 my-2 p-1"><span class="text-danger" ><?php echo $error; ?></span></div>
              <?php } ?>
            <?php } ?>
      
    </div>

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
if(isset($_SESSION['success'])) {
  unset($_SESSION['success']);
}
?>