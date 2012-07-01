<?php
class MySettings {
	static $preferences = null;

	public static function get($setting) {
	//~ d($setting, Value::loadByKey($setting));
		return Value::loadByKey($setting)->value;
		//~ if(static::$preferences == null)
			//~ static::$preferences = Preferences::load(1);
			
		//~ return static::$preferences->$setting;
	}
	
	public static function __callStatic($name, $args) {
	throw new Exception('not implemented');
		//~ if(static::$preferences == null)
			//~ static::$preferences = Preferences::load(1);
			
		//~ return call_user_func_array(array(static::$preferences, $name), $args);
		//~ return static::$preferences::$name($args);
	}
}