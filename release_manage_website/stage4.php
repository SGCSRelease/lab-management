<?php
require_once 'session_class.php';
?>
<?php //ini_set('error_reporting', E_STRICT);
$session = new Session();
if(!$session->is_login) {
	echo "<script>alert('Invalid access!');location.href='index.php';</script>";
	exit;
}

$session->upgrade_stage("stage4");

if(!$session->login_user->is_exist_pw) {
	$text1 = "Step 4. 다음 접속시에 사용할 패스워드를 설정하세요~";
	$text2 = "Password설정";
	$text3 = "내 계정은 오픈소스다.";
} else {
	$text1 = "Step 4. 이미 패스워드 있으신데 재 설정 하실꺼임?";
	$text2 = "Password 재!설!정!";
	$text3 = "했는데 왜 또해 ㅡㅡ";
}
?>
<h1><?php echo $text1 ?></h1><br>
<form id='stage4_form' class="form-inline" role="form">
	<div class="form-group">
		<label class="sr-only" for="user_pw">Password</label>
		<input type="password" name='user_pw' class="form-control my_input" placeholder="input your password">
	</div>
	<button type="submit" class="btn btn-default my_input" onClick="stage4_form_submit();">
		<?php echo $text2 ?>
	</button>
	<button type="reset" class="btn btn-default my_input" onClick="load_thankyou();">
		<?php echo $text3 ?>
	</button>
</form>
<script type="text/javascript">
	function stage4_form_submit() {
		var input_pw = $('#stage4_form input[name="user_pw"]').val().trim();
		if(input_pw == "") {
			alert("fuck that empty password!");
			return false;
		}
		$('#stage4_form input[name="user_pw"]').val("")
		var user_pw = hex_sha512(input_pw);
		$.post("set_passwd.php", {user_pw: user_pw}, function(data) {
			console.log("-->"+data);
			if(data=="fail") {
				alert("fail to set password");
				location.reload();
			} else {
				unload_stage4();
				load_stage4();
				load_thankyou();
			}
		});
		return false;
	}
	
	function after_load_stage4() {}
</script>