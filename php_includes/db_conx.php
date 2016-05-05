<?php

$db_hostname = 'localhost';
$db_username = 'tylerleg_lego';
$db_password = 'Yz3rm@n1';
$db_database = 'tylerleg_social';

/*
   $db_con = new mysqli($db_hostname, $db_username, $db_password, $db_database);

   if ($db_con->connect_error) die($db_con->connect_error);
*/

   $db_conx = mysqli_connect($db_hostname, $db_username, $db_password, $db_database);
   if (mysqli_connect_errno()) { // mysql_connect_errno returns error code of last connect call
      echo mysqli_connect_error(); // returns string description of last error
      exit();
   }

?>
