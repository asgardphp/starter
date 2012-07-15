<?php
class Inscrit extends Model {
			//~ /**
			//~ @Type('email')
			//~ */
			//~ public $email;
	
	public static $properties = array(
		'email'    =>    array(
			'type'    =>    'email',
		),
	);
	
		#General
	public function __toString() {
		return $this->email;
	}	
		public static $files = array(	
		);
	
	public static $relationships = array(	
		);
	
	public static $behaviors = array(	
		);
}