<?php
$dbhost = '127.0.0.1';
$dbuser = 'root';
$dbpass = '1234';
$dbname = 'adbhw1';

/* === Database Connection === */
$db = new mysqli($dbhost, $dbuser, $dbpass, $dbname);

if($db->connect_errno) {
	echo "Connection Failed: " . $db->connect_errno;
	exit();
}

$db->query("SET NAMES 'utf8'");

/* === Functions ===*/

function getCountryByPos($pos){
	global $db;
	$result = $db->query("SELECT cid, ISO3, name FROM country WHERE ST_CONTAINS (g, geomfromtext('POINT(".$pos.")'))");
	if($result->num_rows==0)
		$data = NULL;
	else
		$data = $result->fetch_array();
	$result->close();
	return $data;
}

function getAppByCountry($country){
	global $db;
	$result = $db->query("SELECT * FROM app WHERE country = '".$country."'");
	$i=0;
	while($row = $result->fetch_assoc()){
		$data[$i++] = $row;
	}
	$result->close();
	return $data;
}

function getAppByCustomArea($cid){
	global $db;
	$result = $db->query("SELECT * FROM app WHERE country = 'C".$cid."'");
	$i=0;
	while($row = $result->fetch_assoc()){
		$data[$i++] = $row;
	}
	$result->close();
	return $data;
}

function updateAppAverageRate($star, $country, $pid){
	global $db;
	$result = $db->query("SELECT average, times FROM app where app.country = '".$country."' and app.pid = '".$pid."'");
	$row = $result->fetch_assoc();
	$newTimes = $row['times'] + 1;
	$newAverage = ($row['average'] * $row['times'] + $star) / $newTimes;
	$newAverage = number_format($newAverage, 1, '.', '');
	if(!$db->query("UPDATE app SET average = ".(float)$newAverage.", times = ".$newTimes." WHERE app.country = '".$country."' AND app.pid = '".$pid."'"))
			echo "Errormessage: ".$db->error."\n";
	$result->close();
	return $newAverage;
}

function getCustomAreas(){
	global $db;
	$result = $db->query("SELECT AsText(g) AS geom FROM custom");
	while($row = $result->fetch_assoc()){
		$data[$i++] = $row;
	}
	$result->close();
	return $data;
}

function getAllCustomAreaWKT(){
	$arr = getCustomAreas();

	$str = "";
	foreach($arr as $g){
		$str .= str_replace("POLYGON","", $g['geom']);
	}

	$str = str_replace("))",")),",$str);
	return str_replace(",)", ")", preg_replace("/^(...*)/", "MULTIPOLYGON($0)", $str));
}

function PosWithinCustomArea($pos){
	global $db;
	$result = $db->query("SELECT id, name FROM custom WHERE ST_CONTAINS (g, geomfromtext('POINT(".$pos.")')) ORDER BY id DESC LIMIT 0,1");
	$i=0;
	if($result->num_rows==0)
		$data = NULL;
	else
		$data = $result->fetch_assoc();
	$result->close();
	return $data;
}

function createCustomArea($wkt){
	global $db;
	echo $wkt;
	if(!$db->query("INSERT INTO custom(id, name, g) VALUES (NULL, 'CXX', GeomFromText('".$wkt."'))") )
		echo "Errormessage: ".$db->error."\n";
	createCustomApp();
	return $wkt;
}

function createCustomApp(){
	global $db;
	$result = $db->query("SELECT id FROM custom ORDER BY id DESC LIMIT 0, 1");	// C1, C2, C3, ...
	$newId = $result->fetch_assoc();
	$result->close();
	$result = $db->query("SELECT pid FROM app GROUP BY pid ORDER BY RAND() LIMIT 0, 5");
	for($i=0; $i<5; $i++){
		$random[$i] = $result->fetch_assoc();
	}
	var_dump($random);
	for ($i = 0; $i < 5; $i ++){
		$result2 = $db->query("SELECT pid, name, icon, url FROM app WHERE pid = ".$random[$i]['pid']);
		$data = $result2->fetch_assoc();
		$sql = "INSERT INTO app(id, pid, country, name, icon, url, price, currency, average, times) VALUES (NULL, ".$data['pid'].", 'C".$newId['id']."', '".$data['name']."', '".$data['icon']."', '".$data['url']."', 0, 'CUS', 0, 0)";
		if (! $db->query($sql) )
			echo "Errormessage: ".$db->error."\n";
		$result2->close();
	}
}