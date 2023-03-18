<?php 
session_start();
include "connect.php";
if( !isset($_SESSION['managers'])) {
    header('Location: login.php');
    exit();
}
$id = isset($_GET['offer_id']) && $_GET['offer_id'] != null?$_GET['offer_id']:'';
if($id != null) {
  $query = 'SELECT * FROM offers WHERE id =?';
  $stmt = $con->prepare($query);
  $stmt->execute(array($id));
  $offer = $stmt->fetch();
}

if($_SERVER['REQUEST_METHOD'] == 'POST') {

    // $errors     = [];
    // var_dump(empty($_FILES['photo']['name'][0]));
    $type_of_rooms    =  $_POST['type_of_rooms'] != null ? $_POST['type_of_rooms'] : null;
    $number_of_rooms  =  $_POST['number_of_rooms'] != null ? $_POST['number_of_rooms'] : null;
    $price_per_room   =  $_POST['price_per_room'] != null ? $_POST['price_per_room'] : null;
    $features         =  isset($_POST['features']) && $_POST['features'] != null ? $_POST['features'] : null;
    $photos            =  $_FILES['photo'] != null ? $_FILES['photo'] : null;

    if($type_of_rooms == null) {
        $errors[] = 'Type of rooms  is <strong> REQUIRED </strong>';
    }
    if($number_of_rooms == null) {
        $errors[] = 'Number of rooms is <strong> REQUIRED </strong>';
    }
    if($price_per_room == null) {
        $errors[] = 'Price per room  is <strong> REQUIRED </strong>';
    }elseif($price_per_room < 25) {
        $errors[] = 'Price must be bigger than <strong> 25 </strong>';
    }elseif($price_per_room > 2500) {
        $errors[] = 'Price must be smaller than <strong> 2500 </strong>';
    } 
    if($features == null) {
        $errors[] = 'Features are <strong> REQUIRED </strong>';
    }
    if(!isset( $_POST['id'])) {
      if(empty($_FILES['photo']['name'][0]) ) {
          $errors[] = 'Photo is <strong> REQUIRED </strong>';
      } 
    }
    // get hotel id
    $stmt = $con->prepare('SELECT * FROM managers WHERE id = ?'); 
    $stmt->execute(array($_SESSION['managers']['id']));
    $manager = $stmt->fetch();
    $hotel_id = $manager['hotel_id'];
    // prepare file name
    if(!empty($_FILES['photo']['name'][0])) {
      foreach($_FILES['photo']['name'] as $photo) {
        $ext = pathinfo($photo, PATHINFO_EXTENSION);
        $fileName[] = time(). '-' . rand(0,1000) . '-' . $hotel_id . '.'.$ext;
      }
      $fileName = implode('|', $fileName); 
    }
    // var_dump(pathinfo($_FILES['photo']['name'][0], PATHINFO_EXTENSION));
    // var_dump(implode('|', $fileName));

    if(empty($errors)) {
          // prepare featuers ti be saved in database
          $features =  implode(';', $features);
      if(isset( $_POST['id']) &&  $_POST['id'] != null) {
        if($_FILES['photo']['name'][0] != null) {
            // delete the old photo in server
            $query = 'SELECT * FROM offers WHERE id =?';
            $stmt = $con->prepare($query);
            $stmt->execute(array($_POST['id']));
            $offer = $stmt->fetch();

            $query_update = $con->prepare("UPDATE offers SET
                                            type_of_rooms = ?,
                                            number_of_rooms = ?,
                                            price_per_room = ?,
                                            features = ?,
                                            photo = ?
                                          WHERE id = ?  ");
            $data = [$type_of_rooms, $number_of_rooms, $price_per_room, $features, $fileName, $_POST['id']];
            $query_update->execute($data);
            if($stmt){       
              // save the new photo in server 
              $photos  = explode('|', $fileName);
              for($i=0; $i< count($_FILES['photo']['name']); $i++) {
                move_uploaded_file($_FILES['photo']['tmp_name'][$i], 'dist/images/rooms/'.$photos[$i]);
              }
              if($offer != null) {
                foreach(explode("|", $offer['photo']) as $photo) {                    
                  unlink('dist/images/rooms/'.$photo);
                }
              }

              $_SESSION['success'] = 'You have updated offer information Successfully';
              header('Location: add-offer.php?offer_id='.$_POST['id']);
              exit();
            }
        }else {
              $query_update = $con->prepare("UPDATE offers SET
                                                type_of_rooms = ?,
                                                number_of_rooms = ?,
                                                price_per_room = ?,
                                                features = ?
                                              WHERE id = ?");

              $data = [$type_of_rooms, $number_of_rooms, $price_per_room, $features, $_POST['id']];
              $query_update->execute($data);
              if($stmt){
                $_SESSION['success'] = 'You have updated offer information Successfully';
                header('Location: add-offer.php?offer_id='.$_POST['id']);
                exit();
              }
        }       
 
      }else {        
        $query_insert = "INSERT INTO offers (type_of_rooms, number_of_rooms, price_per_room, features, photo, hotel_id, popular, rated_guest) VALUES(:type_of_rooms, :number_of_rooms, :price_per_room, :features, :photo, :hotel_id, :popular, :rated_guest)";
        $stmt = $con->prepare($query_insert);
        $data = [
            ':type_of_rooms'       => $type_of_rooms,
            ':number_of_rooms'   => $number_of_rooms,
            ':price_per_room'      => $price_per_room,
            ':features'      => $features,
            ':photo'   => $fileName,
            ':hotel_id'  => $hotel_id,
            ':popular' => rand(1,10),
            ':rated_guest' => rand(1,10)
        ];
        $stmt->execute($data);
        if($stmt){ 
          $counter = 0;
          $photos  = explode('|', $fileName);
          for($i=0; $i< count($_FILES['photo']['name']); $i++) {
            move_uploaded_file($_FILES['photo']['tmp_name'][$i], 'dist/images/rooms/'.$photos[$i]);
          }
            // move_uploaded_file($photo['tmp_name'], 'dist/images/rooms/'.$fileName);
            $_SESSION['success'] = 'You have creatred a new offer Successfully';
            header('Location: add-offer.php');
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
    <title>Add Offer</title>
    
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
      <a href="add-offer.php">Add room</a>
      <a href="offers-list.php">List rooms</a>
      <a href="logout.php"><i class="fa fa-user" ></i>Log out</a>   
    </nav>
    <!-- start welcome -->
    <div class="welcome">
      <span>Hello, <?php echo $_SESSION['managers']['name'] ?></span>
      <i class="fa fa-user"></i>
    </div>


    <h2 class="h2-profile my-4">Add Room</h2>
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-12">
          <?php if(isset($offer) && $offer != null) { ?>
            <div id="carouselExampleControls" class="carousel slide" data-ride="carousel" style="width: 50%">
                <div class="carousel-inner">
                  <?php $first = true; ?>
                  <?php foreach(explode("|", $offer['photo']) as $photo) { ?>
                    <div class="carousel-item <?php echo $first == true?"active":""; ?>">
                      <img class="d-block fit fit-300 rounded" style="margin-bottom: 15px;" src="dist/images/rooms/<?php echo $photo ?>" alt="First slide">
                    </div>
                    <?php $first = false ?>
                  <?php } ?>
                </div>
                <a class="carousel-control-prev" href="#carouselExampleControls" role="button" data-slide="prev">
                  <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                  <span class="sr-only">Previous</span>
                </a>
                <a class="carousel-control-next" href="#carouselExampleControls" role="button" data-slide="next">
                  <span class="carousel-control-next-icon" aria-hidden="true"></span>
                  <span class="sr-only">Next</span>
                </a>
            </div>
                <!-- <img src="dist/images/rooms/<?php echo $offer['photo'] ?>" alt="" class="fit fit-300 rounded" style="width: 50%;margin-bottom: 15px;display: block;"> -->
         
              <?php } ?>
            <div class="profile-info">
              <form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="POST" enctype="multipart/form-data">
                <?php
                  if(isset($offer) && $offer != null) {
                    ?>
                    <input type="hidden" name="id" value="<?php echo $offer['id']; ?>" >
                    <?php
                  }
                ?>
                <div class="input-container">
                  <label for="type_of_rooms">Type Of Rooms </label>
                    <select name="type_of_rooms" id="type_of_rooms">
                        <?php if ($offer['type_of_rooms'] == null ){ ?>
                  <?php } ?> 
                  <option value="Single"  >Single</option>
                  <option value="Double"  >Double</option>
                  <option value="King"    >King</option>
                  <option value="Twin"   >Twin</option>
                  <option value="Queen"  >Queen</option>
                </select>
                </div>
                <div class="input-container">
                  <label for="number_of_rooms">Number Of Rooms</label>
                  <input type="number" name="number_of_rooms" id="number_of_rooms" value="<?php echo isset($offer) && $offer != null?$offer['number_of_rooms']:'' ?>" >
                </div>
                <div class="input-container">
                  <label for="price_per_room">Price Per Room</label>
                  <input type="number" name="price_per_room" id="price_per_room" value="<?php echo isset($offer) && $offer != null?$offer['price_per_room']:'' ?>" >
                </div>
                <div class="input-container">
                  <label for="features">Features</label>
                  <?php if(isset($offer) && $offer != null) { 
                    $features = explode(";", $offer["features"]);
                    ?>
                    <div class="checkbox-inputs">
                        <div>
                            <label for="city-view">city View</label>
                            <input type="checkbox" name="features[]" id="city-view" value="city view" <?php echo in_array('city view', $features)?'checked':''; ?> >
                        </div>
                        <div>  
                            <label for="shower">Shower</label>
                            <input type="checkbox" name="features[]" id="shower" value="shower" <?php echo in_array('shower', $features)?'checked':''; ?> >
                        </div>
                        <div>  
                            <label for="coffee_&_tea">Coffee & Tea</label>
                            <input type="checkbox" name="features[]" id="coffee_&_tea" value="coffee & tea" <?php echo in_array('coffee & tea', $features)?'checked':''; ?> >
                        </div>
                        <div>
                            <label for="free_wifi">Free WiFi</label>
                            <input type="checkbox" name="features[]" id="free_wifi" value="free wifi" <?php echo in_array('free wifi', $features)?'checked':''; ?> >
                        </div>
                        <div>                
                            <label for="1_queen_bed">1 Queen Bed</label>
                            <input type="checkbox" name="features[]" id="1_queen_bed" value="1 queen bed" <?php echo in_array('1 queen bed', $features)?'checked':''; ?> >
                        </div>
                        <div>  
                            <label for="1_king_bed">1 King Bed</label>
                            <input type="checkbox" name="features[]" id="1_king_bed" value="1 king bed" <?php echo in_array('1 king bed', $features)?'checked':''; ?> >
                        </div>
                        <div>
                            <label for="free_breakfast">Free Breakfast</label>
                            <input type="checkbox" name="features[]" id="free_breakfast" value="free breakfast" <?php echo in_array('free breakfast', $features)?'checked':''; ?> >
                        </div>
                        <div>   
                            <label for="free_parking">Free Parking</label>
                            <input type="checkbox" name="features[]" id="free_parking" value="free parking" <?php echo in_array('free parking', $features)?'checked':''; ?> >   
                        </div>
                    </div> 
                  <?php }else{ ?>
                    <div class="checkbox-inputs">
                        <div>
                            <label for="city-view">city View</label>
                            <input type="checkbox" name="features[]" id="city-view" value="city view">
                        </div>
                        <div>  
                            <label for="shower">Shower</label>
                            <input type="checkbox" name="features[]" id="shower" value="shower">
                        </div>
                        <div>  
                            <label for="coffee_&_tea">Coffee & Tea</label>
                            <input type="checkbox" name="features[]" id="coffee_&_tea" value="coffee & tea">
                        </div>
                        <div>
                            <label for="free_wifi">Free WiFi</label>
                            <input type="checkbox" name="features[]" id="free_wifi" value="free wifi">
                        </div>
                        <div>                
                            <label for="1_queen_bed">1 Queen Bed</label>
                            <input type="checkbox" name="features[]" id="1_queen_bed" value="1 queen bed">
                        </div>
                        <div>  
                            <label for="1_king_bed">1 King Bed</label>
                            <input type="checkbox" name="features[]" id="1_king_bed" value="1 king bed">
                        </div>
                        <div>
                            <label for="free_breakfast">Free Breakfast</label>
                            <input type="checkbox" name="features[]" id="free_breakfast" value="free breakfast">
                        </div>
                        <div>   
                            <label for="free_parking">Free Parking</label>
                            <input type="checkbox" name="features[]" id="free_parking" value="free parking">   
                        </div>
                    </div> 
                  <?php } ?>  
                </div>
                
                <div class="input-container">
                  <label for="photo">Photo</label>
                  <input type="file" name="photo[]" id="photo" multiple max="3">
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
    <script>
          $("input[type='submit']").click(function(e){
            e.preventDefault();
            var $fileUpload = $("input[type='file']");
            if (parseInt($fileUpload.get(0).files.length)>3){              
              alert("You can only upload a maximum of 3 files");
            }else {
              $("form").submit();
              console.log(5);
            }
          }); 
          $("input[type='file']").on("change", function() {
            if ($("input[type='file']")[0].files.length > 3) {
                alert("You can select only 3 images");
            }
          });  
    </script>

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