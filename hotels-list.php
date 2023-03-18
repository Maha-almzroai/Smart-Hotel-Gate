<?php 
session_start();
include "connect.php";
if( !isset($_SESSION['admins'])) {
    header('Location: login.php');
    exit();
}
// Delete hotel
$id = isset($_GET['hotel_id']) && $_GET['hotel_id'] != null?$_GET['hotel_id']:'';
if($id != null) {
  $query = 'SELECT * FROM hotels WHERE id =?';
  $stmt = $con->prepare($query);
  $stmt->execute(array($id));
  $hotel = $stmt->fetch();
  if($hotel != null) {
    $stmt = $con->prepare("DELETE FROM hotels WHERE id = ?");
    $stmt->execute(array($id));
    if($stmt) {
        unlink('dist/images/hotels/'.$hotel['photo']);
    }
  }
}

$query = "SELECT * FROM hotels ORDER BY id DESC";
$stmt = $con->prepare($query);
$stmt->execute();
$hotels = $stmt->fetchall();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="auther" content="">
    <meta name="description" content="">
    <title>List Hotels</title>
    
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
    
    <h2 class="h2-profile my-4">List Hotels</h2>
    <div class="rooms-list">
      <div class="row">
          <?php 
          $counter = 1;
          foreach($hotels as $hotel) { ?>
            <div class="col-md-3">
                <div class="rome-nav">
                    <!-- Hotel <?php echo $counter ?> -->
                    <?php echo $hotel['name'] ?>
                </div>
                <div class="profile-info">
                    <div  class="input-container">            
                        <label for="number_of_hotel">Number Of Hotel</label>
                        <span id="number_of_hotel"><?php echo $hotel['phone'] ?></span>
                    </div>
                    <div class="input-container">
                        <label for="email_of_hotel">Email Of Hotel</label>
                        <span  id="email_of_hotel"><?php echo $hotel['email'] ?></span>
                    </div>

                    <?php
                        $query = "SELECT name FROM managers WHERE hotel_id =? ORDER BY id DESC";
                        $stmt = $con->prepare($query);
                        $stmt->execute(array($hotel['id']));
                        $managers = $stmt->fetchall();
                    ?>

                    <div  class="input-container">       
                        <label for="hotel_manager_name">Hotel Manager Name</label>
                        <span id="hotel_manager_name">
                            <?php 
                                $managers_name = "";
                                if($managers != null) {
                                    foreach($managers as $manager){
                                        $managers_name .= $manager["name"] . " , ";
                                    }
                                    $managers_name = rtrim($managers_name,", ");
                                }else {
                                    $managers_name = "Not Selected!";
                                }
                                echo $managers_name;
                            ?>
                        </span>
                    </div>
                    <div class="rome-action">
                    <a href="hotels-list.php?hotel_id=<?php echo $hotel["id"] ?>"  onclick="delete_hotel()"><i class="fa fa-trash"></i></a>
                    <a href="manage-hotels.php?hotel_id=<?php echo $hotel["id"] ?>"><i class="fa fa-edit"></i></a>
                    </div>
                </div>            
            </div>
          <?php
          $counter += 1;
             } ?>        
      </div>
    </div>

    <script src="dist/js/jquery-3.4.1.slim.min.js"></script>
    <script src="dist/js/bootstrap.bundle.min.js"></script>
    <script src="dist/js/main.js"></script>
    <script>
        function delete_hotel() {
            alert('Are you sure you want delete the hotel permanently ?');
        }
    </script>
</body>
</html>