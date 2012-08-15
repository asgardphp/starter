<?php
class Participant extends \Coxis\Core\ORM\ModelORM {
	//~ public static function _autoload() {
		//~ parent::_autoload();
		//~ static::$properties['magasin']['validation'] = function($attribute, $value, $params, $validator) {
			//~ d($attribute, $value, $params, $validator);
			
			//~ return 'Erreur champ '.$attribute;
		//~ };
	//~ }
	
	public static $properties = array(
			'nom'	=>	array(
					),
			'prenom'	=>	array(
					),
			'adresse'	=>	array(
					),
			'code_postal'	=>	array(
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
			'magasin'	=>	array(
				'required'	=>	false,
			),
	);
	
	public static $files = array(	
		);
	
	public static $relationships = array(
		'jeu'	=>	array(
			'type'	=>	'belongsTo',
			'model'	=>	'jeu',
		)
	);
	
	public static $behaviors = array(	
		);
		
	public static $meta = array(
			);
		
	public static $messages = array(
		'nom'	=>	array(
			'required'	=>	'Le champ "nom" est requis.',
		),
		//~ 'email'	=>	array(
			//~ 'unique'	=>	'Cette adresse email est déjà utilisée.',
		//~ ),
	);
	
	public function __toString() {
		return $this->nom;
	}
}