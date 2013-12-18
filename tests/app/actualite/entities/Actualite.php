<?php
namespace Tests\App\Actualite\Entities;

class Actualite extends \Coxis\Core\Entity {
	public static $properties = array(
		'titre',
		'date'    =>    array(
			'required'    =>    false,
		),
		'lieu'    =>    array(
			'required'    =>    false,
		),
		'introduction',
		'contenu' => array(
			'required' => true,
		),
		'test'	=>	array(
			'i18n'	=>	true,
			'required'	=>	false,
		),
		'image' => array(
			'type'	=>	'file',
			'dir'	=>	'actualite/',
			'filetype'	=>	'image',
			'required'	=>	false,
			//~ 'multiple'	=>	true,
		),
	);
	
	public static $relations = array(
		'commentaires'	=>	array(
			'entity'	=>	'\Tests\App\Actualite\Entities\Commentaire',
			//~ 'type'		=>	'HMABT',
			//~ 'type'		=>	'hasOne',
			'has'		=>	'many',
			//~ 'type'		=>	'hasMany',
			//~ HMABT
		),
	);
	
	public static $behaviors = array(
		'Coxis\Behaviors\SlugifyBehavior' => true,
		'Coxis\Behaviors\SortableBehavior' => true,
	);
		
	#General
	public function __toString() {
		return (string)$this->titre;
	}
}