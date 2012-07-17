<?php
namespace Coxis\Core;

class Cookie {
	public static function exist($what) {
		return isset($_COOKIE[$what]);
	}
	
	public static function get($what) {
		return $_COOKIE[$what];
	}
	
	public static function set($what, $value, $time=null, $path='/') {
		if($time===null)
			$time = time()+3600*24*365;
		setcookie($what, $value, $time, $path);
	}
	
	public static function delete($what, $path='/') {
		setcookie($what, false, -10000, $path);
	}
}