<?php
class Value extends Model {
	//~ public $key;
	//~ /**
	//~ @Required(false)
	//~ */
	//~ public $value;
	
	public function __toString() {
		return $this->key;
	}
	
	public static $properties = array(
		'key',
		'value'    => array(
			'required'    =>    false,
		),
	);
	
	public static function get($name) {
		$value = static::loadByKey($name);
		if(!$value)
			$value = Value::create(array('key'=>$name));
			
		return $value;
	}

	public static function val($name) {
		return static::get($name)->value;
	}

	public static function rawVal($name) {
		return static::get($name)->raw('value');
	}
	
	//~ public static function __callStatic($name, $args) {
		//~ throw new Exception('not implemented');
	//~ }
}