<?php
class Commentaire extends \Coxis\Core\Model {
	public static $properties = array(
		'titre',
	);
	
	public static $files = array(	
		//~ 'image' => array(
			//~ 'dir'	=>	'actualite/',
			//~ 'type'	=>	'image',
			//~ 'required'	=>	false,
			//~ 'multiple'	=>	true,
		//~ ),
	);
	
	public static $relationships = array(
		'actualite'	=>	array(
			'model'	=>	'actualite',
			'type'		=>	'belongsTo',
		),
	);
	
	public static $behaviors = array(
		//~ 'slugify' => true,
		//~ 'sortable' => true,
	);
		
	public static $meta = array(
	);
		
	#General
	public function __toString() {
		return (string)$this->titre;
	}
}