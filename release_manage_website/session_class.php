<?php
require_once 'db_config.php';
require_once 'security_functions.php';
require_once 'user_class.php';

class Session {
	var $session_name = 'release_mng_session';
	var $is_login = false;
	var $login_user = null;
	var $login_string = "";
	
	var $stages = array("login","stage1","stage2","stage3","stage4","thankyou");
	var $now_stage = "login";
	
	function __construct() {
		$this->sec_session_start();
		$this->_load_val_from_session();
	}
	
	function sec_session_start() {
		$session_name = $this->session_name; // Set a custom session name
		$secure = SECURE;
		// This stops JavaScript being able to access the session id.
		$httponly = true;
		// Forces sessions to only use cookies.
		if (ini_set ( 'session.use_only_cookies', 1 ) === FALSE) {
			return false;
		}
		// Gets current cookies params.
		$cookieParams = session_get_cookie_params ();
		session_set_cookie_params ( $cookieParams ["lifetime"], $cookieParams ["path"], $cookieParams ["domain"], $secure, $httponly );
		// Sets the session name to the one set above.
		session_name ( $session_name );
		session_start (); // Start the PHP session
		//session_regenerate_id ( true ); // regenerated the session, delete the old one.
		return true;
	}
	
	function _load_val_from_session() {
		if(isset($_SESSION ['is_login']))
			$this->is_login = $_SESSION['is_login'];
		else
			$this->is_login = false;
		
		if(isset($_SESSION ['login_user']))
			$this->login_user = unserialize ( $_SESSION ['login_user'] );
		else
			$this->login_user = null;
		
		if(isset($_SESSION ['login_string']))
			$this->login_string = $_SESSION['login_string'];
		else
			$this->login_string = "";
		
		if(isset($_SESSION ['now_stage']))
			$this->now_stage = $_SESSION['now_stage'];
		else
			$this->now_stage = "login";
	}
	
	function _store_val_to_session() {
		$_SESSION['is_login'] = $this->is_login;
		$_SESSION['login_user'] = serialize($this->login_user);
		$_SESSION['login_string'] = $this->login_string;
		$_SESSION['now_stage'] = $this->now_stage;
	}
	
	function is_valid_user_name($user_name, $mysqli) {
		if ($stmt = $mysqli->prepare ( "SELECT 1 FROM user_list
		WHERE user_name = ? 
		LIMIT 1" )) {
		 	$stmt->bind_param ( 's', $user_name );
		    $stmt->execute (); // Execute the prepared query.
		    $stmt->store_result ();
		        	
		    // get variables from result.
		    $stmt->bind_result ( $junk );
		    $stmt->fetch ();

		    if ($stmt->num_rows < 1)
		        return false;
		    else
		    	return true;
		} else return false;
	}
	
	function is_exist_pw($user_name, $mysqli) {
		if ($stmt = $mysqli->prepare ( "SELECT is_exist_pw FROM user_list
		WHERE user_name = ? 
		LIMIT 1" )) {
		 	$stmt->bind_param ( 's', $user_name );
		    $stmt->execute (); // Execute the prepared query.
		    $stmt->store_result ();
		        	
		    // get variables from result.
		    $stmt->bind_result ( $is_exist_pw );
		    $stmt->fetch ();

		    if ($stmt->num_rows < 1 || !$is_exist_pw)
		        return false;
		    else
		    	return true;
		} else return false;
	}
	
	function login($user_name, $user_pw, $mysqli) {
		$login_user = new User ();
		// Get the user-agent string of the user.
		$user_browser = $_SERVER ['HTTP_USER_AGENT'];
						
		// Using prepared statements means that SQL injection is not possible.
		if ($stmt = $mysqli->prepare ( "SELECT ul_id, user_name, user_pw, pw_salt,
		is_exist_pw, stage
        FROM user_list
		WHERE user_name = ?
        LIMIT 1" )) {
			$stmt->bind_param ( 's', $user_name );
			$stmt->execute (); // Execute the prepared query.
			$stmt->store_result ();
			
			// get variables from result.
			$stmt->bind_result ( $login_user->ul_id, $login_user->user_name, 
					$db_user_pw, $db_pw_salt, $login_user->is_exist_pw, $login_user->stage );
			$stmt->fetch ();
			
			if ($stmt->num_rows < 1)
				return false;
			
			if(!$login_user->is_exist_pw) {
				$login_user->_init_val_with_xss_clean ();
				$this->is_login = true;
				$this->login_user = $login_user;
				$this->login_string = hash ( 'sha512', $user_pw . $user_browser );					
				$this->_store_val_to_session();
				return true;
			}
			
			if(empty($user_pw))
				return false;

			// hash the password with the unique salt.
			$user_pw = hash ( 'sha512', $user_pw . $db_pw_salt );
			if ($stmt->num_rows == 1) {
				// If the user exists we check if the account is locked
				// from too many login attempts
				if(false) {
				//if ($this->checkbrute ( $login_user->ul_id, $mysqli ) == true) {
					// Account is locked
					// Send an email to user saying their account is locked
					return false;
				} else {
					// Check if the password in the database matches
					// the password the user submitted.
					if ($db_user_pw == $user_pw) {
						// Password is correct!

						$login_user->_init_val_with_xss_clean ();
						$this->is_login = true;
						$this->login_user = $login_user;
						$this->login_string = hash ( 'sha512', $user_pw . $user_browser );					
						$this->_store_val_to_session();
						
						// Login successful.
						return true;
					} else {
						// Password is not correct
						// We record this attempt in the database
						//$now = time ();
						//$mysqli->query ( "INSERT INTO login_attempts(ul_id, time)
	        			//		VALUES ('$this->login_user->user_id', '$now')" );
						return false;
					}
				}
			} else {
				// No user exists.
				return false;
			}
		}
	}
	function checkbrute($ul_id, $mysqli) {
		// Get timestamp of current time
		$now = time ();
		
		// All login attempts are counted from the past 2 hours.
		$valid_attempts = $now - (2 * 60 * 60);
		
		if ($stmt = $mysqli->prepare ( "SELECT time
				FROM login_attempts
				WHERE user_id = ?
				AND time > '$valid_attempts'" )) {
			$stmt->bind_param ( 'i', $ul_id );
			
			// Execute the prepared query.
			$stmt->execute ();
			$stmt->store_result ();
			
			// If there have been more than 5 failed logins
			if ($stmt->num_rows > 5) {
				return true;
			} else {
				return false;
			}
		}
		return false;
	}
	function login_check($mysqli) {
		// Check if all session variables are set
		if(!empty($this->login_user) && $this->login_user->is_exist_pw)
			return true;
			
		if (!empty($this->login_user) && !empty($this->login_string)) {
			
			$login_user = $this->login_user;
			$login_string = $this->login_string;
			
			// Get the user-agent string of the user.
			$user_browser = $_SERVER ['HTTP_USER_AGENT'];
			
			if ($stmt = $mysqli->prepare ( "SELECT user_pw
                                      FROM user_list
                                      WHERE ul_id = ? LIMIT 1" )) {
				// Bind "$user_id" to parameter.
				$stmt->bind_param ( 'i', $login_user->ul_id );
				$stmt->execute (); // Execute the prepared query.
				$stmt->store_result ();
				
				if ($stmt->num_rows == 1) {
					// If the user exists get variables from result.
					$stmt->bind_result ( $db_user_pw );
					$stmt->fetch ();
					$login_check = hash ( 'sha512', $db_user_pw . $user_browser );
					
					if ($login_check == $login_string) {
						// Logged In!!!!
						return true;
					} else {
						// Not logged in
						return false;
					}
				} else {
					// Not logged in
					return false;
				}
			} else {
				// Not logged in
				return false;
			}
		} else {
			// Not logged in
			return false;
		}
	}
	function logout() {
		// Unset all session values
		$_SESSION = array();
		
		// get session parameters
		$params = session_get_cookie_params();
		
		// Delete the actual cookie.
		setcookie(session_name(),
				'', time() - 42000,
				$params["path"],
				$params["domain"],
				$params["secure"],
				$params["httponly"]);
		
		// Destroy session
		session_destroy();
	}
	
	function upgrade_stage($stage) {
		if( array_search($this->now_stage, $this->stages) < array_search($stage, $this->stages) )
			$_SESSION['now_stage'] = $this->now_stage = $stage;
	}
	
	function set_user_pw($mysqli, $user_pw) {
		$ul_id = $this->login_user->ul_id;
		$random_salt = hash('sha512', uniqid(mt_rand(1, mt_getrandmax()), true));
		$user_pw = hash('sha512', $user_pw . $random_salt);
		$is_exist_pw = 1;
		if ($stmt = $mysqli->prepare ( "UPDATE user_list SET user_pw = ? , pw_salt = ? , is_exist_pw = ? WHERE ul_id = ? LIMIT 1" )) {
			$stmt->bind_param ( 'ssdd', $user_pw , $random_salt , $is_exist_pw , $ul_id );
			if( ! $stmt->execute () )
				return false;
		} else return false;
		$this->login_user->is_exist_pw = 1;
		$_SESSION['login_user'] = serialize($this->login_user);
		return true;
	}
}

?>