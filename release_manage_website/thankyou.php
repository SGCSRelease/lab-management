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

$session->upgrade_stage("thankyou");
$r = mt_rand(0,1);
$makers = Array("Developed by 태궈","Algorithm by 쾅희");
?>

<h1 style="font-size:3.5rem;">Thank you !</h1><br>
<button type="button" class="btn btn-default my_input" onClick='req_logout();'>Logout</button><button type="button" class="btn btn-default my_input" onClick='back_to_first();'>초심으로 돌아가기</button><br><br>
<h1 style='font-weight:bold;'>
<?php
echo $makers[$r];
echo "<br>";
echo $makers[($r+1)%2];
?>
</h1>
<script type="text/javascript">
	function after_load_thankyou() {}
	function back_to_first() {
		unload_stage2();
	}
</script>