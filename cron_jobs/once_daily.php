<?php
   require_once("../php_includes/db_conx.php");
   // This block deletes all accounts that do not activate after 3 days

   $sql = "SELECT id, username, email FROM users WHERE signup<=CURRENT_DATE - INTERVAL 3 DAY AND activated='0'";
   $query = mysqli_query($db_conx, $sql);
   $numrows = mysqli_num_rows($query);
/*
   while($row_e = mysqli_fetch_array($query, MYSQLI_ASSOC)) {
      $id_e = $row_e["id"];
      $u_e = $row_e['username'];
      $e_e  = $row_e['email'];
      $ww_e .= "$id_e : $u_e - $e_e<br>";
   }
*/
   for ($i = 0; $i < $numrows; $i++) {
      mysqli_data_seek($query, $i);
      $row = mysqli_fetch_row($query);
      $ww_e .= "$row[0] : $row[1] - $row[2]<br>";
   }

   if ($numrows > 0) {
      while ($row = mysqli_fetch_array($query, MYSQLI_ASSOC)) {
         $id = $row['id'];
         $username = $row['username'];
         $userFolder = "../user/$username";
         if (is_dir($userFolder)) {
            rmdir($userFolder);
         }
         mysqli_query($db_conx, "DELETE FROM users WHERE id='$id' AND username='$username' AND activated='0' LIMIT 1");
         mysqli_query($db_conx, "DELETE FROM useroptions WHERE username='$username' LIMIT 1");
      }
   }

      #   mysqli_free_result($query);
      #   mysqli_close($$db_conx);
   $to = 'reports@tylerlego.com';
   $from = 'reports@tylerlego.com';
   $date = date("n/j/y");
   $subject = "Cleared Users Report $date";
   $message = "<!DOCTYPE html><html><head><meta charset='UTF-8'><title>tylerlego.com Message</title></head><body style='font-family:Tahoma, Geneva, sans-serif;'><h3>Users Cleared: $numrows</h3><p>$ww_e<p></body></html>";
   $headers = "From: $from\n";
   $headers .= "MIME-Version: 1.0\n";
   $headers .= "Content-type: text/html; charset=iso-8859-1\n";
   mail($to, $subject, $message, $headers);


?>
