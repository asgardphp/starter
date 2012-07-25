<?php
namespace Coxis\Core;

class Validator {
	private static $rules = array();
	private static $rules_messages = array();
	public $constrains = array();
	public $messages = array();

	public static function _autoload() {
		static::register('same', function($attribute, $value, $params) {
			return ($value == $params[1][$params[0]]);
		}, 'The field ":attribute" must be same as ":param0".');
		static::register('integer', function($attribute, $value, $params) {
			return preg_match('/[0-9]+/', $value);
		}, 'The field ":attribute" must be an integer.');
		static::register('true', function($attribute, $value, $params) {
			return (boolean)$value;
		}, 'The field ":attribute" must be checked.');
		static::register('required', function($attribute, $value, $params) {
			return $value !== null && $value !== '';
		}, 'The field ":attribute" is required.');
		static::register('email', function($attribute, $value, $params) {
			return filter_var($value, FILTER_VALIDATE_EMAIL);
		}, 'The field ":attribute" must be a valid e-mail address.');
		static::register('image', function($attribute, $value, $params) {
			try {
				$mime = mime_content_type($value['tmp_name']);
				return in_array($mime, array('image/jpeg', 'image/png', 'image/gif'));
			} catch(\ErrorException $e) {
				return true;
			}
		}, 'The file ":attribute" must be an image.');
		static::register('filerequired', function($attribute, $value, $params) {
			return $value && file_exists($value['tmp_name']);
		}, 'The file ":attribute" is required.');
	}

	function __construct($constrains=array(), $messages=array()) {
		$this->setConstrains($constrains);
		$this->setMessages($messages);
	}
	
	public static function register($rule, $callback, $message=false) {
		static::$rules[$rule] = $callback;
		if($message)
			static::$rules_messages[$rule] = $message;
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
						$paramas = array();
					}
					$constrains[$attribute] = array();
					$constrains[$attribute][$rule] = $params;
				}
		}
		
		$res = array();
		foreach($constrains as $attribute=>$attr_constrains) {
			foreach($attr_constrains as $rule=>$params) {
				$callback = false;
				//~ d($rule, $params);
				if(!is_string($params) && is_callable($params)) {
					$callback = $params;
					$params = array();
				}
				elseif(isset(static::$rules[$rule])) {
					$callback = static::$rules[$rule];
					if(!is_array($params))
						$params = array($params);
				}
				if(!$callback)
					continue;
					
				$res[$attribute][] = array(
					'rule'	=>	$rule, 
					'params'	=>	$params,
					'callback'	=>	$callback,
				);
			}
		}
		$this->constrains = $res;
		return $this;
	}
	
	public function setMessages($messages) {
		$this->messages = $messages;
		return $this;
	}

	public function errors($data) {
		$missing = array_diff(array_keys($this->constrains), array_keys($data));
		foreach($missing as $key)
			$data[$key] = null;
		
		$errors = array();
		foreach($data as $attribute=>$val)
			$errors[] = $this->attributeError($attribute, $val, $data);
		
		return array_filter($errors);
	}
	
	public function getMessage($rule, $attribute, $params) {
		if(isset($this->messages[$attribute][$rule]))
			$msg = $this->messages[$attribute][$rule];
		elseif(isset($this->messages[$attribute]['_default']))
			$msg = $this->messages[$attribute]['_default'];
		elseif(isset($this->messages['_default']))
			$msg = $this->messages['_default'];
		elseif(isset(static::$rules_messages[$rule]))
			$msg = static::$rules_messages[$rule];
		else
			$msg = 'The field "'.$attribute.'" is invalid.';
		
		$msg = str_replace(':attribute', $attribute, $msg);
		foreach($params as $k=>$v)
			$msg = str_replace(':param'.$k, $v, $msg);
		
		return $msg;
	}
	
	public function attributeError($attribute, $val, $data) {
		if(!isset($this->constrains[$attribute]))
			return false;
		foreach($this->constrains[$attribute] as $constrain) {
			$rule = $constrain['rule'];
			$callback = $constrain['callback'];
			$params = $constrain['params'];
			if(!call_user_func_array($callback, array($attribute, $val, array_merge($params, array($data)))))
				return $this->getMessage($rule, $attribute, $params);
		}
		return false;
	}
}