<?php
namespace Coxis\Core\Form;

abstract class WidgetHelper {
	public $name;
	public $value = null;
	protected $dad = null;
	public $params;

	function __construct() {
		if(!isset($this->params['type']))
			$this->params['type'] = 'text';
	}
	
	public function __toString() {
		return (string)$this->val();
	}
	
	public function val() {
		return $this->value;
	}
	
	public function setName($name) {
		$this->name = $name;
	}
	
	public function setValue($value) {
		$this->value = $value;
	}
	
	public function getParents() {
		return $this->dad->getParents();
	}
	
	public function getID() {
		$parents = $this->getParents();
		
		if(sizeof($parents) > 0) {
			$id = $parents[0].'-';
			for($i=1; $i<sizeof($parents); $i++)
				$id .= $parents[$i].'-';
			$id .= $this->name;
			return $id;
		}
		else
			return $this->name;
	}
	
	public function getName() {
		$parents = $this->getParents();
		
		if(sizeof($parents) > 0) {
			$id = $parents[0];
			for($i=1; $i<sizeof($parents); $i++)
				$id .= '['.$parents[$i].']';
			$id .= '['.$this->name.']';
			return $id;
		}
		else
			return $this->name;
	}
}