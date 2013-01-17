<?php
namespace Coxis\Validation;

class Validator {
	public $constrains = array();
	public $messages = array();

	function __construct($constrains=array(), $messages=array()) {
		$this->setConstrains($constrains);
		$this->setMessages($messages);
	}
	
	public function setConstrains($constrains) {
		foreach($constrains as $attribute=>$attr_constrains) {
			if(!is_array($attr_constrains))
				$attr_constrains = array($attr_constrains);
			foreach($attr_constrains as $k=>$v)
				if(is_int($k)) {
					try {
						list($rule, $params) = explode(':', $v);
					} catch(\ErrorException $e) {
						$rule = $v;
						$params = array();
					}
					$constrains[$attribute] = array();
					$constrains[$attribute][$rule] = $params;
				}
		}
		
		$res = array();
		foreach($constrains as $attribute=>$attr_constrains) {
			foreach($attr_constrains as $rule=>$params) {
				$callback = null;
				if(!is_string($params) && is_callable($params)) {
					$callback = $params;
					$params = array();
				}
				// elseif(isset(static::$rules[$rule]))
				// 	if(!is_array($params))
				// 		$params = array($params);
					
				$res[$attribute][$rule] = array(
					'rule'	=>	$rule, 
					'params'	=>	$params,
					'callback'	=>	$callback,
				);
			}
		}		$this->constrains = $res;
		
		return $this;
	}
	
	public function setMessages($messages) {
		$this->messages = $messages;
		return $this;
	}

	public function errors($data) {
		$missing = array_diff(array_keys($this->constrains), array_keys($data));
		if(is_array($missing))
			foreach($missing as $key)
				$data[$key] = null;
		
		$errors = array();
		foreach($data as $attribute=>$val) {
			$res = $this->attributeError($attribute, $val, $data);
			if($res)
				if(is_array($res))
					$errors[$attribute] = $res;
				else
					$errors[$attribute][] = $res;
		}
		
		return array_filter($errors);
	}
	
	public function getMessage($rule, $attribute, $default=null) {
		if(isset($this->messages[$attribute][$rule]))
			$msg = $this->messages[$attribute][$rule];
		elseif(isset($this->messages[$attribute]['_default']))
			$msg = $this->messages[$attribute]['_default'];
		elseif(isset($this->messages['_default']))
			$msg = $this->messages['_default'];
		elseif($default)
			$msg = $default;
		else
			$msg = 'The field "'.$attribute.'" is incorrect.';
		
		return $msg;
	}
	
	public function attributeError($attribute, $val, $data, $checkArray=true) {
		if($checkArray && isset($this->constrains[$attribute]['is_array']) && $constrain=$this->constrains[$attribute]['is_array']) {
			$params = array($constrain['params'], $data);
			if($msg = $this->error($constrain['rule'], $constrain['callback'], $attribute, $val, $params))
				return $msg;
			else {
				$messages = array();
				foreach($val as $k=>$v) {
					if($err = $this->attributeError($attribute, $v, $params, false))
						$messages[$k] = $err;
				}
				if(!$messages)
					return false;
				return $messages;
			}
		}

		if(!isset($this->constrains[$attribute]))
			return false;
		foreach($this->constrains[$attribute] as $constrain) {
			$rule = $constrain['rule'];
			$callback = $constrain['callback'];
			$params = $constrain['params'];
			
			$params = array($params, $data);
			$msg = $this->error($rule, $callback, $attribute, $val, $params);
			
			if($msg)
				return $msg;
		}
		return false;
	}

	public function error($rule, $callback, $attribute, $val, $params) {
		return \Validation::error($this, $rule, $callback, $attribute, $val, $params);
	}
}