<?php
namespace Coxis\Core\Form;

class Checkboxes extends WidgetHelper implements \ArrayAccess, \Iterator {
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
	
	public function next() {
		$this->pos++;
	}
  
    public function key()  {
    	return $this->pos;
    }
  
    public function valid() {
		return ($this->pos < sizeof($this->params['choices']));
    }
  
    public function current() {
    	$keys = array_keys($this->params['choices']);
		return new CheckboxInput($this, $keys[$this->pos]);
    }
	
	// public function hasNext() {
	// 	return ($this->pos < sizeof($this->params['choices']));
	// }
	
	// public function next() {
	// 	if($this->hasNext()) {
	// 		$keys = array_keys($this->params['choices']);
	// 		return new CheckboxInput($this, $keys[$this->pos++]);
	// 	}
	// 	else
	// 		return false;
	// }
	
	//todo be able to print label directly : echo $box->label;
	
	public function offsetSet($offset, $value) {
		if(is_null($offset))
			$this->params[] = $value;
		else
			$this->params[$offset] = $value;
	}

	public function offsetExists($offset) {
		return isset($this->params['choices'][$offset]);
	}

	public function offsetUnset($offset) {
		unset($this->params['choices'][$offset]);
	}

	public function offsetGet($offset) {
		return isset($this->params['choices'][$offset]) ? new CheckboxInput($this, $this->params['choices'][$offset]) : null;
	}
}