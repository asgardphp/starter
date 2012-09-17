<?php
class ArrayProperty extends BaseProperty {
	public function getRules() {
		$rules = parent::getRules();
		$rules['array'] = true;

		return $rules;
	}

	public function getDefault() {
		return array();
	}

	public function serialize($obj) {
		return serialize($obj);
	}

	public function unserialize($str) {
		try {
			return unserialize($str);
		} catch(\ErrorException $e) {
			return array($str);
		}
		// return unserialize($str);
	}

	public function set($val) {
		if(is_array($val))
			return $val;
		try {
			return unserialize($val);
		} catch(\ErrorException $e) {
			return (array)$val;
		}
	}
}
