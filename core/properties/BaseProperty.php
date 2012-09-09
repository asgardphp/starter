<?php
class BaseProperty {
	protected $model = null;
	protected $name = null;
	protected $params = array();

	function __construct($model, $name, $params) {
		$this->model = $model;
		$this->name = $name;
		$this->params = $params;
	}

	public function __get($name) {
		if(!isset($this->params[$name]))
			return null;
		return $this->params[$name];
	}

	public function getName() {
		return $this->name;
	}

	public function getParams() {
		return $this->params;
	}

	public function getDefault() {
		return '';
	}

	// public function isI18N() {
	// 	return (isset($this->params['i18n']) && $this->params['i18n']);
	// }

	public function getRules() {
		$res = $this->params;
		// try {
		$res[$res['type']] = true;
// 	} catch(\Exception $e) {
// d($this);
// 	}
		unset($res['type']);
		return $res;

		/*$rules = array();
		if(isset($this->params['length']))
			$rules['length'] = $this->params['length'];

		return $rules;*/
	}

	public function serialize($obj) {
		return (string)$obj;
	}

	public function unserialize($str) {
		return $str;
	}

	public function set($val) {
		return $val;
	}
}