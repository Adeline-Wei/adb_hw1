<?php
require_once('inc.php');

$star = $_GET['star'];
$country = $_GET['country'];
$pid = $_GET['pid'];

print_r(updateAppAverageRate($star, $country, $pid)); 
//echo 'Parameters:'.$star.' '.$country.' '.$pid;