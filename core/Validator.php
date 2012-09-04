<?php
namespace Coxis\Core;

class Validator {
	private static $rules = array();
	public $constrains = array();
	public $messages = array();

	public static function _autoload() {
		static::register('same', function($attribute, $value, $params, $validator) {
			$as = $params[0];
			$as_value = $params[1][$as];
			if($value !== $as_value) {
				$msg = $validator->getMessage('same', $attribute, __('The field ":attribute" must be same as ":as".'));
				return Validator::format($msg, array(
					'attribute'	=>	$attribute,
					'as'	=>	$as,
				));
			}
		});
		
		static::register('exact_length', function($attribute, $value, $params, $validator) {
			if(strlen($value) > $params[0]) {
				$msg = $validator->getMessage('integer', $attribute, __('The field ":attribute" must be :length characters.'));
				return Validator::format($msg, array(
					'attribute'	=>	$attribute,
					'length'	=>	$params[0],
				));
			}
		});
		
		static::register('integer', function($attribute, $value, $params, $validator) {
			if(!preg_match('/[0-9]*/', $value)) {
				$msg = $validator->getMessage('integer', $attribute, __('The field ":attribute" must be an integer.'));
				return Validator::format($msg, array(
					'attribute'	=>	$attribute,
				));
			}
		});
		
		static::register('required', function($attribute, $value, $params, $validator) {
			$required = $params[0];
			if(!$required)
				return false;
			if(!($value !== null && $value !== '')) {
				$msg = $validator->getMessage('required', $attribute, __('The field ":attribute" is required.'));
				return Validator::format($msg, array(
					'attribute'	=>	$attribute,
				));
			}
		});
		
		static::register('email', function($attribute, $value, $params, $validator) {
			if(!filter_var($value, FILTER_VALIDATE_EMAIL)) {
				$msg = $validator->getMessage('email', $attribute, __('The field ":attribute" must be a valid e-mail address.'));
				return Validator::format($msg, array(
					'attribute'	=>	$attribute,
				));
			}
		});
		
		static::register('image', function($attribute, $value, $params, $validator) {
			try {
				$mime = mime_content_type($value['tmp_name']);
				if(!in_array($mime, array('image/jpeg', 'image/png', 'image/gif'))) {
					$msg = $validator->getMessage('image', $attribute, __('The file ":attribute" must be an image.'));
					return Validator::format($msg, array(
						'attribute'	=>	$attribute,
					));
				}
			} catch(\ErrorException $e) {}
		});
		
		static::register('filerequired', function($attribute, $value, $params, $validator) {
			$msg = false;
			//~ d($value);
			if(!$value)
				$msg = $validator->getMessage('filerequired', $attribute, __('The file ":attribute" is required.'));
			elseif(!file_exists($value))
				$msg = $validator->getMessage('fileexists', $attribute, __('The file ":attribute" does not exist.'));
			if($msg)
				return Validator::format($msg, array(
					'attribute'	=>	$attribute,
				));
		});
		
		static::register('unique', function($attribute, $value, $params, $validator) {
			$in = $params[0];
			$loadby = 'loadBy'.$attribute;
			if(!$in::$loadby($value))
				return;
			elseif(isset($params['id']) && $params['id'] == $in::$loadby($value)->id)
				return;
			
			$msg = $validator->getMessage('unique', $attribute, __('The field ":attribute" must be unique.'));
			if($msg)
				return Validator::format($msg, array(
					'attribute'	=>	$attribute,
				));
		});
	}
	
	public static function format($msg, $params) {
		foreach($params as $k=>$v)
			$msg = str_replace(':'.$k, $v, $msg);
		return $msg;
	}

	function __construct($constrains=array(), $messages=array()) {
		$this->setConstrains($constrains);
		$this->setMessages($messages);
	}
	
	public static function register($rule, $callback) {
		static::$rules[$rule] = $callback;
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
				elseif(isset(static::$rules[$rule]))
					if(!is_array($params))
						$params = array($params);
					
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
		if(is_array($missing))
			foreach($missing as $key)
				$data[$key] = null;
		
		$errors = array();
		foreach($data as $attribute=>$val)
			$errors[] = $this->attributeError($attribute, $val, $data);
		
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
	
	public function attributeError($attribute, $val, $data) {
		if(!isset($this->constrains[$attribute]))
			return false;
		foreach($this->constrains[$attribute] as $constrain) {
			$rule = $constrain['rule'];
			$callback = $constrain['callback'];
			$params = $constrain['params'];
			
			if(!is_array($params))
				$params = array($params);
			$params = array_merge($params, array($data));
			$msg = $this->error($rule, $callback, $attribute, $val, $params);
			
			if($msg)
				return $msg;
		}
		return false;
	}
	
	public function error($rule, $callback, $attribute, $val, $params) {
		if(!$callback) {
			if(!isset(static::$rules[$rule]))
				return false;
			$callback = static::$rules[$rule];
		}
		$result = call_user_func_array($callback, array($attribute, $val, $params, $this));
		
		return $result;
	}
}