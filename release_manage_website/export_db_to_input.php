<?php
require_once 'db_connect.php';

$mysqli = connect_db();

if ($result = $mysqli->query ( "SELECT mng_id, mng_day, mng_period FROM mng_list ORDER BY mng_period ASC, mng_day ASC" )) {
	$mng_num = $result->num_rows;
	echo $mng_num . "\n";

	for ($i=0; $i < $mng_num; $i++) {
		$row = $result->fetch_assoc();
		echo $row['mng_id']." ".$row['mng_day']." ".$row['mng_period']."\n";	
	}
	$result->close();
} else return false;

echo "\n";

if ($result = $mysqli->query ( "SELECT u.ul_id, u.user_name, count(a.ul_id) as cnt FROM user_list u LEFT OUTER JOIN apply_list a ON a.ul_id=u.ul_id and is_can_mng=1 and prefer_order is not null group by u.ul_id" )) {
	$user_num = $result->num_rows;
	echo $user_num . "\n";

	for ($i=0; $i < $user_num; $i++) {
		$row = $result->fetch_assoc();
		echo $row['ul_id']." ".$row['user_name']." ".$row['cnt']."\n";
	}
	$result->close();
} else return false;

echo "\n";

if ($result = $mysqli->query( "SELECT count(*) as apply_num, MAX(prefer_order) as max_po FROM apply_list WHERE is_can_mng = 1 and prefer_order is not NULL" )) {
	$row = $result->fetch_assoc();
	echo $row['apply_num'] . " " . $row['max_po'] . "\n";
}

if ($result = $mysqli->query ( "SELECT ul_id, mng_id, prefer_order FROM apply_list WHERE is_can_mng = 1 and prefer_order is not NULL" )) {
	$apply_num = $result->num_rows;

	for ($i=0; $i < $apply_num; $i++) {
		$row = $result->fetch_assoc();
		echo $row['ul_id']." ".$row['mng_id']." ".$row['prefer_order']."\n";
	}
	$result->close();
} else return false;

echo "\n";

?>
