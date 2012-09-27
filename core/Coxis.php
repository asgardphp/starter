<?php
namespace Coxis\Core;

class Coxis {
	private static $arrs = array();
	
	public static function set() {
		$args = func_get_args();
		$arr =& static::$arrs;
		$key = $args[sizeof($args)-2];
		$value = $args[sizeof($args)-1];
		array_pop($args);
		array_pop($args);
		
		foreach($args as $parent)
			$arr =& $arr[$parent];
		$arr[$key] = $value;
	}
	
	public static function get() {
		//todo use access()
		$args = func_get_args();
		$result = static::$arrs;
		foreach(func_get_args() as $key)
			if(!isset($result[$key]))
				return null;
			else
				$result = $result[$key];
		
		return $result;
	}
}