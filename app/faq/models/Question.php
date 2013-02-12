<?php
class Question extends \Coxis\Core\Model {
	public static $properties = array(
		'question',
		'answer',
	);
	
	public static $relations = array(	
	);
	
	public static $behaviors = array(
		'sortable',
	);
		
	public static $meta = array(
	);
	
	public function __toString() {
		return (string)$this->question;
	}
}