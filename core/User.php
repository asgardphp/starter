<?php
namespace Coxis\Core;

class User {
	private $data = array();
	  
	function __construct() {
		$this->start();
	}

	public function start() {
		if(!headers_sent()) {
			if(isset($_GET['PHPSESSID']))
				session_id($_GET['PHPSESSID']);
			elseif(isset($_POST['PHPSESSID']))
				session_id($_POST['PHPSESSID']);
			session_start();
		}
		if(isset($_SESSION))
			$this->data = $_SESSION;
	}
	
	public function delete($name) {
		if(isset($_SESSION))
			unset($_SESSION[$name]);
		unset($this->data[$name]);
	}
	
	public function get($name) {
		if(isset($this->data[$name]))
			return $this->data[$name];
		return null;
	}
	  
	public function set($name, $value) {
		$this->data[$name] = $value;
		if(isset($_SESSION))
			$_SESSION[$name] = $value;
	}
}