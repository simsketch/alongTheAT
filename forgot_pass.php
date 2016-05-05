<?php
   include_once('php_includes/check_login_status.php');

   if ($user_ok == true) {
      header("location: user.php?u=" . $_SESSION['username']);
      exit();
   }
?>
<?php
   if(isset($_POST['e'])) {
      $e = mysqli_real_escape_string($db_conx, $_POST['e']);
      $sql = "SELECT id, username FROM users WHERE email='$e' AND activated='1' LIMIT 1";
      $query = mysqli_query($db_conx, $sql);
      $numrows = mysqli_num_rows($query);
      if ($numrows > 0) {
         while($row = mysqli_fetch_array($query, MYSQLI_ASSOC)) {
            $id = $row['id'];
            $u = $row['username'];
         }
         $email_rand = substr(str_shuffle($e), 0, 4);
         $rand_num = rand(10000,99999);
         $temp_pass = "$email_rand$rand_num";
         $hash_temp_pass = md5($temp_pass);

         $sql = "UPDATE useroptions SET temp_pass='$hash_temp_pass' WHERE username='$u' LIMIT 1";
         $query = mysqli_query($db_conx, $sql);

         $to = "$e";
         $from = "contact@tylerlego.com";
         $headers = "From: $from\n";
         $headers .= "MIME-Version: 1.0\n";
         $headers .= "Content-type: text/html; charset=iso-8859-1 \n";
         $subject = "tylerlego.com Temporary Password";
         $msg = '<h2>Hello '.$u.'</h2><p>This is an automated message from tylerlego.com. If you did not recently request a temporary password, please disregard this email or reply to contact@tylerlego.com with any questions.</p><p>Once logging in with your temporary password, you will be able to change your password to anything you like, as many times as you like</p><p>After you click the link below your password to login will be:<br /><b>'.$temp_pass.'</b></p><p><a href="http://www.tylerlego.com/forgot_pass.php?u='.$u.'&p='.$hash_temp_pass.'">Click here now to apply the temporary password shown above to your account</a></p><p>If you do not click the link in this email, no changes will be made to your account. In order to set your login password to the temporary password you must click the link above.</p>';
         if (mail($to, $subject, $msg, $headers)) {
            echo "success";
            exit();
         } else {
            echo "email_send_failed";
            exit();
         }
      } else {
         echo "no_exist";
      }
      exit();
   }
?>

<?php
// EMAIL LINK CLICK CALLS THIS CODE TO EXECUTE
if(isset($_GET['u']) && isset($_GET['p'])){
	$u = preg_replace('#[^a-z0-9]#i', '', $_GET['u']);
	$temppasshash = preg_replace('#[^a-z0-9]#i', '', $_GET['p']);
	if(strlen($temppasshash) < 10){
		exit();
	}
	$sql = "SELECT id FROM useroptions WHERE username='$u' AND temp_pass='$temppasshash' LIMIT 1";
	$query = mysqli_query($db_conx, $sql);
	$numrows = mysqli_num_rows($query);
	if ($numrows == 0) {
		header("location: message.php?msg=There is no match for that username with that temporary password in the system. We cannot proceed.");
    	exit();
	} else {
		$row = mysqli_fetch_row($query);
		$id = $row[0];
		$sql = "UPDATE users SET password='$temppasshash' WHERE id='$id' AND username='$u' LIMIT 1";
	   $query = mysqli_query($db_conx, $sql);
		$sql = "UPDATE useroptions SET temp_pass='' WHERE username='$u' LIMIT 1";
	   $query = mysqli_query($db_conx, $sql);
	   header("location: login.php");
      exit();
    }
}
?>

<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8">
		<title>Login</title>
		<link rel="icon" href="favicon.ico" type="image/x-icon">
		<link rel="stylesheet" href="style/normalize.css">
		<link rel="stylesheet" href="style/style.css">
		<style type="text/css">
			#forgotpassform{
				margin-top:24px;
			}
			#forgotpassform > div {
				margin-top: 12px;
			}
			#forgotpassform > input,select {
				width: 200px;
				padding: 3px;
				background: #F3F9DD;
			}
			#forgotpassbtn {
				font-size:18px;
				padding: 12px;
			}
		</style>
		<script src="js/main.js"></script>
		<script src="js/ajax.js"></script>
      <script type="text/javascript">
      function emptyElement(x) {
         _(x).innerHTML = "";
      }
      function reset() {
         emptyElement('status');
         _('forgotpassbtn').style.display = "block";
      }

      function forgotpass() {
         var e = _('email').value;
         if (e == "") {
            _('status').innerHTML = "Please enter your email address";
         }  else {
            _('forgotpassbtn').style.display = "none";
            _('status').innerHTML = "Please wait...";

            var ajax = ajaxObj('POST', 'forgot_pass.php');
            ajax.onreadystatechange = function() {
               if (ajaxReturn(ajax) == true) {
                  var response = ajax.responseText;

                  if(response == "success") {
                     _('forgotpassform').innerHTML = '<h3>Check your email inbox in a few minutes</h3>';
                  } else if (response == 'no_exist') {
                     _('status').innerHTML = 'That email address does not exist in our system';
                  } else if (response == 'email_send_failed') {
                     _('status').innerHTML = 'Unable to send email';
                  } else {
                     _('status').innerHTML = 'An unknown error has occurred';
                  }
               }
            }
            ajax.send('e='+e);
         }
      }

      function addEv() {
         _('email').addEventListener("focus", reset, false);
      }
      </script>
   </head>
   <body>
      <?php include_once("template_pageTop.php"); ?>
      <div id="pageMiddle">
         <h3>Create a temporary login password</h3>
         <!-- TEMP PASS FORM -->
         <form id="forgotpassform" onsubmit="return false;">
            <div>Enter Your Email Address:</div>
            <input type="text" id="email" maxlength="88">
            <br><br>
            <button id="forgotpassbtn" onclick="forgotpass()">Generate Temporary Password</button>
            <p id="status"></p>
         </form>
         <!-- TEMP PASS FORM -->
      </div>
      <?php include_once("template_pageBottom.php"); ?>
      <script type="text/javascript">
         addEv();
      </script>
   </body>
</html>
