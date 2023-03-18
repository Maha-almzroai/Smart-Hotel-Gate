<?php 
session_start();
if(!isset($_SESSION['customers'])) {
    header('Location: index.php');
    exit();
}

include "connect.php";

require "libraries/PHPMailer/src/Exception.php";
require "libraries/PHPMailer/src/PHPMailer.php";
require "libraries/PHPMailer/src/SMTP.php";
require "libraries/barcode.php";

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;


$reservation_id     = isset($_GET['reservation_id']) && $_GET['reservation_id'] != null?$_GET['reservation_id']:null;

$stmt = $con->prepare('SELECT * FROM reservations WHERE id = ?');
$stmt->execute(array($reservation_id));
$reservation = $stmt->fetch();

$stmt = $con->prepare('SELECT * FROM customers WHERE id = ?');
$stmt->execute(array($_SESSION['customers']['id']));
$customer = $stmt->fetch();

// $auth_code = bin2hex(openssl_random_pseudo_bytes(4));
// barcode
if($reservation['auth_code'] == null ) {
    $text = $reservation['id'] . '-'. $customer['firstname'] . '-'.  $customer['lastname'];
    $filename = $text.'.png';
    $filepath = 'dist\\images\\barcodes\\'.$filename;
    //generate barcode
    barcode( $filepath, $text, $size, $orientation, $code_type, $print, $sizefactor );
}else {
    $filepath = 'dist\\images\\barcodes\\'.$reservation['auth_code'];
}


// message content
$Replay  = '<div style="text-align:center;"><h3>Thanks for your reservation</h3>';
$Replay .= '<h2>Hi ' . $customer['firstname'] . ' '.  $customer['lastname'] .'</h2>';
$Replay .= '<h2>Your room number is 209 </h2>';

// ------ convert image to send via email ------//

// Read image path, convert to base64 encoding
$imgData = base64_encode(file_get_contents($filepath));
// Format the image SRC:  data:{mime};base64,{data};
$src = 'data: '.mime_content_type($filepath).';base64,'.$imgData;

// ------ convert image to send via email ------//

$Replay .= '<img style="dispaly:block; margin:20px auto;" src="'.$src.'">';
echo $Replay;
$altReplay  = $Replay;

$Name       = $customer['firstname'] . ' ' . $customer['lastname'];
$Email      = $customer['email'];
$Subject    = 'Verify reservation';

$mail = new PHPMailer(true);                    //passing 'true' enables exceptions

try {



    //server setting

    $mail->SMTPDebug = 0;                       //enable verbose debug output
    $mail->isSMTP();                            //set mailer to use smtp
    $mail->Host = 'smtp.mailtrap.io';           //specify main and backup smtp servers
    $mail->SMTPAuth = true ;                    //enable smtp authentication
    $mail->Username = 'edb871052a4dbf';         //smtp username
    $mail->Password = '9a9e7e70747f31';         //smtp password
    // $mail->SMTPSecure = 'tls';               //enable tls encryption, 'ssl' also accepted
    $mail->Port = 2525;                         //tcp port to connect to

    //recipients

    $mail->setFrom('admin@hotel-gate.com', 'Hotel Gate');
    $mail->addAddress($Email, $Name);

    //content

    $mail->isHTML(true);
    $mail->Subject = $Subject;
    $mail->Body    = $Replay;
    $mail->AltBody = $altReplay;
    
    if($mail->send()){
        $stmt = $con->prepare('UPDATE reservations SET auth_code = ? WHERE id =?');
        $stmt->execute(array($filename, $reservation['id']));

        $_SESSION['success'] = 'Reservation code sent to your email Successfully';
        header('Location: successful-reservation.php');
        exit();
    }




} catch (Exception $e) {
    $_SESSION['success'] = 'Something goes wrong please try later';
    header('Location: successful-reservation.php');
    exit();
}
