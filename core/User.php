<?php
namespace Coxis\Core;

class User {
	private static $data = array();
	  
	public static function start() {
		if(!headers_sent()) {
			if(isset($_GET['PHPSESSID']))
				session_id($_GET['PHPSESSID']);
			elseif(isset($_POST['PHPSESSID']))
				session_id($_POST['PHPSESSID']);
			session_start();
		}
		static::$data = $_SESSION;
	}
	
	public static function delete($name) {
		unset($_SESSION[$name]);
		unset(static::$data[$name]);
	}
	
	public static function get($name) {
		if(isset(static::$data[$name]))
			return static::$data[$name];
		return null;
	}
	  
	public static function set($name, $value) {
		static::$data[$name] = $value;
		$_SESSION[$name] = $value;
	}
}