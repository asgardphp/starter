<?php
class Mailing extends Model {
	static $properties = array(
		'title',
		'content',
		'plaintext' => array(
			'required'	=>	false,
		),
	);
	
		#General
	public function __toString() {
		return $this->title;
	}	
}