<?php
namespace Tests\App\Actualite\Entities;

class Commentaire extends \Asgard\Core\Entity {
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
			'entity'	=>	'\Tests\App\Actualite\Entities\Actualite',
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