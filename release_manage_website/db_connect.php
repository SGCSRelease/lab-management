<?php 
require_once 'db_config.php';

function connect_db() {
	$mysqli = new mysqli(DB_SERVER_ADDR, DB_USER_ID, DB_USER_PW, DB_NAME);
	$mysqli->set_charset("UTF8");
	return $mysqli;
}
?>