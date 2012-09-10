<?php
class Recette extends \Coxis\Core\Model {
	public static $properties = array(
					'titre'	=>	array(
							),
					'complexite'	=>	array(
								'required'	=>	false,
								'in'	=>	array (
  0 => 'Forte',
  1 => 'Moyenne',
  2 => 'Simple',
),
							),
					'budget'	=>	array(
								'required'	=>	false,
								'in'	=>	array (
  0 => 'Elevé',
  1 => 'Moyen',
  2 => 'Faible',
),
							),
					'temps_de_preparation'	=>	array(
								'required'	=>	false,
							),
					'temps_de_cuisson'	=>	array(
								'required'	=>	false,
							),
					'type_de_plat'	=>	array(
								'in'	=>	array (
  0 => 'Entrée',
  1 => 'Plat',
),
							),
					'ingredients'	=>	array(
							),
					'preparation'	=>	array(
							),
					'animal'	=>	array(
								'in'	=>	array (
  0 => 'Boeuf',
  1 => 'Veau',
  2 => 'Porc',
  3 => 'Agneau',
),
							),
					'saison'	=>	array(
								'in'	=>	array (
  0 => 'Eté',
  1 => 'Hiver',
),
							),
					'photo_small'	=>	array(
								'type'	=>	'file',
								'dir'	=>	'recettes/small',
								'filetype'	=>	'image',
							),
					'photo_big'	=>	array(
								'type'	=>	'file',
								'dir'	=>	'recettes/big',
								'filetype'	=>	'image',
							),
			);
	
	public static $files = array(	
		);
	
	public static $relationships = array(	
			'morceaux' => array(
						'type'	=>	'HMABT',
						'model'	=>	'morceau',
					),
		);
	
	public static $behaviors = array(	
			'sortable' => true,
		);
		
	public static $meta = array(
			);
	
	public function __toString() {
		return $this->titre;
	}
}