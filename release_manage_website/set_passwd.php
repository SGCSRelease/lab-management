<?php
require_once 'session_class.php';
require_once 'mng_class.php';
require_once 'db_connect.php';
?>
<?php
$session = new Session();
if(!$session->is_login) {
	echo "<script>alert('Invalid access!');location.href='index.php';</script>";
	exit;
}

if(!isset($_POST['user_pw'])) {
	echo "<script>alert('Invalid access!');location.href='index.php';</script>";
	exit;
}

if(strlen($_POST['user_pw']) != 128) {
	exit("fail");
}

$mysqli = connect_db();
if( ! $session->set_user_pw($mysqli, $_POST['user_pw']) )
	exit("fail");
exit("success");
?>