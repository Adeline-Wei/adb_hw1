<?php
require_once('inc.php');

//print_r(getCountryByPos("120.982024 23.973875"));

//print_r(getAppByCountry("TW"));

//print_r(updateAppAverageRate(4, 'TW', '893518867'));
$arr = getCustomAreas();

echo "<pre>";
var_dump($arr);
echo "</pre>";

$str = "";
foreach($arr as $g){
	$str .= str_replace("POLYGON","", $g['geom']);
}

$str = str_replace("))",")),",$str);
echo str_replace(",)", ")", preg_replace("/^(...*)/", "MULTIPOLYGON($0)", $str));