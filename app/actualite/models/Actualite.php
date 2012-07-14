<?php
class Actualite extends Model {
			public $titre;
			
			/**
			@Required(false)
			*/
			public $date;
	
			/**
			@Required(false)
			*/
			public $lieu;
	
			public $introduction;
			public $contenu;
	
		#General
	public function __toString() {
		return (string)$this->titre;
	}	
		public static $files = array(	
			'image' => array(
			'dir'	=>	'actualite/',
						'type'	=>	'image',
						'required'	=>	true,
					),
		);
	
	public static $relationships = array(	
		);
	
	public static $behaviors = array(	
			'slugify' => true,
			'sortable' => true,
		);
}