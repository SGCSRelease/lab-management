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

if(!isset($_POST['json_mng_id']) || !isset($_POST['json_po'])) {
	echo "<script>alert('Invalid access!');location.href='index.php';</script>";
	exit;
}

echo $_POST['json_mng_id'] . "@@@@@@@@@@@" . $_POST['json_po'];

$mng_id_arr = json_decode($_POST['json_mng_id']);
$mng_po_arr = json_decode($_POST['json_po']);

echo $mng_id_arr . "############" . $mng_po_arr;

$mysqli = connect_db();
$mngManager = new MngManager($session->login_user);
//$mngManager->change_is_can_mng($mysqli, $_GET['mng_id']);
?>