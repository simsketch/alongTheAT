<?php
   include_once("php_includes/check_login_status.php");
   // Initialize any variables that the page might echo
   $u = "";
   $sex = "Male";
   $userlevel = "";
   $country = "";
   $joindate = "";
   $lastsession = "";
   // Make sure the _GET username is set, and sanitize it
   if(isset($_GET["u"])){
   	$u = preg_replace('#[^a-z0-9]#i', '', $_GET['u']);
   } else {
       header("location: http://www.yoursite.com");
       exit();
   }
   // Select the member from the users table
   $sql = "SELECT * FROM users WHERE username='$u' AND activated='1' LIMIT 1";
   $user_query = mysqli_query($db_conx, $sql);
   // Now make sure that user exists in the table
   $numrows = mysqli_num_rows($user_query);
   if($numrows < 1){
   	echo "That user does not exist or is not yet activated, press back";
       exit();
   }
   // Check to see if the viewer is the account owner
   $isOwner = "no";
   if($u == $log_username && $user_ok == true){
   	$isOwner = "yes";
   }
   // Fetch the user row from the query above
   while ($row = mysqli_fetch_array($user_query, MYSQLI_ASSOC)) {
   	$profile_id = $row["id"];
   	$gender = $row["gender"];
   	$country = $row["country"];
   	$userlevel = $row["userlevel"];
   	$signup = $row["signup"];
   	$lastlogin = $row["lastlogin"];
   	$joindate = strftime("%b %d, %Y", strtotime($signup));
   	$lastsession = strftime("%b %d, %Y", strtotime($lastlogin));
   	if($gender == "f"){
   		$sex = "Female";
   	}
   }
?>
<?php
   $isFriend = false;
   $ownerBlockViewer = false;
   $viewerBlockOwner = false;
   if($u != $log_username && $user_ok == true){
   	$friend_check = "SELECT id FROM friends WHERE user1='$log_username' AND user2='$u' AND accepted='1' OR user1='$u' AND user2='$log_username' AND accepted='1' LIMIT 1";
   	if(mysqli_num_rows(mysqli_query($db_conx, $friend_check)) > 0){
           $isFriend = true;
       }
   	$block_check1 = "SELECT id FROM blockedusers WHERE blocker='$u' AND blockee='$log_username' LIMIT 1";
   	if(mysqli_num_rows(mysqli_query($db_conx, $block_check1)) > 0){
           $ownerBlockViewer = true;
       }
   	$block_check2 = "SELECT id FROM blockedusers WHERE blocker='$log_username' AND blockee='$u' LIMIT 1";
   	if(mysqli_num_rows(mysqli_query($db_conx, $block_check2)) > 0){
           $viewerBlockOwner = true;
       }
   }
?>
<?php
   $friend_button = '<button disabled>Request As Friend</button>';
   $block_button = '<button disabled>Block User</button>';
   // LOGIC FOR FRIEND BUTTON
   if($isFriend == true){
   	$friend_button = '<button onclick="friendToggle(\'unfriend\',\''.$u.'\',\'friendBtn\')">Unfriend</button>';
   } else if($user_ok == true && $u != $log_username && $ownerBlockViewer == false){
   	$friend_button = '<button onclick="friendToggle(\'friend\',\''.$u.'\',\'friendBtn\')">Request As Friend</button>';
   }
   // LOGIC FOR BLOCK BUTTON
   if($viewerBlockOwner == true){
   	$block_button = '<button onclick="blockToggle(\'unblock\',\''.$u.'\',\'blockBtn\')">Unblock User</button>';
   } else if($user_ok == true && $u != $log_username){
   	$block_button = '<button onclick="blockToggle(\'block\',\''.$u.'\',\'blockBtn\')">Block User</button>';
   }
?>
<?php
include('ChromePhp.php');
$friendsHTML = ''; // html for displaying friends list, as thumbnails
$friends_view_all_link = ''; // link to complete friends page for user
// starts empty because a user might have less than the max viewable friends and not need view all link
$sql = "SELECT COUNT(id) FROM friends WHERE user1='$u' AND accepted='1' OR user2='$u' AND accepted='1'";
$query = mysqli_query($db_conx, $sql);
$query_count = mysqli_fetch_row($query);
$friend_count = $query_count[0];
if ($friend_count < 1) {
   $friendsHTML = $u." has no friends yet";
   $friend_count = "0 friends";
} else {
   $max = 6;
   $all_friends = array();
   $sql = "SELECT user1 FROM friends WHERE user2='$u' AND accepted='1' ORDER BY RAND() LIMIT $max";
   $query = mysqli_query($db_conx, $sql);
   while ($row = mysqli_fetch_array($query, MYSQLI_ASSOC)) {
      array_push($all_friends, $row["user1"]);
   }

   $sql = "SELECT user2 FROM friends WHERE user1='$u' AND accepted='1' ORDER BY RAND() LIMIT $max";
   $query = mysqli_query($db_conx, $sql);
   while ($row = mysqli_fetch_array($query, MYSQLI_ASSOC)) {
      array_push($all_friends, $row["user2"]);
   }

   shuffle($all_friends);
   $friendArrayCount = count($all_friends);
   if($friendArrayCount > $max) {
      array_splice($all_friends, $max);
   }
   if ($friend_count > $max) {
      $friends_view_all_link = '<a href="view_friends.php?u='.$u.'">view all</a>';
   }
   $orLogic = '';
   foreach($all_friends as $key => $user) {
      $orLogic .= "username='$user' OR ";
   }
   $orLogic = chop($orLogic, "OR ");
   $sql = "SELECT username, avatar FROM users WHERE $orLogic";
   $query = mysqli_query($db_conx, $sql);
   while ($row = mysqli_fetch_array($query, MYSQLI_ASSOC)) {
      $friend_username = $row['username'];
      $friend_avatar = $row['avatar'];
      if ($friend_avatar != '') {
         $friend_pic = 'user/'.$friend_username.'/'.$friend_avatar.'';
      } else {
         $friend_pic = 'images/avatardefault.jpg';
      }
      $friendsHTML .= '<a href="user.php?u='.$friend_username.'"><img class="friendpics" src="'.$friend_pic.'" alt="'.$friend_username.'" title="'.$friend_username.'" width="100" height="100"></a><span>&nbsp;</span>';
   }
   if ($friend_count == 1) {
      $friend_count = $friend_count.' friend';
   } else {
      $friend_count = $friend_count.' friends';
   }
}

?>


<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8">
		<title><?php echo $u; ?></title>
		<link rel="icon" href="favicon.ico" type="image/x-icon">
		<link rel="stylesheet" href="style/normalize.css">
      <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.6.1/css/font-awesome.min.css">
		<link rel="stylesheet" href="style/style.css">
      <script src="js/main.js"></script>
      <script src="js/ajax.js"></script>
      <script type="text/javascript">
         function friendToggle(type,user,elem){
         	var conf = confirm("Press OK to confirm the '"+type+"' action for user <?php echo $u; ?>.");
         	if(conf != true){
         		return false;
         	}
         	_(elem).innerHTML = 'please wait ...';
         	var ajax = ajaxObj("POST", "php_parsers/friend_system.php");
         	ajax.onreadystatechange = function() {
         		if(ajaxReturn(ajax) == true) {
         			if(ajax.responseText == "friend_request_sent"){
         				_(elem).innerHTML = 'Friend Request Sent';
         			} else if(ajax.responseText == "unfriend_ok"){
         				_(elem).innerHTML = '<button onclick="friendToggle(\'friend\',\'<?php echo $u; ?>\',\'friendBtn\')">Request As Friend</button>';
         			} else {
         				alert(ajax.responseText);
         				_(elem).innerHTML = 'Try again later';
         			}
         		}
         	}
         	ajax.send("type="+type+"&user="+user);
         }
         function blockToggle(type,blockee,elem){
         	var conf = confirm("Press OK to confirm the '"+type+"' action on user <?php echo $u; ?>.");
         	if(conf != true){
         		return false;
         	}
         	var elem = document.getElementById(elem);
         	elem.innerHTML = 'please wait ...';
         	var ajax = ajaxObj("POST", "php_parsers/block_system.php");
         	ajax.onreadystatechange = function() {
         		if(ajaxReturn(ajax) == true) {
         			if(ajax.responseText == "blocked_ok"){
         				elem.innerHTML = '<button onclick="blockToggle(\'unblock\',\'<?php echo $u; ?>\',\'blockBtn\')">Unblock User</button>';
         			} else if(ajax.responseText == "unblocked_ok"){
         				elem.innerHTML = '<button onclick="blockToggle(\'block\',\'<?php echo $u; ?>\',\'blockBtn\')">Block User</button>';
         			} else {
         				alert(ajax.responseText);
         				elem.innerHTML = 'Try again later';
         			}
         		}
         	}
         	ajax.send("type="+type+"&blockee="+blockee);
         }
      </script>
   </head>
   <body>
      <?php include_once("template_pageTop.php"); ?>
      <div id="pageMiddle">
         <h3><?php echo $u; ?></h3>
         <p>Is the viewer the page owner, logged in and verified? <b><?php echo $isOwner; ?></b></p>
         <p>Gender: <?php echo $sex; ?></p>
         <p>Country: <?php echo $country; ?></p>
         <p>User Level: <?php echo $userlevel; ?></p>
         <p>Join Date: <?php echo $joindate; ?></p>
         <p>Last Login: <?php echo $lastsession; ?></p>
         <br>

         <p><span id="friendBtn"><?php echo $friend_button; ?></span><?php echo "&nbsp".$u." has ".$friend_count; echo $friends_view_all_link; ?></p>
         <p><span id="blockBtn"><?php echo $block_button; ?></span></p>
         <hr>
         <p><?php echo $friendsHTML; ?></p>
      </div>
      <?php include_once('template_pageBottom.php'); ?>
   </body>
</html>
