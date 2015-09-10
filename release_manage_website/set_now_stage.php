<?php
require_once 'session_class.php';
?>
<?php
$session = new Session();
if(!$session->is_login) {
	echo "<script>alert('Invalid access!');location.href='index.php';</script>";
	exit;
}

if(!isset($_GET['stage'])) {
	echo "<script>alert('Invalid access!');location.href='index.php';</script>";
	exit;
}

if(array_search($_GET['stage'], $session->stages) === false) {
	exit("fail");
}
$_SESSION['now_stage'] = $session->now_stage = $_GET['stage'];

exit("success ($session->now_stage)");
