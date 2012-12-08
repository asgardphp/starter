<?php
namespace Coxis\Core\Form;

class RadioGroup extends WidgetHelper implements \ArrayAccess, \Iterator  {
	protected $pos=0;
	
	function __construct($dad, $name, $params, $value) {
		$this->name = $name;
		$this->dad = $dad;
		$this->params = $params;
		$this->value = $value;
		parent::__construct();
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
	
	public function rewind() {
		$this->pos = 0;
		return $this;
	}
	
	public function next() {
		try {
			$keys = array_keys($this->params['choices']);
			return new RadioInput($this, $keys[++$this->pos]);
		}
		catch(\Exception $e) {
			return false;
		}
	}

    public function current() {
		try {
			$keys = array_keys($this->params['choices']);
			return new RadioInput($this, $keys[$this->pos]);
		}
		catch(\Exception $e) {
			return false;
		}
    }

    public function key() {
        return $this->pos;
    }

   public  function valid() {
   		return ($this->pos < sizeof($this->params['choices']));
    }
}