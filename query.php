<?php
$dbhost = '127.0.0.1';
$dbuser = 'root';
$dbpass = '1234';
$dbname = 'adb';
$conn = mysql_connect($dbhost, $dbuser, $dbpass) or die('Error with MySQL connection');
mysql_query("SET NAMES 'utf8'");
mysql_select_db($dbname);
# 完成連接資料庫

$content = json_decode($_GET['content'], true);	# true:將std Class變成Array
$latLng_clicked = $content['latLng']['B']." ".$content['latLng']['k'];	# B:經度 k:緯度

# 找到使用者點擊的國家(c_id)
$resCountry = mysql_query("SELECT cid, Name FROM country WHERE ST_CONTAINS (g, geomfromtext('POINT(".$latLng_clicked.")'))");
$row = mysql_fetch_array($resCountry, MYSQL_NUM);
# 計算該國家的產品數量
if ($resProducts = mysql_query("SELECT COUNT(*) FROM product WHERE product.cid = ".$row[0]))
{
	$numProducts = mysql_fetch_array($resProducts);
}	
$jsonArray = array();
# 找到該國家的所有產品(p_id) & 列出
if ($resProducts = mysql_query("SELECT product.Name, product.pid FROM product WHERE product.cid = ".$row[0]))
{
	# 找到產品的評論 & 列出
	$tmp = array();
	//$tmp['cid'] = $row[0];	# Country ID
	while ($row1 = mysql_fetch_array($resProducts, MYSQL_NUM))
	{
		$tmp['name'] = $row1[0];	# Product Name
		$tmp['cid'] = $row[0];
		$tmp['pid'] = $row1[1];		# Product ID
		if ($resReviews = mysql_query("SELECT rid, content FROM review WHERE cid = ".$row[0]." and pid = ".$row1[1]))
		{
			$tmp3 = array();
			while ($row2 = mysql_fetch_array(($resReviews), MYSQL_NUM))
			{
				$tmp2['rid'] = $row2[0];
				$tmp2['content'] = $row2[1];
				array_push($tmp3, $tmp2);
			}
			$tmp['contents'] = $tmp3;
		}
		array_push($jsonArray, $tmp);
	}
	echo json_encode($jsonArray);
}
?>