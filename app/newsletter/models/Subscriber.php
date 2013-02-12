<?php
class Subscriber extends Model {
	static $properties = array(
		'email'
	);
	
	#General
	public function __toString() {
		return (string)$this->email;
	}	
}