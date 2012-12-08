<?php
namespace Coxis\Core\Form;

class Radio extends WidgetHelper implements \ArrayAccess {
	protected $pos=0;
	
	function __construct($dad, $name, $params, $value) {
		$this->name = $name;
		$this->dad = $dad;
		$this->params = $params;
		$this->value = $value;
		parent::__construct();
	}
	
	public function rewind() {
		$this->pos = 0;
		return $this;
	}
	
	public function hasNext() {
		return ($this->pos < sizeof($this->params['choices']));
	}
	
	public function next() {
		if($this->hasNext()) {
			$keys = array_keys($this->params['choices']);
			return new RadioInput($this, $keys[$this->pos++]);
		}
		else
			return false;
	}
	
	public function offsetSet($offset, $value) {
		if(is_null($offset))
			$this->params['choices'][] = $value;
		else
			$this->params['choices'][$offset] = $value;
	}

	public function offsetExists($offset) {
		return isset($this->params['choices'][$offset]);
	}

	public function offsetUnset($offset) {
		unset($this->params['choices'][$offset]);
	}

	public function offsetGet($offset) {
		return isset($this->params['choices'][$offset]) ? new RadioInput($this, $this->params['choices'][$offset]) : null;
	}
}