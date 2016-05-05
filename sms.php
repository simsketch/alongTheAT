<?php

$colin_num = "8285756822@vtext.com";
$lego_num = "5613139761@messaging.sprintpcs.com";
/* watch the video for detailed instructions */


$to = "5613139761@messaging.sprintpcs.com";
$from = "contact@tylerlego.com";
$message = "Jake, my friend...is that your name?";
$headers = "From: $from\n";
mail($to, '', $message, $headers); // subject is left blank: ''

echo "it works";

/*
( HLR Lookup API )  use to determine carrier automatically

POPULAR US SMS GATEWAY DOMAINS
Verizon Wireless - vtext.com
Virgin Mobile - vmobl.com
Alltel - sms.alltelwireless.com
ATT - txt.att.net
Boost Mobile - sms.myboostmobile.com
Republic Wireless - text.republicwireless.com
Sprint - messaging.sprintpcs.com
T-Mobile - tmomail.net
U.S. Cellular - email.uscc.net
*/

?>
