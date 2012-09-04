<?php
class Magasin extends \Coxis\Core\ORM\ModelORM {
	public static $properties = array(
					'nom'	=>	array(
							),
			);
	
	public static $files = array(	
		);
	
	public static $relationships = array(	
			'centrale' => array(
						'type'	=>	'belongsTo',
						'model'	=>	'centrale',
						'required'	=>	true,
					),
			'jeu' => array(
						'type'	=>	'HMABT',
						'model'	=>	'jeu',
					),
			'participant' => array(
						'type'	=>	'hasMany',
						'model'	=>	'participant',
					),
		);
	
	public static $behaviors = array(	
		);
		
	public static $meta = array(
			);
	
	public function __toString() {
		return $this->nom;
	}
}