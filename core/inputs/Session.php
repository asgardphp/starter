<?php
namespace Coxis\Core\Inputs;

abstract class Session extends InputsBag {
	public static function set($name, $value) {
		if(isset($_SESSION))
			$_SESSION[$name] = $value;
		return parent::set($name, $value);
	}
	  
	public static function remove($name) {
		if(isset($_SESSION))
			unset($_SESSION[$name]);
		return parent::remove($name);
	}
}