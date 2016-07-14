<?php
$time_start = microtime(TRUE); 

require_once('/class/config.php');

require_once('/class/darkmeta.php');

//initialise the class
$DarkMeta = new DarkMeta();

//sterilz the url
$DarkMeta->url_heartbeat('http://www.gmail.com:80');

$DarkMeta->URL_GET($DarkMeta->url_host);

$time_end = microtime(TRUE);
//dividing with 60 will give the execution time in minutes other wise seconds
$execution_time = ($time_end - $time_start);
?>
