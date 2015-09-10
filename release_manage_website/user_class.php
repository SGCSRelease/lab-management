<?php
require_once 'security_functions.php';

class User {
	var $ul_id;
	var $user_name;
	var $user_name_print;
	var $is_exist_pw;
	var $stage;
	
	function _init_val_with_xss_clean() {
		$this->user_name_print = htmlentities($this->user_name, ENT_QUOTES, 'UTF-8');
	}
}
?>