<?php
require_once 'db_config.php';
require_once 'user_class.php';

class Mng {
	var $mng_id;
	var $mng_day;
	var $mng_period;
	var $sel_cnt;
	var $is_can_mng;
	var $prefer_order;
}

class MngManager {
	var $MNG_DAY_NUM = 5;
	var $MNG_PERIOD_NUM = 6;
	var $MNG_DAY_NAMES = array('MON', 'TUE', 'WED', 'THU', 'FRI');
	var $MNG_PERIOD_NAMES = array('2교시', '3교시', '4교시', '5교시', '6교시', '저녁관리');
	
	var $login_user;
	var $mngs;
	var $mng_num;
	
	function __construct($login_user) {
		$this->login_user = $login_user;
	}
	
	function load_mng_list($mysqli) { // not finish
		if ($result = $mysqli->query ( "SELECT mng_id, mng_day, mng_period FROM mng_list ORDER BY mng_period ASC, mng_day ASC" )) {
	        $this->mngs = array();
			$this->mng_num = $result->num_rows;

			for ($i=0; $i < $result->num_rows; $i++) {
				$row = $result->fetch_assoc();

				$this->mngs[$i] = new Mng();
				$this->mngs[$i]->mng_id = $row['mng_id'];
				$this->mngs[$i]->mng_day = $row['mng_day'];
				$this->mngs[$i]->mng_period = $row['mng_period'];
				
				if($stmt = $mysqli->prepare( "SELECT count(*) as sel_cnt FROM apply_list WHERE mng_id = ? and is_can_mng = 1 and prefer_order is not NULL" )) {
					$stmt->bind_param( 'd', $this->mngs[$i]->mng_id);
					$stmt->execute();
					$stmt->store_result();
					$stmt->bind_result( $this->mngs[$i]->sel_cnt );
					$stmt->fetch();
					$stmt->close();
				} else $this->mngs[$i]->sel_cnt=0;
				if($stmt = $mysqli->prepare( "SELECT is_can_mng, prefer_order FROM apply_list WHERE ul_id = ? and mng_id = ? LIMIT 1" )) {
					$stmt->bind_param( 'dd', $this->login_user->ul_id, $this->mngs[$i]->mng_id);
					//print_r($this->login_user->ul_id . "ff" . $this->mngs[$i]->mng_id . "\n");
					$stmt->execute();
					$stmt->store_result();
					if($stmt->num_rows < 1) {
						$this->mngs[$i]->is_can_mng = 1;
					} else {
						$stmt->bind_result( $this->mngs[$i]->is_can_mng, $this->mngs[$i]->prefer_order );
						$stmt->fetch();
						$stmt->close();
					}
				} else $this->mngs[$i]->is_can_mng=1;
			}
			$result->close();
			return true;
		} else {
			return false;
		}
	}

	function change_is_can_mng($mysqli, $mng_id) {
		$ul_id = $this->login_user->ul_id;
		if ($stmt = $mysqli->prepare ( "SELECT is_can_mng, prefer_order FROM apply_list WHERE ul_id = ? and mng_id = ? LIMIT 1" )) {
			$stmt->bind_param( 'dd', $ul_id, $mng_id);
			$stmt->execute();
			$stmt->store_result();
			if($stmt->num_rows < 1) {
				if ($stmt2 = $mysqli->prepare ( "INSERT INTO apply_list (ul_id, mng_id, is_can_mng) VALUES ( ? , ? , ? )" )) {
					$tmp = 0;
					$stmt2->bind_param( 'ddd', $ul_id, $mng_id, $tmp);
					$stmt2->execute();
					$stmt2->close();
					$stmt->close();
					return true;
				} else return false;
			} else {
				$stmt->bind_result( $old_is_can_mng , $prefer_order );
				$stmt->fetch();
				$new_is_can_mng = (intval($old_is_can_mng) + 1) % 2;
				if(!$new_is_can_mng) {
					if ($stmt2 = $mysqli->prepare ( "UPDATE apply_list SET prefer_order=null WHERE ul_id = ? and mng_id = ?" )) {
						$stmt2->bind_param( 'dd', $ul_id, $mng_id);
						$stmt2->execute();
						$stmt2->close();
					} else return false;
					if ($stmt2 = $mysqli->prepare ( "UPDATE apply_list SET prefer_order=prefer_order-1 WHERE ul_id = ? and prefer_order > ?" )) {
						$stmt2->bind_param( 'dd', $ul_id, $prefer_order);
						$stmt2->execute();
						$stmt2->close();
					} else return false;
				}
				if ($stmt2 = $mysqli->prepare ( "UPDATE apply_list SET is_can_mng = ? WHERE ul_id = ? and mng_id = ?" )) {
					$stmt2->bind_param( 'ddd', $new_is_can_mng, $ul_id, $mng_id);
					$stmt2->execute();
					$stmt2->close();
					$stmt->close();
					return true;
				} else return false;
			}
		} else return false;
	}

	function update_mng_po($mysqli, $mng_id_po_arr) {
		$ul_id = $this->login_user->ul_id;
		foreach ($mng_id_po_arr as $mng_id => $po) {
			if ($stmt = $mysqli->prepare ( "SELECT is_can_mng FROM apply_list WHERE ul_id = ? and mng_id = ? LIMIT 1" )) {
				$stmt->bind_param( 'dd', $ul_id, $mng_id);
				$stmt->execute();
				$stmt->store_result();
				if($stmt->num_rows == 1) {
					$stmt->bind_result( $is_can_mng );
					$stmt->fetch();
					if(!$is_can_mng)
						return false;
				}
				$stmt->close();
			} else return false;
		}
		if ($stmt = $mysqli->prepare ( "DELETE FROM apply_list WHERE ul_id = ? and prefer_order is not NULL" )) {
			$stmt->bind_param( 'd', $ul_id);
			$stmt->execute();
			$stmt->close();
		} else return false;
		foreach ($mng_id_po_arr as $mng_id => $po) {
			if ($stmt = $mysqli->prepare ( "SELECT 1 FROM apply_list WHERE ul_id = ? and mng_id = ? LIMIT 1" )) {
				$stmt->bind_param( 'dd', $ul_id, $mng_id);
				$stmt->execute();
				$stmt->store_result();
				if($stmt->num_rows < 1) {
					if ($stmt2 = $mysqli->prepare ( "INSERT INTO apply_list (ul_id, mng_id, is_can_mng, prefer_order) VALUES ( ? , ? , ? , ? )" )) {
						$tmp = 1;
						$stmt2->bind_param( 'dddd', $ul_id, $mng_id, $tmp, $po);
						$stmt2->execute();
						$stmt2->close();
					} else return false;
				} else {
					if ($stmt2 = $mysqli->prepare ( "UPDATE apply_list SET prefer_order = ? WHERE ul_id = ? and mng_id = ? LIMIT 1" )) {
						$stmt2->bind_param( 'ddd', $po, $ul_id, $mng_id);
						$stmt2->execute();
						$stmt2->close();
					} else return false;
				}
			} else return false;
			$stmt->close();
		}
		return true;
	}
}

?>
