<?php
class Value extends Model {
	public $key;
	/**
	@Required(false)
	*/
	public $value;
	
	public function __toString() {
		return $this->key;
	}
	
	public static function get($name) {
		$value = static::loadByKey($name);
		if(!$value)
			$value = Value::create()->save(array('key'=>$name));
			
		return $value;
	}
}