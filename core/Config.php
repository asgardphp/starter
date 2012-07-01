<?php
//todo merge with coxis?
class Config {
	private static $config = array();
	
	public static function loadConfigFile($filename) {
		require($filename);
		if(isset($config['all']))
			static::load($config['all']);
		if(isset($config[_ENV_]))
			static::load($config[_ENV_]);
	}
	
	public static function load($config) {
		foreach($config as $key=>$value)
			static::set($key, $value);
	}
	
	public static function set() {
		$args = func_get_args();
		$arr =& static::$config;
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
		$result = static::$config;
		foreach(func_get_args() as $key)
			if(!isset($result[$key]))
				return null;
			else
				$result = $result[$key];
		
		return $result;
	}
}