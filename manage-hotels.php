<?php 
session_start();
include "connect.php";
if( !isset($_SESSION['admins'])) {
    header('Location: login.php');
    exit();
}
$id = isset($_GET['hotel_id']) && $_GET['hotel_id'] != null?$_GET['hotel_id']:'';
if($id != null) {
  $query = 'SELECT * FROM hotels WHERE id =?';
  $stmt = $con->prepare($query);
  $stmt->execute(array($id));
  $hotel = $stmt->fetch();
}

if($_SERVER['REQUEST_METHOD'] == 'POST') {

    // $errors     = [];
    $name       =  $_POST['name'] != null ? $_POST['name'] : null;
    $stars      = isset($_POST['stars']) && $_POST['stars'] != null ? $_POST['stars'] : null;
    $location   =  $_POST['location'] != null ? $_POST['location'] : null;
    $email      =  $_POST['email'] != null ? $_POST['email'] : null;
    $phone      =  $_POST['phone'] != null ? $_POST['phone'] : null;
    $discount      =  $_POST['discount'] != null ? $_POST['discount'] : null;
    $discount_code      =  $_POST['discount_code'] != null ? $_POST['discount_code'] : null;
    $rooms_number      =  $_POST['rooms_number'] != null ? $_POST['rooms_number'] : null;
    $photo            =  $_FILES['photo'] != null ? $_FILES['photo'] : null;

    if(!isset( $_POST['id'])) {
      if($photo['name'] == null) {
          $errors[] = 'Photo is <strong> REQUIRED </strong>';
      } 
    }
    // var_dump($stars);
    if($name == null) {
        $errors[] = 'Hotel Name is <strong> REQUIRED </strong>';
    }
    if($stars == null) {
        $errors[] = 'Hotel rank is <strong> REQUIRED </strong>';
    }
    if($location == null) {
        $errors[] = 'Hotel location is <strong> REQUIRED </strong>';
    }
    if($email == null) {
        $errors[] = 'Email is <strong> REQUIRED </strong>';
    }
    if($phone == null) {
        $errors[] = 'Phone is <strong> REQUIRED </strong>';
    }
    if($discount == null && $discount_code != null) {
        $errors[] = 'The value of discount is <strong> REQUIRED </strong> if you enter the discount code  ';
    }
    if($discount != null && $discount_code == null) {
        $errors[] = 'The discount code is <strong> REQUIRED </strong> if you enter the discount value  ';
    }
    if($rooms_number == null) {
        $errors[] = 'Rooms number is <strong> REQUIRED </strong>';
    }
    if(isset( $_POST['id']) &&  $_POST['id'] != null) {
      $query = "SELECT * FROM hotels WHERE email = ? AND id !=?";
      $stmt = $con->prepare($query);
      $stmt->execute(array($email, $_POST['id']));
      $results = $stmt->fetchall();
      if($results) {
          $errors[] = 'This Email is <strong> exist </strong> try another one';
      }  
    }else {
      $query = "SELECT * FROM hotels WHERE email = ?";
      $stmt = $con->prepare($query);
      $stmt->execute(array($email));
      $results = $stmt->fetchall();
      if($results) {
          $errors[] = 'This Email is <strong> exist </strong> try another one';
      }  
    }
 
    
    if(empty($errors)) {

      if(isset( $_POST['id']) &&  $_POST['id'] != null) {
       
        if($photo['name'] == null) {
            $query = 'SELECT * FROM hotels WHERE id =?';
            $stmt = $con->prepare($query);
            $stmt->execute(array($_POST['id']));
            $hotel = $stmt->fetch();
            $fileName = $hotel['photo'];
        }else{
            $ext = pathinfo($photo['name'], PATHINFO_EXTENSION);
            $fileName = time() . '-hotel.'.$ext;       
        }

        $query_update = $con->prepare("UPDATE 
                                        hotels SET
                                          name = ?,
                                          stars = ?,
                                          location = ?,
                                          email = ?,
                                          phone = ?,
                                          discount = ?,
                                          discount_code = ?,
                                          rooms_number = ?,
                                          photo = ?
                                        WHERE id = ?  ");
        
        $data = [
            $name,
            $stars,
            $location,
            $email,
            $phone,
            $discount,
            $discount_code,
            $rooms_number,
            $fileName,
            $_POST['id']
        ];
        $query_update->execute($data);
        if($stmt){      
              if($photo['name'] != null) {
                move_uploaded_file($photo['tmp_name'], 'dist/images/hotels/'.$fileName);
                unlink('dist/images/hotels/'.$hotel['photo']);
            }   

            $_SESSION['success'] = 'You have updated hotel information Successfully';
            header('Location: manage-hotels.php?hotel_id='.$_POST['id']);
            exit();
        } 
      }else {
        $ext = pathinfo($photo['name'], PATHINFO_EXTENSION);
        $fileName = time() . '-hotel.'.$ext;

        $query_insert = "INSERT INTO hotels (name, stars, location, email, phone, discount, discount_code, rooms_number, photo) VALUES(:name, :stars, :location, :email, :phone, :discount, :discount_code, :rooms_number, :photo)";
        $stmt = $con->prepare($query_insert);
        $data = [
            ':name'       => $name,
            ':stars'      => $stars,
            ':location'   => $location,
            ':email'      => $email,
            ':phone'      => $phone,
            ':discount'   => $discount,
            ':discount_code'  => $discount_code,
            ':rooms_number'   => $rooms_number,
            ':photo'   => $fileName,
        ];
        $stmt->execute($data);
        if($stmt){
            move_uploaded_file($photo['tmp_name'], 'dist/images/hotels/'.$fileName);
            $_SESSION['success'] = 'You have creatred a new hotel Successfully';
            header('Location: manage-hotels.php');
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
    <title>Manage Hotels</title>
    
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
    <h2 class="h2-profile my-4">Manage Hotels</h2>

    <div class="container-fluid">
      <div class="row">
        <div class="col-md-12">
          <?php if(isset($hotel) && $hotel != null) { ?>
              <img src="dist/images/hotels/<?php echo $hotel['photo'] ?>" alt="" class="fit fit-300 rounded" style="width: 50%;margin-bottom: 15px;display: block;">
          <?php } ?>
          <div class="profile-info">
            <form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="POST" enctype="multipart/form-data">
              <?php
                if(isset($hotel) && $hotel != null) {
                  ?>
                  <input type="hidden" name="id" value="<?php echo $hotel['id']; ?>" >
                  <?php
                }
              ?>
              <div class="input-container">
                <label for="name">Hotel Name </label>
                <input type="text" name="name" id="name" value="<?php echo isset($hotel) && $hotel != null?$hotel['name']:'' ?>" >
              </div>
              <div class="input-container">
                <label >Hotel Class </label>
                <div class="checkbox-inputs">
                    <div class="mr-0">
                        <label for="1-star">One</label>
                        <input type="radio" name="stars" id="1-star" value="1" <?php echo isset($hotel) && $hotel != null && $hotel['stars'] == '1'?'checked':'' ?> >
                    </div>
                    <div>
                        <label for="2-star">Two</label>
                        <input type="radio" name="stars" id="2-star" value="2" <?php echo isset($hotel) && $hotel != null && $hotel['stars'] == '2'?'checked':'' ?> >
                    </div>
                    <div>
                        <label for="3-star">Three</label>
                        <input type="radio" name="stars" id="3-star" value="3" <?php echo isset($hotel) && $hotel != null && $hotel['stars'] == '3'?'checked':'' ?> >
                    </div>
                    <div>
                        <label for="4-star">Four</label>
                        <input type="radio" name="stars" id="4-star" value="4" <?php echo isset($hotel) && $hotel != null && $hotel['stars'] == '4'?'checked':'' ?> >
                    </div>
                    <div>
                        <label for="5-star">Five</label>
                        <input type="radio" name="stars" id="5-star" value="5" <?php echo isset($hotel) && $hotel != null && $hotel['stars'] == '5'?'checked':'' ?> >
                    </div>
                </div>       
              </div>
              <div class="input-container">
                <label for="location">Hotel Location</label>
                <input type="text" name="location" id="location" value="<?php echo isset($hotel) && $hotel != null?$hotel['location']:'' ?>" >
              </div>
              <div class="input-container">
                <label for="email">Email</label>
                <input type="email" name="email" id="email" value="<?php echo isset($hotel) && $hotel != null?$hotel['email']:'' ?>" >
              </div>
              <div class="input-container">
                <label for="phone">Phone number for hotel</label>
                <input type="number" name="phone" id="phone" value="<?php echo isset($hotel) && $hotel != null?$hotel['phone']:'' ?>" >
              </div>
              <div class="input-container">
                <label for="discount">Amount Of Discount</label>
                <input type="number" name="discount" id="discount" value="<?php echo isset($hotel) && $hotel != null?$hotel['discount']:'' ?>" >
              </div>
              <div class="input-container">
                <label for="discount_code">Discount Code</label>
                <input type="number" name="discount_code" id="discount_code" value="<?php echo isset($hotel) && $hotel != null?$hotel['discount_code']:'' ?>" >
              </div>
              <div class="input-container">
                <label for="rooms_number">Rooms Number</label>
                <input type="number" name="rooms_number" id="rooms_number" value="<?php echo isset($hotel) && $hotel != null?$hotel['rooms_number']:'' ?>" >
              </div>
              <div class="input-container">
                <label for="photo">Photo</label>
                <input type="file" name="photo" id="photo" >
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
      </div>
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
    unset($_SESSION['success']);
?>