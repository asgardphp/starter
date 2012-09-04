<?php
class Centrale extends \Coxis\Core\ORM\ModelORM {
	public static $properties = array(
					'nom'	=>	array(
							),
			);
	
	public static $files = array(	
		);
	
	public static $relationships = array(	
			'magasins' => array(
						'type'	=>	'hasMany',
						'model'	=>	'magasin',
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