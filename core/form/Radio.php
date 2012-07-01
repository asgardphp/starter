<?php
class Radio extends WidgetHelper {
	private $pos=0;
	
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
}