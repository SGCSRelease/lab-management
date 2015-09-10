<?php
require_once 'session_class.php';
?>
<?php
$session = new Session();
$now_stage = $session->now_stage;
$stages = $session->stages;
$now_stage_idx = array_search($now_stage, $stages);
?>
<!DOCTYPE HTML>
<html>
	<head>
		<title>Release 관리 신청 (인공지능ver)</title>
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
		<meta http-equiv="Content-Type" content="text/html" charset="utf-8">
		<meta name="viewport" content="width=device-width, user-scalable=no">
		<script src="//code.jquery.com/jquery-1.9.1.min.js"></script>
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap.min.css">
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/js/bootstrap.min.js"></script>
		
		<link rel="stylesheet" href="assets/css/jquery.fullPage.css">
		<script src="assets/js/jquery.fullPage.min.js"></script>
		
		<link rel="stylesheet" href="assets/css/common.css">
		<script src="assets/js/common.js"></script>
		<script src="assets/js/sha512.js"></script>
		<link rel="stylesheet" href="assets/css/index.css">
		
		<script type="text/javascript">
			var is_login_load = false;
			var is_stage1_load = false;
			var is_stage2_load = false;
			var is_stage3_load = false;
			var is_stage4_load = false;
			var is_thankyou_load = false;
			var now_stage_idx = 0;
			
			function build_fullpage() {
				$('#fullpage').fullpage({
					sectionsColor: ['#1bbc9b', '#4BBFC3', '#7BAABE', '#ccddff', "#f0e68c", "#ffd700"],
					anchors: ['p0','p1','p2','p3','p4','p5'],
					menu: '#menu',
					scrollingSpeed: 600,
					scrollOverflow: true,	
					
					afterLoad: function(anchorLink, index){
						$.fn.fullpage.moveTo(now_stage_idx+1, 0);
					},
					afterResize: function() {	
					//	$.fn.fullpage.moveTo(now_stage_idx+1, 0);
					}
				});
			}
			
			function set_now_stage(stage) {
				$.get("set_now_stage.php", {stage: stage}, function(data) {
					console.log(data);
				});
			}
			
			function reload_login(data) {
				$('#login').empty();
				$('.section[data-anchor=p0]').show();
				$('#login').append(data);
				after_load_login();
				//build_fullpage();
			}
			
			function req_login() {
				var type = $('#login_form input[name="type"]').val().trim();
				var user_name = $('#login_form input[name="user_name"]').val().trim();
				if(type == '1') {
					$.post("login.php", {user_name: user_name}, reload_login);
				} else if(type == '2') {
					var user_pw = hex_sha512($('#login_form input[name="user_pw"]').val().trim());
					$.post("login.php", {user_name: user_name, user_pw: user_pw}, reload_login);
				}
			}
			function req_logout() {
				$.get('logout.php', function() {
					location.reload();
				});
			}
			
			function load_login() {
				if(is_login_load) {
					$.fn.fullpage.moveTo(1, 0);
					return;
				}
				$('#login').empty();
				$('.section[data-anchor=p0]').show();
				$('#login').load('login.php', function() {
					console.log('login load');
					after_load_login();
					$.fn.fullpage.moveTo(1, 0);
					is_login_load = true;
					now_stage_idx = 0;
				});
			}
			
			function load_stage1() {
				if(is_stage1_load) {
					$.fn.fullpage.moveTo(2, 0);
					return;
				}
				$('#stage1').empty();
				$('.section[data-anchor=p1]').show();
				$('#stage1').load('stage1.php', function() {
					console.log('stage1 load');
					after_load_stage1();
					$.fn.fullpage.moveTo(2, 0);
					is_stage1_load = true;
					if(now_stage_idx<1)now_stage_idx = 1;
				});
			}
			function unload_stage1() {
				if(!is_stage1_load) return;
				$.fn.fullpage.moveTo(1, 0);
				$('#stage1').html("Not Loading...");
				$('.section[data-anchor=p1]').hide();
				$('#stage2').html("Not Loading...");
				$('.section[data-anchor=p2]').hide();
				$('#stage3').html("Not Loading...");
				$('.section[data-anchor=p3]').hide();
				$('#stage4').html("Not Loading...");
				$('.section[data-anchor=p4]').hide();
				$('#thankyou').html("Not Loading...");
				$('.section[data-anchor=p5]').hide();
				is_stage1_load = false;
				is_stage2_load = false;
				is_stage3_load = false;
				is_stage4_load = false;
				is_thankyou_load = false;
				now_stage_idx = 0;
				set_now_stage("login");
			}
			
			function load_stage2() {
				if(is_stage2_load) {
					$.fn.fullpage.moveTo(3, 0);
					return;
				}
				$('#stage2').empty();
				$('.section[data-anchor=p2]').show();
				$('#stage2').load('stage2.php', function() {
					console.log('stage2 load');
					after_load_stage2();
					$.fn.fullpage.moveTo(3, 0);
					is_stage2_load = true;
					if(now_stage_idx<2)now_stage_idx = 2;
				});
			}
			function unload_stage2() {
				if(!is_stage2_load) return;
				$.fn.fullpage.moveTo(2, 0);
				$('#stage2').html("Not Loading...");
				$('.section[data-anchor=p2]').hide();
				$('#stage3').html("Not Loading...");
				$('.section[data-anchor=p3]').hide();
				$('#stage4').html("Not Loading...");
				$('.section[data-anchor=p4]').hide();
				$('#thankyou').html("Not Loading...");
				$('.section[data-anchor=p5]').hide();
				is_stage2_load = false;
				is_stage3_load = false;
				is_stage4_load = false;
				is_thankyou_load = false;
				now_stage_idx = 1;
				set_now_stage("stage1");
			}
			
			function load_stage3() {
				if(is_stage3_load) {
					$.fn.fullpage.moveTo(4, 0);
					return;
				}
				$('#stage3').empty();
				$('.section[data-anchor=p3]').show();
				$('#stage3').load('stage3.php', function() {
					console.log('stage3 load');
					after_load_stage3();
					$.fn.fullpage.moveTo(4, 0);
					is_stage3_load = true;
					if(now_stage_idx<3)now_stage_idx = 3;
				});
			}
			function unload_stage3() {
				if(!is_stage3_load) return;
				$.fn.fullpage.moveTo(3, 0);
				$('#stage3').html("Not Loading...");
				$('#stage4').html("Not Loading...");
				$('#thankyou').html("Not Loading...");
				$('.section[data-anchor=p3]').hide();
				$('.section[data-anchor=p4]').hide();
				$('.section[data-anchor=p5]').hide();
				is_stage3_load = false;
				is_stage4_load = false;
				is_thankyou_load = false;
				now_stage_idx = 2;
				set_now_stage("stage2");
			}
			
			function load_stage4() {
				if(is_stage4_load) {
					$.fn.fullpage.moveTo(5, 0);
					return;
				}
				$('#stage4').empty();
				$('.section[data-anchor=p4]').show();
				$('#stage4').load('stage4.php', function() {
					console.log('stage4 load');
					after_load_stage4();
					$.fn.fullpage.moveTo(5, 0);
					is_stage4_load = true;
					if(now_stage_idx<4)now_stage_idx = 4;
				});
			}
			
			function unload_stage4() {
				if(!is_stage4_load) return;
				$.fn.fullpage.moveTo(4, 0);
				$('#stage4').html("Not Loading...");
				$('#thankyou').html("Not Loading...");
				$('.section[data-anchor=p4]').hide();
				$('.section[data-anchor=p5]').hide();
				is_stage4_load = false;
				is_thankyou_load = false;
				now_stage_idx = 3;
				set_now_stage("stage3");
				$('.section[data-anchor=p4]').hide();
			}
			
			function load_thankyou() {
				if(is_thankyou_load) {
					$.fn.fullpage.moveTo(6, 0);
					return;
				}
				$('#thankyou').empty();
				$('.section[data-anchor=p5]').show();
				$('#thankyou').load('thankyou.php', function() {
					console.log('thankyou load');
					after_load_thankyou();
					$.fn.fullpage.moveTo(6, 0);
					is_thankyou_load = true;
					if(now_stage_idx<5)now_stage_idx = 5;
				});
			}
			
			$(document).ready(function() {
				//$('#apply_stage').append("<div class='slide' id='start'><p>Hello</p></div>");
				$('#fullpage').append("<div class='section'><div id='login'>Not Loading...</div></div></div>");
				$('#fullpage').append("<div class='section'><div id='stage1'>Not Loading...</div></div>");
				$('#fullpage').append("<div class='section'><div id='stage2'>Not Loading...</div></div>");
				$('#fullpage').append("<div class='section'><div id='stage3'>Not Loading...</div></div>");
				$('#fullpage').append("<div class='section'><div id='stage4'>Not Loading...</div></div>");
				$('#fullpage').append("<div class='section'><div id='thankyou'>Not Loading...</div></div>");

				build_fullpage();
				$('.section[data-anchor=p0]').hide();
				$('.section[data-anchor=p1]').hide();
				$('.section[data-anchor=p2]').hide();
				$('.section[data-anchor=p3]').hide();
				$('.section[data-anchor=p4]').hide();
				$('.section[data-anchor=p5]').hide();

				<?php
					if(array_search("login", $stages) <= $now_stage_idx) {
						echo "console.log('now_stage_idx = $now_stage_idx');";
						echo "load_login();";
					}
					if(array_search("stage1", $stages) <= $now_stage_idx) {
						echo "load_stage1();";
					}
					if(array_search("stage2", $stages) <= $now_stage_idx) {
						echo "load_stage2();";
					}
					if(array_search("stage3", $stages) <= $now_stage_idx) {
						echo "load_stage3();";
					}
					if(array_search("stage4", $stages) <= $now_stage_idx) {
						echo "load_stage4();";
					}
					if(array_search("thankyou", $stages) <= $now_stage_idx) {
						echo "load_thankyou();";
					}
				?>
				<?php
					if(array_search("login", $stages) == $now_stage_idx) {
						echo "$.fn.fullpage.moveTo(1, 0);";
						echo "now_stage_idx = 0;";
					}
					if(array_search("stage1", $stages) == $now_stage_idx) {
						echo "$.fn.fullpage.moveTo(2, 0);";
						echo "now_stage_idx = 1;";
					}
					if(array_search("stage2", $stages) == $now_stage_idx) {
						echo "$.fn.fullpage.moveTo(3, 0);";
						echo "now_stage_idx = 2;";
					}
					if(array_search("stage3", $stages) == $now_stage_idx) {
						echo "$.fn.fullpage.moveTo(4, 0);";
						echo "now_stage_idx = 3;";
					}
					if(array_search("stage4", $stages) == $now_stage_idx) {
						echo "$.fn.fullpage.moveTo(5, 0);";
						echo "now_stage_idx = 4;";
					}
					if(array_search("thankyou", $stages) == $now_stage_idx) {
						echo "$.fn.fullpage.moveTo(6, 0);";
						echo "now_stage_idx = 5;";
					}
				?>
			});
		</script>
	</head>
	<body>
		
		<div id='fullpage'>
		</div>
		<!-- Modal -->
		<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		  <div class="modal-dialog modal-lg">
			<div class="modal-content">
			  <div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h1 class="modal-title" id="myModalLabel">우린 팀이 아니야.<br> This is a competition.</h1>
			  </div>
			  <div class="modal-body" id='competiton_table'>
				
			  </div>
			  <div class="modal-footer">
				<button type="button" class="btn btn-primary my_input" data-dismiss="modal">Close</button>
			  </div>
			</div>
		  </div>
		</div>
		
		<script type="text/javascript">	
		</script>
	</body>
</html>
