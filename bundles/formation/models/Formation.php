<?php
class Formation extends Model {
			public $titre;
	
			public $date;
	
			public $lieu;
	
			public $introduction;
			public $contenu;
	
		#General
	public function __toString() {
		return $this->titre;
	}	
		public static $files = array(	
			'image' => array(
			'dir'	=>	'formation/',
						'type'	=>	'image',
						'required'	=>	false,
					),
		);
	
	public static $relationships = array(	
		);
	
	public static $behaviors = array(	
			'page' => true,
		);
}