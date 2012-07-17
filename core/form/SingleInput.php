<?php
namespace Coxis\Core\Form;

class SingleInput {
	protected $dad = null;
	protected $key = null;

	function __construct($dad, $key) {
		$this->dad = $dad;
		$this->key = $key;
	}
	
	public function getID() {
		return $this->dad->getID().'-'.$this->key;
	}

	public function label($label=null) {
		if(!$label)
			$label = $this->text();
		
		echo '<label for="'.$this->getID().'">'.$label.'</label>';
		
		return $this;
	}

	public function text() {
		return $label = $this->dad->params['choices'][$this->key];
	}
}