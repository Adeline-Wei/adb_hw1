<?php
require_once('inc.php');


$content = json_decode($_GET['content'], true);
$pos = $content['latLng']['B']." ".$content['latLng']['k'];

$result = PosWithinCustomArea($pos);
if($result != NULL){
	$country = array("id"=>$result['id'],"name"=>"C".$result['id']);
	$apps = getAppByCustomArea($result['id']);
}else{
	$country = getCountryByPos($pos);
	$apps = getAppByCountry($country['ISO3']);
}

echo json_encode(array("country" => $country, "apps" => $apps));