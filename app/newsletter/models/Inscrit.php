<?php
class Inscrit extends Model {
			/**
			@Type('email')
			*/
			public $email;
	
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