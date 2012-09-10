<?php
class Morceau extends \Coxis\Core\Model {
	public static $properties = array(
					'nom'	=>	array(
							),
					'animal'	=>	array(
								'in'	=>	array (
  0 => 'Boeuf',
  1 => 'Veau',
  2 => 'Porc',
  3 => 'Agneau',
),
							),
					'description'	=>	array(
						'required'	=> false,
							),
			);
	
	public static $files = array(	
		);
	
	public static $relationships = array(	
			'recettes' => array(
						'type'	=>	'HMABT',
						'model'	=>	'recette',
					),
		);
	
	public static $behaviors = array(	
			'sortable' => true,
		);
		
	public static $meta = array(
			);
	
	public function __toString() {
		return $this->nom;
	}
}