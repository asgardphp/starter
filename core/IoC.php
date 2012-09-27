<?php
class IoC {
	private static $registry = array();

	public static function register($name, $callback) {
		static::$registry[$name] = $callback;
	}
	
	public static function resolve($name) {
		$params = func_get_args();
		if(isset($params[0]))
			unset($params[0]);
		return call_user_func_array(static::$registry[$name], $params);
	}
}