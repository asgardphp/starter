<?php
class ModelDecorator {
	private $_model = null;

	public function _getModel() {
		return $this->_model;
	}
	
	public function raw($name) {
		return $this->_model->$name;
	}

	public function __construct($model) {
		$this->_model = $model;
		
		return $this->_model;
	}

	public function __set($name, $value) {
		$property = $this->_model->getProperty($name);
		if(isset($property['setfilter']))
			$value = call_user_func($property['setfilter'], $value);
		$this->_model->$name = $value;
	}
	
	public function __get($name) {
		if(Coxis::get('in_view'))
			if(is_string($this->_model->$name))
				return HTML::sanitize($this->_model->$name);
			else
				return $this->_model->$name;
		else
			return $this->_model->$name;
	}
	
	public function __isset($name) {
		return isset($this->_model->$name);
	}
	
	public function __unset($name) {
		unset($this->_model->$name);
	}
	
	public function __call($name, $arguments) {
		return call_user_func_array(array($this->_model, $name), $arguments);
	}
	
	//~ public static function getStatic($name) {
		//~ $a = access(debug_backtrace(), 1);
		//~ $model = $a['args'][0]->_getModel();
		//~ return $model::$$name;
		
	//~ d($name);
		//todo quite ugly...
		//~ $a = access(debug_backtrace(), 1);
		//~ $model = $a['args'][0]->_getModel();
		//~ d($model);
		//~ d($a['args'][0]);
		//~ d();
		//~ d($name);
		//~ return $model::$$name;
	//~ }
	
	//~ public static function __callStatic($name, $arguments) {
		//~ d($name, $arguments);
		
		//~ $a = access(debug_backtrace(), 2);
		//~ d($a);
	//~ d(debug_backtrace());
	//~ d(get_called_class());
		//~ return call_user_func_array(array(get_class($this->_model), $name), $arguments);
	//~ }
	
	public function __toString() {
		return $this->_model->__toString();
	}
	
	public function set($vars) {
	//~ d($vars);
	//~ d(debug_backtrace());
		foreach($vars as $k=>$v)
				$this->$k = $v;
				
		return $this;
	}
}