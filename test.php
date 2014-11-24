<?php
require_once('inc.php');

//print_r(getCountryByPos("120.982024 23.973875"));

//print_r(getAppByCountry("TW"));

//print_r(updateAppAverageRate(4, 'TW', '893518867'));
echo getAllCustomAreaWKT();

//createCustomArea("POLYGON((-63.6328125 81.67242422726376,-24.2578125 81.67242422726376,-24.2578125 65.80277639340238,-63.6328125 65.80277639340238,-63.6328125 81.67242422726376))");


echo "<pre>";
var_dump(PosWithinCustomArea("-44.252350999999976 66.82983573538964"));
echo "</pre>";