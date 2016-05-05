<?php
   session_start();

   if (isset($_SESSION['username'])) {
      header('Location: user.php?u='.$_SESSION['username']);
      exit();
   }
?>
<?php

// AJAX CALLS THIS LOGIN CODE TO EXECUTE
   if(isset($_POST['e'])) {
      // CONNECT TO DATABASE
      include_once('php_includes/db_conx.php');
      // GATHER THE POSTED DATA INTO LOCAL VARIABLES AND SANITIZE
      $e = mysqli_real_escape_string($db_conx, $_POST['e']);
      $p = md5($_POST['p']);
      // GET USER IP ADDRESS
      $ip = preg_replace('#[^0-9.]#', '', getenv('REMOTE_ADDR'));
      // FORM DATA ERROR HANDLING
      if ($e == "" || $p == "") {
         echo "login_failed";
         exit();
      } else {
      // END FORM DATA ERROR HANDLING
         $sql = "SELECT id, username, password FROM users WHERE email='$e' AND activated='1' LIMIT 1";
         $query = mysqli_query($db_conx, $sql);
         $numrows = mysqli_num_rows($query);
         if ($numrows < 1) {
            echo "login_failed";
            exit();
         }
         $row = mysqli_fetch_row($query);
         $db_id = $row[0];
         $db_username = $row[1];
         $db_pass_str = $row[2];
         if($p != $db_pass_str){
   			echo "login_failed";
            exit();
   		} else {
			// CREATE THEIR SESSIONS AND COOKIES
   			$_SESSION['userid'] = $db_id;
   			$_SESSION['username'] = $db_username;
   			$_SESSION['password'] = $db_pass_str;
   			setcookie("id", $db_id, strtotime( '+30 days' ), "/", "", "", TRUE);
   			setcookie("user", $db_username, strtotime( '+30 days' ), "/", "", "", TRUE);
      		setcookie("pass", $db_pass_str, strtotime( '+30 days' ), "/", "", "", TRUE);
   			// UPDATE THEIR "IP" AND "LASTLOGIN" FIELDS
   			$sql = "UPDATE users SET ip='$ip', lastlogin=now() WHERE username='$db_username' LIMIT 1";
            $query = mysqli_query($db_conx, $sql);
   			echo $db_username;
		      exit();
         }
      }
      exit();
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
			#loginform{
				margin-top:24px;
			}
			#loginform > div {
				margin-top: 12px;
			}
			#loginform > input,select {
				width: 200px;
				padding: 3px;
				background: #F3F9DD;
			}
			#loginbtn {
				font-size:18px;
				padding: 12px;
			}
			#terms {
				border:#CCC 1px solid;
				background: #F5F5F5;
				padding: 12px;
			}
		</style>
		<script src="js/main.js"></script>
		<script src="js/ajax.js"></script>
      <script type="text/javascript">
      function emptyElement(x) {
         _(x).innerHTML = "";
      }
      function statusEmpty() {
         emptyElement('status');
      }

      function login() {
         var e = _('email').value;
         var p = _('password').value;

         if ((e == "") || (p == "")) {
            _('status').innerHTML = "Please enter username and password";
         }  else {
            _('loginbtn').style.display = "none";
            _('status').innerHTML = "Please wait...";

            var ajax = ajaxObj('POST', 'login.php');
            ajax.onreadystatechange = function() {
               if (ajaxReturn(ajax) == true) {
                  if(ajax.responseText == "login_failed") {

alert('login fail');

                     _('status').innerHTML = "Login unsuccessful, please try again.";
                     _('loginbtn').style.display = "block";
                  } else {
                     window.location = "user.php?u="+ajax.responseText;
                  }
               }
            }
            ajax.send('e='+e+'&p='+p);
         }
      }

      function addEvents(){
         _('email').addEventListener("focus", statusEmpty, false);
         _('password').addEventListener("focus", statusEmpty, false);
      }
      </script>
   </head>
   <body>
      <?php include_once("template_pageTop.php"); ?>
      <div id="pageMiddle">
         <h3>Log In</h3>
         <!-- LOGIN FORM -->
         <form id="loginform" onsubmit="return false;">
            <div>Email Address:</div>
            <input type="text" id="email" maxlength="88">
            <div>Password:</div>
            <input type="password" id="password" maxlength="50">
            <br><br>
            <button id="loginbtn" onclick="login()">Log In</button>
            <p id="status"></p>
            <a href="forgot_pass.php">Forgot Your Password?</a>
         </form>
         <!-- END LOGIN FORM -->
      </div>
      <?php include_once("template_pageBottom.php"); ?>


      <script type="text/javascript">
         addEvents();
      </script>
   </body>
</html>
