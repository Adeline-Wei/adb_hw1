<?php
require_once('inc.php');


$content = json_decode($_GET['content'], true);
$pos = $content['latLng']['B']." ".$content['latLng']['k'];

$country = getCountryByPos($pos);
$apps = getAppByCountry($country['ISO3']);

echo json_encode(array("country" => $country, "apps" => $apps));

