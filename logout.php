<?php
session_start();

// SET SESSION DATA TO AN EMPTY ARRAY
$_SESSION = array();
// EXPIRE THEIR COOKIE FILES
if (isset($_COOKIE['id']) && isset($_COOKIE['user']) && isset($_COOKIE['pass'])) {
   setcookie('id');
   setcookie('user');
   setcookie('pass');
}
// DESTROY THE SESSION VARIABLES
session_destroy();
// DOUBLE CHECK TO SEE IF THEIR SESSION EXISTS
if (isset($_SESSION['username'])) {
   header("location: message.php?msg=Error:_Logout_Failed");
} else {
   header("location: http://www.tylerlego.com");
   exit();
}

?>
