<?php
require_once 'session_class.php';
require_once 'mng_class.php';
require_once 'db_connect.php';
?>
<?php //ini_set('error_reporting', E_STRICT);
$session = new Session();
if(!$session->is_login) {
	echo "<script>alert('Invalid access!');location.href='index.php';</script>";
	exit;
}

$mysqli = connect_db();
$mngManager = new MngManager($session->login_user);
$mngManager->load_mng_list($mysqli);

$session->upgrade_stage("stage3");
?>
<h1>Step 3. 다시 한번 신청을 확인해 주세요.</h1><br>
<?php
echo "<div class='table-responsive'>";
echo "<table class='table table-striped table-bordered'>";
echo "<thead>";
echo "<th>교시</th>";
for ($i=0; $i <$mngManager->MNG_DAY_NUM ; $i++) { 
	echo "<th>".$mngManager->MNG_DAY_NAMES[$i]."</th>";
}
echo "</thead>";
echo "<tbody>";
for ($i=0; $i < $mngManager->mng_num; $i++) {
	$mng = $mngManager->mngs[$i];
	if($i % $mngManager->MNG_DAY_NUM == 0) echo "<tr><th>".$mngManager->MNG_PERIOD_NAMES[$i / $mngManager->MNG_DAY_NUM]."</th>";
	$class="mng_class_$mng->mng_id ";
	if(!empty($mng->prefer_order) && intval($mng->prefer_order) >= 1) $class .= "is_prefer_mng ";
	else if($mng->is_can_mng) $class .= "is_can_mng ";
	else $class .= "is_cannot_mng ";
	if(!empty($mng->prefer_order) && intval($mng->prefer_order) >= 1) $content = $mng->prefer_order . "지망";
	else $content = "";
	echo "<td id='mng2_$mng->mng_id' class='${class}'>$content</td>";
	if($i % $mngManager->MNG_DAY_NUM == $mngManager->MNG_DAY_NUM-1) echo "</tr>";
}
echo "</tbody>";
echo "</table>";
echo "</div>";
?>

<button onClick='unload_stage3();'>Back Step (잘못됐어요!)</button>
<button onClick='load_stage4();'>Next Step (굿굿!)</button>
<script type='text/javascript'>
	function after_load_stage3() {
		console.log('after_load_stage3 call!!');
		<?php
		?>
	}
</script>
