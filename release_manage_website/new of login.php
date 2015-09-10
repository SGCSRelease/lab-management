<?php
require_once 'session_class.php';
require_once 'db_connect.php';
?>
<?php
$session = new Session();

$msg = "";

if(isset($_POST['user_name'])) {
	$user_name = $_POST['user_name'];
	$mysqli = connect_db();
	
	$is_valid = $session->is_valid_user_name($user_name, $mysqli);
	if(!$is_valid) {
		$msg = "Invalid user name!";
	} else {
		if(isset($_POST['user_pw']))
			$user_pw = $_POST['user_pw'];
		else
			$user_pw = "";
		$is_login = $session->login($user_name, $user_pw, $mysqli);
		if(!$is_login)
			$msg = "Invalid password!";
		
	}
	$mysqli->close();
}
?>
<!DOCTYPE HTML>
<html>
	<head>
		<title>Release 관리 신청 (인공지능ver)</title>
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
		<meta http-equiv="Content-Type" content="text/html" charset="utf-8">
		<meta name="viewport" content="width=device-width">
		<script src="//code.jquery.com/jquery-1.9.1.min.js"></script>
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap.min.css">
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/js/bootstrap.min.js"></script>
		
		<link rel="stylesheet" href="assets/css/jquery.fullPage.css">
		<script src="assets/js/jquery.fullPage.min.js"></script>
		
		<link rel="stylesheet" href="assets/css/common.css">
		<script src="assets/js/common.js"></script>
		<link rel="stylesheet" href="assets/css/index.css">
		
		<script type="text/javascript">
			function select(mng_id) {
				alert(mng_id);
			}
			function req_logout() {
				$.get('logout.php', function() {
					location.reload();
				});
			}
		</script>
	</head>
	<body>
		<div class='container'>
<?php
if($session->is_login) {
?>
			<h1>You are Logined!</h1>
			<button onclick="req_logout();">Logout</button>
<?php
}
else {
?>
			<h1><?php echo $msg ?></h1>
			<form id='login_form' class="form-inline" role="form" method='post' action="<?php echo $_SERVER['PHP_SELF']; ?>">
				<input type='hidden' name='type' value='1'>
				<div class="form-group">
					<label class="sr-only" for="user_name">User Name</label>
					<input type="text" name='user_name' class="form-control" placeholder=" 학번+이름 ex) 14권태국">
				</div>
				<button type="submit" class="btn btn-default">
					Sign in
				</button>
			</form>
<?php
}
?>
		</div>
	</body>
</html>