<?php
// Ajax calls this REGISTRATION code to execute
include_once("php_includes/check_login_status.php");
if(isset($_POST["e"])){

	// CONNECT TO THE DATABASE
	include_once("php_includes/db_conx.php");
	// GATHER THE POSTED DATA INTO LOCAL VARIABLES
	$n = filter_var($_POST['n'], FILTER_SANITIZE_STRING);
	$e = filter_var($_POST['e'], FILTER_VALIDATE_EMAIL);
	$d = filter_var($_POST['d'], FILTER_SANITIZE_STRING);


	if($e == "" || $n == ""){
		echo "The form requires both name and a valid email";
      exit();
	} else {
		 // Email user's report
		$to = "reports@tylerlego.com";
		$from = "$e";
		$subject = 'TylerLego.com Error Report';
		$message = "Name: $n<br>";
		$message .= "Email: $e<br>";
		$message .= "Description: $d";

		$headers = "From: $from\n";
      $headers .= "MIME-Version: 1.0\n";
      $headers .= "Content-type: text/html; charset=iso-8859-1\n";

		mail($to, $subject, $message, $headers);

		$replyto = "$e";
		$replyfrom = "reports@tylerlego.com";
		$replysubject = "Thanks for the heads up!";
		$replymessage = "<h2>Thank you for the message, $n!</h2>";

		$replymessage .= "<p>As I continue to develop the tools used on this site, testing and finding bugs will only continue to grow in importance. By taking the time to send me an error report, you are making my job just a little bit easier. Thank you!</p>";
		$replymessage .= "<p>If you haven't already, head on over to our <a href='http://www.tylerlego.com/signup.php'>sign up</a> page and create an account. There isn't much functionality right now, but that will change as progress is made.</p>";
		$replymessage .= "<p>Thanks again!</p>";
		$replymessage .= "<p>Lego<br>";
		$replymessage .= "<a href='http://www.tylerlego.com'>tylerlego.com</a></p>";

		$replyheaders = "From: $replyfrom\n";
		$replyheaders .= "MIME-Version: 1.0\n";
		$replyheaders .= "Content-type: text/html; charset=iso-8859-1\n";

		mail($replyto, $replysubject, $replymessage, $replyheaders);

		// LOOK UP:
		// SEND MAIL SMTP VIA PHP SCRIPT
		echo "email_success";
		exit();
	 }
	exit();
}
?>
<?php

	$userList = '';
	$sql = "SELECT username FROM users WHERE activated='1'";
	$query = mysqli_query($db_conx, $sql);
	while ($row = mysqli_fetch_row($query)) {
		$userList .= "<a href='user.php?u=$row[0]'>$row[0]</a> | ";
	}
	$userList = rtrim($userList, " | ");



?>

<!DOCTYPE html>
<html>
   <head>
      <meta charset="utf-8">
      <title>LegoNet</title>
      <link rel="icon" href="favicon.ico" type="image/x-icon">
      <link rel="stylesheet" href="style/normalize.css">
      <link rel="stylesheet" href="style/style.css">
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.6.1/css/font-awesome.min.css">
      <style type="text/css">
         #reportform{
            margin-top:24px;
         }
         #reportform > div {
            margin-top: 12px;
         }
         #reportform > input,select {
            width: 200px;
            padding: 3px;
            background: #F3F9DD;
         }
         #reportform > textarea {
            padding: 3px;
            background: #F3F9DD;
         }
         #reportbtn {
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
         function reportError() {
            var e = _('email').value;
            var n = _('name').value;
            var d = _('description').value;
            var status = _("status");

            if (e == "" || n == "") {
               status.innerHTML = "Please fill out name and email";
            } else {
               _('reportbtn').style.display = "none";
               status.innerHTML = "Please wait...";

               var ajax = ajaxObj("POST", "index.php");
               ajax.onreadystatechange = function() {
                  if(ajaxReturn(ajax) == true) {
                     if(ajax.responseText != "email_success") {
								_("reportbtn").style.display = "block";
                        status.innerHTML = ajax.responseText;
                     } else {
                        _("reportform").innerHTML = "<h2>Thank you for the report!</h2>";
                     }
                  }
               }
                  ajax.send('n='+n+'&e='+e+'&d='+d);
            }
         }
      </script>
   </head>
   <body>
      <?php include_once 'template_pageTop.php'; ?>
      <div id="pageMiddle">
         <h1>Welcome to tylerlego.com!</h1>
         <p>
            At the moment, this site is not fully functional...but that's ok! I am building and testing different modules for a social sharing site using the LAMP stack. Go ahead and create an account, click around a little bit (you won't get far yet).  Everything that you can navigate to should work completely, so if you feel inclined to report any errors fill out the form below and shoot me an email. Thank you!
         </p>

         <form id="reportform" action="index.html" onsubmit="return false;" method="post">
            <div>Name:</div>
            <input type="text" id="name" name="name"><br>
            <div>Email:</div>
            <input type="text" id="email" name="email"><br>
            <div>Error Description:</div>
            <textarea id="description" name="description" rows="8" cols="40"></textarea>
            <br><br>
            <button id="reportbtn" type="button" onclick="reportError()" name="button">Send Error Report</button>
            <p id="status"></p>
         </form>
			<br><br>
			<div id="usersList">
				<h3>Activated Users:</h3>
				<?php echo $userList; ?>
			</div>
      </div>
      <?php include_once 'template_pageBottom.php' ?>
   </body>
</html>
