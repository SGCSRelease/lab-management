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

$session->upgrade_stage("stage2");
?>
<h1>Step 2. 관리 가능한 시간중 선호 지망 순위를 매겨주세요.</h1><br>
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
	$class .= "yes_hover ";
	if(!empty($mng->prefer_order) && intval($mng->prefer_order) >= 1) $content = $mng->prefer_order . "지망";
	else $content = "";
	echo "<td id='mng2_$mng->mng_id' class='${class}'>$content</td>";
	if($i % $mngManager->MNG_DAY_NUM == $mngManager->MNG_DAY_NUM-1) echo "</tr>";
}
echo "</tbody>";
echo "</table>";
echo "</div>";
?>
<button onClick='unload_stage2();'>Back Step</button>
<button data-toggle="modal" data-target="#myModal" onClick='load_competition_table();'>See Competition</button>
<button onClick='submit_po();'>Next Step</button>
<script type='text/javascript'>
	var hmap_mng_id_po = new JqMap();	// mng_id -> priority order
	var hmap_po_mng_id = new JqMap();	// priority order -> mng_id
	var wait_po;

	function load_competition_table() {
		$("#competiton_table").empty();
		$("#competiton_table").load("competition_table.php", function(data) {
			console.log("load competiton table ("+data+")");
		});
	}

	function submit_po() {
		var json_mng_id = JSON.stringify(hmap_mng_id_po.keys());
		var json_po= JSON.stringify(hmap_mng_id_po.values());
		$.post("process_mng_po.php", {json_mng_id: json_mng_id, json_po: json_po}, function(data) {
			console.log(data);
			if(data=="fail") {
				alert("fail to query database!");
				location.reload();
			} else {
				load_stage3();
			}
		});
	}
	
	function update_mng_po() {
		var json_mng_id = JSON.stringify(hmap_mng_id_po.keys());
		var json_po= JSON.stringify(hmap_mng_id_po.values());
		$.post("process_mng_po.php", {json_mng_id: json_mng_id, json_po: json_po}, function(data) {
			console.log(data);
			if(data=="fail") {
				alert("fail to query database!");
				location.reload();
			}
		});
	}

	function insert_hmap(mng_id) {
		hmap_mng_id_po.put(mng_id, wait_po);
		hmap_po_mng_id.put(wait_po, mng_id);
		$("#mng2_"+mng_id).html(wait_po + "지망");
		$("#mng2_"+mng_id).removeClass('is_can_mng');
		$("#mng2_"+mng_id).addClass('is_prefer_mng');
		wait_po++;
	}
	function delete_hmap(mng_id) {
		var d_po = Number(hmap_mng_id_po.get(mng_id));
		var new_hmap_mng_id_po = new JqMap();
		var new_hmap_po_mng_id = new JqMap();
		hmap_mng_id_po.remove(mng_id);
		hmap_po_mng_id.remove(d_po);
		$("#mng2_"+mng_id).empty();
		$("#mng2_"+mng_id).removeClass('is_prefer_mng');
		$("#mng2_"+mng_id).addClass('is_can_mng');
		var po_s = hmap_po_mng_id.keys();
		for(var i in po_s) {
			var po = Number(po_s[i]);
			var b_mng_id = hmap_po_mng_id.get(po);
			$("#mng2_"+b_mng_id).empty();
			if(d_po < po) {
				new_hmap_po_mng_id.put(po-1, b_mng_id);
				new_hmap_mng_id_po.put(b_mng_id, po-1);
			} else {
				new_hmap_po_mng_id.put(po, b_mng_id);
				new_hmap_mng_id_po.put(b_mng_id, po);
			}
		}
		hmap_mng_id_po = $.extend(true,{}, new_hmap_mng_id_po);
		hmap_po_mng_id = $.extend(true,{}, new_hmap_po_mng_id);
		var n_mng_id_s = hmap_mng_id_po.keys();
		for(var i in n_mng_id_s) {
			var n_mng_id = n_mng_id_s[i];
			$("#mng2_"+n_mng_id).html(hmap_mng_id_po.get(n_mng_id) + "지망");
		}
		wait_po--;
	}
	
	function mng2_click(mng_id) {
		if(now_stage_idx > 2)
			return;
		if($("#mng2_"+mng_id).hasClass("is_cannot_mng"))
			return;
		if(!hmap_mng_id_po.containsKey(mng_id)) {
			insert_hmap(mng_id);
		} else {
			delete_hmap(mng_id);
		}
		update_mng_po();
	}
	function after_load_stage2() {
		console.log('after_load_stage2 call!!');
		<?php
		$max_po = 0;
		for ($i=0; $i < $mngManager->mng_num; $i++) {
			$mng = $mngManager->mngs[$i]; 
			$mng_id = $mng->mng_id;
			echo "$('#mng2_$mng_id').click(function(){mng2_click($mng_id)});";
			if(!empty($mng->prefer_order) && intval($mng->prefer_order) >= 1) {
			/*	echo("
					$('#mng2_$mng_id').removeClass('is_can_mng');
					$('#mng2_$mng_id').addClass('is_prefer_mng');
					");*/
				echo "hmap_po_mng_id.put($mng->prefer_order,$mng_id);";
				echo "hmap_mng_id_po.put($mng_id,$mng->prefer_order);";
				if($max_po < $mng->prefer_order)
					$max_po = $mng->prefer_order;
			}
		}
		echo "wait_po = $max_po + 1;";
		?>
	}
</script>
