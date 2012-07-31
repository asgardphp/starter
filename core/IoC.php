<?php
class IoC {
	private static $registry = array();

	public static function _autoload() {
		static::register('DataMapper', function($model, $tables=array()) {
			return new ORM($model, $tables);
		});
	}

	public static function register($name, $callback) {
		static::$registry[$name] = $callback;
	}
	
	public static function resolve($name) {
		//~ d(func_get_args());
		$params = func_get_args();
		//~ d($params);
		if(isset($params[0]))
			unset($params[0]);
		//~ d($params);
		//~ d(call_user_func_array(static::$registry[$name], $params));
		return call_user_func_array(static::$registry[$name], $params);
		//~ return static::$registry[$name]();
	}
}