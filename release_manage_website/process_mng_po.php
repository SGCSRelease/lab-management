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

//echo $_POST['json_mng_id'] . "@@@@@@@@@@@" . $_POST['json_po'];

$mng_id_arr = json_decode($_POST['json_mng_id']);
$mng_po_arr = json_decode($_POST['json_po']);

$mng_po_arr_sorted = $mng_po_arr;
sort($mng_po_arr_sorted);
foreach ($mng_po_arr_sorted as $i => $po) {
	if($i + 1 != $po)
		exit("fail");
}

$mng_id_po_arr = array_combine($mng_id_arr, $mng_po_arr);

//echo $mng_id_arr . "############" . $mng_po_arr;

$mysqli = connect_db();

$mngManager = new MngManager($session->login_user);

if(!$mngManager->update_mng_po($mysqli, $mng_id_po_arr))
	exit("fail");
//$mngManager->change_is_can_mng($mysqli, $_GET['mng_id']);
exit("success");
?>