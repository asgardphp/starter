<?php
namespace Coxis\Core;

class Validation {
	private $rules = array();

	public function __construct() {
		$this->register('same', function($attribute, $value, $params, $validator) {
			$as = $params[0];
			$as_value = $params[1][$as];
			if($value !== $as_value) {
				$msg = $validator->getMessage('same', $attribute, __('The field ":attribute" must be same as ":as".'));
				return Validation::format($msg, array(
					'attribute'	=>	str_replace('_', ' ', $attribute),
					'as'	=>	$as,
				));
			}
		});

		$this->register('unique', function($attribute, $value, $params, $validator) {
			$modelName = get_class($validator->model);
			if($modelName::where(array($attribute => $value, 'id!=?'=>$params[1]['id']))->count() > 0) {
				$msg = $validator->getMessage('unique', $attribute, __('":value" is already used.'));
				return Validation::format($msg, array(
					'attribute'	=>	str_replace('_', ' ', $attribute),
					'value'	=>	$value,
				));
			}
		});
		
		$this->register('equal', function($attribute, $value, $params, $validator) {
			if($value !== $params[0]) {
				$msg = $validator->getMessage('equal', $attribute, __('The field ":attribute" is not correct.'));
				return Validation::format($msg, array(
					'attribute'	=>	str_replace('_', ' ', $attribute),
				));
			}
		});
		
		$this->register('exact_length', function($attribute, $value, $params, $validator) {
			if(strlen($value) > $params[0]) {
				$msg = $validator->getMessage('exact_length', $attribute, __('The field ":attribute" must be :length characters.'));
				return Validation::format($msg, array(
					'attribute'	=>	str_replace('_', ' ', $attribute),
					'length'	=>	$params[0],
				));
			}
		});
		
		$this->register('integer', function($attribute, $value, $params, $validator) {
			if(!preg_match('/^[0-9]*$/', $value)) {
				$msg = $validator->getMessage('integer', $attribute, __('The field ":attribute" must be an integer.'));
				return Validation::format($msg, array(
					'attribute'	=>	str_replace('_', ' ', $attribute),
				));
			}
		});
		
		$this->register('required', function($attribute, $value, $params, $validator) {
			$required = $params[0];
			if(!$required)
				return false;
			if($value === null || $value === '') {
				$msg = $validator->getMessage('required', $attribute, __('The field ":attribute" is required.'));
				return Validation::format($msg, array(
					'attribute'	=>	str_replace('_', ' ', $attribute),
				));
			}
		});
		
		$this->register('email', function($attribute, $value, $params, $validator) {
			if(!filter_var($value, FILTER_VALIDATE_EMAIL)) {
				$msg = $validator->getMessage('email', $attribute, __('The field ":attribute" must be a valid e-mail address.'));
				return Validation::format($msg, array(
					'attribute'	=>	str_replace('_', ' ', $attribute),
				));
			}
		});
		
		$this->register('date', function($attribute, $value, $params, $validator) {
			if(!$value)
				return;
			if(!preg_match('/[0-9]{2}\/[0-9]{2}\/[0-9]{4}/', $value)) {
				$msg = $validator->getMessage('email', $attribute, __('The field ":attribute" must be a date (dd/mm/yyyy).'));
				return Validation::format($msg, array(
					'attribute'	=>	str_replace('_', ' ', $attribute),
				));
			}
		});
	}
	
	public function register($rule, $callback) {
		$this->rules[$rule] = $callback;
	}
	
	public static function format($msg, $params) {
		foreach($params as $k=>$v)
			$msg = str_replace(':'.$k, $v, $msg);
		return $msg;
	}
	
	public function error($validator, $rule, $callback, $attribute, $val, $params) {
		if(!$callback) {
			if(!isset($this->rules[$rule]))
				return false;
			$callback = $this->rules[$rule];
		}
		$result = call_user_func_array($callback, array($attribute, $val, $params, $validator));
		
		return $result;
	}

	public function ruleExists($rule) {
		return isset($this->rules[$rule]);
	}
}