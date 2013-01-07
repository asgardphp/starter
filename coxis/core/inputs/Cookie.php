<?php
namespace Coxis\Core\Inputs;

abstract class COOKIE extends InputsBag {
	// function __construct() {
	// 	$this->inputs = $_COOKIE;
	// }
	
	public static function set($what, $value, $time=null, $path='/') {
		if($time===null)
			$time = time()+3600*24*365;
		setcookie($what, $value, $time, $path);
		parent::set($what, $value);
		// return $this;
	}
	
	public static function remove($what, $path='/') {
		setcookie($what, false, -10000, $path);
		parent::remove($what);
		// return $this;
	}
}