<?php
$to = 'contact@tylerlego.com';
$from = 'contact@tylerlego.com';
$subject = 'Test cron job';
$message = 'CRON IS WORKING';
$headers = "From: $from\n";
$headers .= "MIME-Version: 1.0\n";
$headers .= "Connect-type: text/html; charset=iso-8859-1\n";
mail($to, $subject, $message, $headers);
?>
