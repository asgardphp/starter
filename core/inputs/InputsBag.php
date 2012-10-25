<?php
namespace Coxis\Core\Inputs;

class InputsBag {
	protected $inputs = array();

	function __construct($inputs=array()) {
		$this->inputs = $inputs;
	}

	public function get($name, $default=null) {
		return isset($this->inputs[$name]) ? $this->inputs[$name]:$default;
	}

	public function set($name, $value) {
		if(is_array($name)) {
			$this->inputs = array_merge($this->inputs, $name);
		}
		else
			$this->inputs[$name] = $value;
		return $this;
	}

	public function has($name) {
		return isset($this->inputs[$name]);
	}

	public function remove($name) {
		unset($this->inputs[$name]);
		return $this;
	}

	public function all() {
		return $this->inputs;
	}

	public function clear() {
		$this->inputs = array();
		return $this;
	}

	public function setAll($all) {
		return $this->clear()->set($all);
	}
}