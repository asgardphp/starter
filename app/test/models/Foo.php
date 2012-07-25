<?php
class Foo extends \Coxis\Core\Model {
	public static $properties = array(
		'date_naissance',
		'mot_de_passe'	=>	array(
			'setFilter'	=>	array('Foo', 'hash'),
		),
		'email',
	);
	
	public static function hash($val) {
		return md5($val);
	}
	
	public static $files = array(	
		'image' => array(
			'dir'	=>	'actualite/',
			'type'	=>	'image',
			'required'	=>	true,
			'multiple'	=>	true,
		),
	);
	
	public static $relationships = array(
		//~ 'commentaires'	=>	array(
			//~ 'model'	=>	'commentaire',
			//~ 'type'		=>	'HMABT',
			//~ 'type'		=>	'hasOne',
			//~ 'type'		=>	'belongsTo',
			//~ 'type'		=>	'hasMany',
			//~ HMABT
		//~ ),
	);
	
	public static $behaviors = array(
		'slugify' => true,
		'sortable' => true,
	);
		
	public static $meta = array();
	
	public static $messages = array(
		'date_naissance'	=>	array(
			'_default'	=>	'Date de naissance est invalide.',
		)
	);
		
	#General
	public function __toString() {
		return (string)$this->email;
	}
}