<?php
$dbhost = '127.0.0.1';
$dbuser = 'root';
$dbpass = '1234';
$dbname = 'adb';
$conn = mysql_connect($dbhost, $dbuser, $dbpass) or die('Error with MySQL connection');
mysql_query("SET NAMES 'utf8'");
mysql_select_db($dbname);
# 完成連接資料庫

# 取得cid, pid, review
$cpid = $_GET['content2'];
$review = $_GET['content3'];
$id = explode('_', $cpid);

# 將review insert 到資料庫
$query = "INSERT INTO review VALUES (".$id[0].", ".$id[1].", NULL, '".$review."')";
mysql_query($query) or die ('Error with MySQL Query');

# review insert 成功！
echo $id[1].'<br>';
echo $review.'<br>';
?>