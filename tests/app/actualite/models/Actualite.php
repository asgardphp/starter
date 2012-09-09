<?php
namespace Coxis\Tests\App\Actualite\Models;

// class Actualite extends \Coxis\Core\ORM\ModelORM {
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
		),
		'image' => array(
			'type'	=>	'file',
			'dir'	=>	'actualite/',
			'filetype'	=>	'image',
			'required'	=>	false,
			//~ 'multiple'	=>	true,
		),
	);
	
	public static $relationships = array(
		'commentaires'	=>	array(
			'model'	=>	'\Coxis\Tests\App\Actualite\Models\Commentaire',
			//~ 'type'		=>	'HMABT',
			//~ 'type'		=>	'hasOne',
			'type'		=>	'hasMany',
			//~ 'type'		=>	'hasMany',
			//~ HMABT
		),
	);
	
	public static $behaviors = array(
		'slugify' => true,
		'sortable' => true,
	);
		
	public static $meta = array();
		
	#General
	public function __toString() {
		return (string)$this->titre;
	}
}