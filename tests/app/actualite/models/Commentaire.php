<?php
namespace Tests\App\Actualite\Models;

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
	
	public static $relations = array(
		'actualite'	=>	array(
			'model'	=>	'\Tests\App\Actualite\Models\Actualite',
			'has'	=>	'one',
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