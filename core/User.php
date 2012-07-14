<?php
class User {
	private static $id = null;
	private static $role = null;
	  
	public static function start() {
		if(!headers_sent()) {
			if(isset($_GET['PHPSESSID']))
				session_id($_GET['PHPSESSID']);
			elseif(isset($_POST['PHPSESSID']))
				session_id($_POST['PHPSESSID']);
			session_start();
		}
		if(isset($_SESSION['id']) && is_numeric($_SESSION['id']))
			static::$id = $_SESSION['id'];
		if(isset($_SESSION['role']))
			static::$role = $_SESSION['role'];
	}
	  
	public static function getId() {
		return static::$id;
	}
	  
	public static function setId($id) {
		$_SESSION['id'] = $id;
		static::$id = $id;
	}
	  
	public static function getRole() {
		return static::$role;
	}
	  
	public static function setRole($role) {
		$_SESSION['role'] = $role;
		static::$role = $role;
	}
	
	public static function logout() {
		unset($_SESSION['id']);
		static::$id = null;
		unset($_SESSION['role']);
		static::$role = null;
	}
}