<?php
class Coxis {
	public static $controller_hooks = array();
	public static $hooks_table = array();
	public static $libs = array();

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
	
	public static function preLoadClasses($file) {
		if(is_dir($file))
			foreach(glob($file.'/*') as $sub_file)
				static::preLoadClasses($sub_file);
		else {
			list($class) = explode('.', basename($file));
			static::$libs[strtolower($class)] = $file;
		}
	}
	
	public static function loadClass($class) {
		$class = strtolower($class);
		if(isset(static::$libs[$class])) {
			include_once(static::$libs[$class]);
			if(is_subclass_of($class, 'Model'))
				$class::loadModel();
		}
		elseif(function_exists('__autoload'))
				__autoload($class);
		else
			throw new Exception($class.' was not preloaded.');
	}
}