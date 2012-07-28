<?php
namespace Coxis\App\Actualite\Models;

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
	);
	
	public static $files = array(	
		'image' => array(
			'dir'	=>	'actualite/',
			'type'	=>	'image',
			'required'	=>	false,
			//~ 'multiple'	=>	true,
		),
	);
	
	public static $relationships = array(
		'commentaires'	=>	array(
			'model'	=>	'\Coxis\App\Actualite\Models\Commentaire',
			//~ 'type'		=>	'HMABT',
			//~ 'type'		=>	'hasOne',
			'type'		=>	'belongsTo',
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