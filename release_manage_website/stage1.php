<?php
require_once 'session_class.php';
require_once 'mng_class.php';
require_once 'db_connect.php';
?>
<?php //ini_set('error_reporting', E_STRICT);
$session = new Session();
if(!$session->is_login) {
	echo "asdf ".$_SESSION['is_login']." asdf";
	echo "<script>alert('pppp');";
	//echo "<script>alert('Invalid access!');location.href='index.php';</script>";
	exit();
}

$mysqli = connect_db();
$mngManager = new MngManager($session->login_user);
$mngManager->load_mng_list($mysqli);

$session->upgrade_stage("stage1");
?>
<h1>Step 1. 관리 불가능한 시간들을 골라주세요.</h1><br>
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
	if($mng->is_can_mng) $class .= "is_can_mng ";
	else $class .= "is_cannot_mng is_cannot_mng_for_stage1 ";
	$class .= "yes_hover ";	
	echo "<td id='mng_$mng->mng_id' class='${class}'></td>";
	if($i % $mngManager->MNG_DAY_NUM == $mngManager->MNG_DAY_NUM-1) echo "</tr>";
}
echo "</tbody>";
echo "</table>";
echo "</div>";
?>
<button onClick='req_logout();'>Logout</button>
<button onClick='load_stage2();'>Next Step</button>
<script type='text/javascript'>
	function change_is_can_mng(mng_id) {
		if(now_stage_idx > 1)
			return;
		console.log("change_is_can_mng("+mng_id+")");
		$.get('change_is_can_mng.php',  {mng_id: mng_id}, function(data) {
			if(data=="") {
				if($(".mng_class_"+mng_id).hasClass("is_can_mng")) {
					$(".mng_class_"+mng_id).removeClass("is_can_mng");
					$(".mng_class_"+mng_id).addClass("is_cannot_mng");
					$(".mng_class_"+mng_id).addClass("is_cannot_mng_for_stage1");
				} else {
					$(".mng_class_"+mng_id).addClass("is_can_mng");
					$(".mng_class_"+mng_id).removeClass("is_cannot_mng");
					$(".mng_class_"+mng_id).removeClass("is_cannot_mng_for_stage1");
				}
				
			}
			//location.reload();
		});
	}
	function after_load_stage1() {
		console.log('after_load_stage1 call!!');
		<?php
		for ($i=0; $i < $mngManager->mng_num; $i++) { 
			$mng_id = $mngManager->mngs[$i]->mng_id;
			echo "$('#mng_$mng_id').click(function(){change_is_can_mng($mng_id)});";
		}
		?>
	}
</script>
