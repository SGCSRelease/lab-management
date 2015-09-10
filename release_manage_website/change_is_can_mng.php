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

if(!isset($_GET['mng_id'])) {
	echo "<script>alert('Invalid access!');location.href='index.php';</script>";
	exit;
}

$mysqli = connect_db();
$mngManager = new MngManager($session->login_user);
$mngManager->change_is_can_mng($mysqli, $_GET['mng_id']);
?>