<?php
require_once 'session_class.php';
require_once 'db_connect.php';
?>
<?php
$session = new Session();

$msg = "";
$login_success = false;
$is_exist_pw = false;

if(isset($_POST['user_name'])) {
	$user_name = $_POST['user_name'];
	$mysqli = connect_db();
	
	$is_valid = $session->is_valid_user_name($user_name, $mysqli);
	if(!$is_valid) {
		$msg = "누구냐 넌?";
	} else {
		if($session->is_exist_pw($user_name, $mysqli))
			$is_exist_pw = true;
		
		if(isset($_POST['user_pw'])) {
			$user_pw = $_POST['user_pw'];
			$is_login = $session->login($user_name, $user_pw, $mysqli);
			if(!$is_login)
				$msg = "비밀번호가 틀렸습니다!";
			else
				$login_success = true;
		} else {
			if(!$is_exist_pw) {
				$is_login = $session->login($user_name, "", $mysqli);
				$login_success = true;
			} else {
				$msg = "비밀번호도 입력해주세요 ^^";
			}
		}
	}
	$mysqli->close();
}
if($session->is_login) {
?>
<h1>You are Logined! (<?php echo $session->login_user->user_name ?>)</h1>
<button onclick="req_logout();">Logout</button>
<script>
	function after_load_login() {
		console.log('is_login true');
		<?php if($login_success) echo "alert('login success!');load_stage1();" ?>
	};
</script>
<?php
}
else {
?>
<script>
	function after_load_login() {
		console.log('is_login false');
		//i$('input[name="user_name"]').focus(function() { $.fn.fullpage.rebuild(); });
		//$('input').blur(function() { console.log("input blur"); $.fn.fullpage.setAutoScrolling(true); });
		//$('input').focus(function() { console.log("input focus"); $.fn.fullpage.setAutoScrolling(true); });
					
		//$('input').blur(function() { $.fn.fullpage.moveTo(1, 0); $.fn.fullpage.rebuild(); });
		/*
		$('input').blur(function() {
			setTimeout(function() {
				if (!$(document.activeElement).is('input')) {
					$(window).scrollTop(0,0);
				}
			}, 0);
		});
		 */
	};
</script>
<h1 style="font-size:3rem;color:#f0e68c;">릴리즈 관리 신청 페이지</h1><br>
<h1><?php echo $msg ?></h1>
<form id='login_form' class="form-inline" role="form" onsubmit="req_login(); return false;">
	<input type='hidden' name='type' value='<?php if($is_exist_pw) echo '2'; else echo '1'; ?>'>
	<div class="form-group">
		<label class="sr-only" for="user_name">User Name</label>
		<input type="text" name='user_name' class="form-control my_input" placeholder="학번+이름 ex) 14권태국" value="<?php if($is_exist_pw) echo "$user_name";?>">
		<?php
		if($is_exist_pw) echo "<input type='password' name='user_pw' class='form-control my_input' placeholder='비밀번호'>";
		?>
	</div>
	<button type="submit" class="btn btn-default my_input">
		Sign in
	</button>
</form>

<?php
}
?>
