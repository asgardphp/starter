<?php
namespace Tests\App\Actualite\Models;

class Actualite extends \Coxis\Core\Model {
	public static $properties = array(
		'titre',
		'date'    =>    array(
			'required'    =>    false,
		),
		'lieu'    =>    array(
			'required'    =>    false,
		),
		'introduction',
		'contenu',
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
			'model'	=>	'\Tests\App\Actualite\Models\Commentaire',
			//~ 'type'		=>	'HMABT',
			//~ 'type'		=>	'hasOne',
			'has'		=>	'many',
			//~ 'type'		=>	'hasMany',
			//~ HMABT
		),
	);
	
	public static $behaviors = array(
		'slugify' => true,
		'sortable' => true,
	);
		
	#General
	public function __toString() {
		return (string)$this->titre;
	}
}