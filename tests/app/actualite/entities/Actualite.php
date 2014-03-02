<?php
namespace Tests\App\Actualite\Entities;

class Actualite extends \Coxis\Core\Entity {
	public static $properties = array(
		'titre',
		'date'    =>    array(
			'validation' => array(
				'required'	=>	false,
			)
		),
		'lieu'    =>    array(
			'validation' => array(
				'required'	=>	false,
			)
		),
		'introduction',
		'contenu' => array(
			'validation' => array(
				'required'	=>	true,
			)
		),
		'test'	=>	array(
			'i18n'	=>	true,
			'validation' => array(
				'required'	=>	false,
			)
		),
		'image' => array(
			'type'	=>	'file',
			'dir'	=>	'actualite/',
			'filetype'	=>	'image',
			'validation' => array(
				'required'	=>	false,
			)
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
		// 'Coxis\Behaviors\SlugifyBehavior' => true,
		// 'Coxis\Behaviors\SortableBehavior' => true,
	);
		
	#General
	public function __toString() {
		return (string)$this->titre;
	}
}