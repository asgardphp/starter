<?php
namespace Coxis\Core\Properties;

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

	public function __toString() {
		return $this->name;
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

	public function getRules() {
		$res = $this->params;
		$res[$res['type']] = true;
		unset($res['type']);
		unset($res['setHook']);
		return $res;
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