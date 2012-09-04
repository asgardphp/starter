<?php
class Participant extends \Coxis\Core\ORM\ModelORM {
	// public static function _autoload() {
		// parent::_autoload();
		// static::$properties['magasin']['validation'] = function($attribute, $value, $params, $validator) {
		// 	d($attribute, $value, $params, $validator);
			
		// 	return 'Erreur champ '.$attribute;
		// };
		// static::$messages['magasin'] = array(
		// 	'custom'	=>	function($a,$b,$c) {
		// 		d($a,$b,$c);
		// 	},
		// );
	// }
	
	public static $properties = array(
			'civilite'	=>	array(
				'in'		=>	array('M', 'Mme', 'Mlle')
			),
			'nom'	=>	array(
					),
			'prenom'	=>	array(
					),
			'adresse'	=>	array(
					),
			'code_postal'	=>	array(
				'type'	=>	'integer',
				'length'	=> 5,
				'exact_length'	=> 5,
			),
			'ville'	=>	array(
					),
			'pays'	=>	array(
					),
			'email'	=>	array(
				'type'	=>	'email',
				//~ 'unique'	=>	'participant',
				//~ 'unique'	=>	true,
			),
			'telephone'	=>	array(
					),
			'reponse'	=>	array(
					),
			// 'magasin'	=>	array(
			// 	'required'	=>	false,
			// ),
	);
	
	public static $files = array(	
		);
	
	public static $relationships = array(
		'jeu'	=>	array(
			'type'	=>	'belongsTo',
			'model'	=>	'jeu',
		),
		'magasin'	=>	array(
			'type'	=>	'belongsTo',
			'model'	=>	'magasin',
		),
	);
	
	public static $behaviors = array(	
		);
		
	public static $meta = array(
			);
		
	public static $messages = array(
		'nom'	=>	array(
			'required'	=>	'Le champ "nom" est requis.',
		),
		// 'magasin'	=>	array(
		// 	'custom'	=>	array('Participant', 'checkMagasin'),
		// ),
		//~ 'email'	=>	array(
			//~ 'unique'	=>	'Cette adresse email est déjà utilisée.',
		//~ ),
	);

	// public static function checkMagasin($a, $b, $c) {
	// 	d($a, $b, $c);
		// 'Le champ "magasin" est requis.'
	// }
	
	public function __toString() {
		return $this->nom.' '.$this->prenom;
	}
}