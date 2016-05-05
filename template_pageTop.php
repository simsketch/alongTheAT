<?php
// It is important for any file that includes this file, to have
// check_login_status.php included at its very top.
$envelope = '';
$loginLink = '<a href="login.php">Log In</a> &nbsp; | &nbsp; <a href="signup.php">Sign Up</a>';
if($user_ok == true) {
	$sql = "SELECT notescheck FROM users WHERE username='$log_username' LIMIT 1";
	$query = mysqli_query($db_conx, $sql);
	$row = mysqli_fetch_row($query);
	$notescheck = $row[0];
	$sql = "SELECT id FROM notifications WHERE username='$log_username' AND date_time > '$notescheck' LIMIT 1";
	$query = mysqli_query($db_conx, $sql);
	$numrows = mysqli_num_rows($query);
    if ($numrows == 0) {
		$envelope = '<a href="notifications.php"><i style="color: #7f7f7f; position: relative; top: -1px;" class="fa fa-envelope" aria-hidden="true"></i></a>';
    } else {
		$envelope = '<a href="notifications.php"><i style="color: #c0e73d; position: relative; top: -1px;" class="fa fa-envelope" aria-hidden="true"></i></a>';
	}
    $loginLink = '| &nbsp; <a href="user.php?u='.$log_username.'">'.$log_username.'</a> &nbsp; | &nbsp; <a href="logout.php">Log Out</a>';
}

// $envelope = "end";
?>

<header id="pageTop">
   <div id="pageTopWrap">
      <div id="pageTopLogo">

         <a href="index.php"><img id="logo" src="images/logo.png" alt="Lego Logo" title="LegoLock"></a>
      </div>
      <div id="pageTopRest">
         <div id="menu1">
            <div id="notesDiv">
               <?php echo $envelope; ?> &nbsp; &nbsp; <?php echo $loginLink; ?>
            </div>
         </div>
         <div id="menu2">
            <div>
               <a href="index.php"><img id="homeIcon" src="images/home1.png" height="20" alt="home" title="home"></a>
               <!--<a href="#">Menu_Item_1</a>
               <a href="#">Menu_Item_2</a>-->
            </div>
         </div>
      </div>
   </div>
</header>
