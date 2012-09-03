<?php
class Jeu extends \Coxis\Core\ORM\ModelORM {
	public static $properties = array(
					'titre'	=>	array(
							),
					'couleur_de_fond'	=>	array(
							),
					'adresse'	=>	array(
							),
					'date_debut'	=>	array(
						'type'	=>	'date',
							),
					'date_fin'	=>	array(
						'type'	=>	'date',
							),
					'question'	=>	array(
							),
					'reponses'	=>	array(
							),
					'bonne_reponse'	=>	array(
							),
					'codes_barres'	=>	array(
								'required'	=>	false,
							),
					'lien_optionnelle'	=>	array(
								'required'	=>	false,
							),
					'question_magasin'	=>	array(
								'required'	=>	false,
							),
					// 'magasins'	=>	array(
					// 			'required'	=>	false,
					// 		),
			);
	
	public static $files = array(	
			'image_de_fond' => array(
			'dir'	=>	'jeu/',
						'type'	=>	'image',
						'required'	=>	true,
					),
			'valider' => array(
			'dir'	=>	'jeu/',
						'type'	=>	'image',
						'required'	=>	true,
						'format'	=>	IMAGETYPE_PNG,
					),
			'reglement_du_jeu' => array(
			'dir'	=>	'jeu/',
						'type'	=>	'image',
						'required'	=>	true,
						'format'	=>	IMAGETYPE_PNG,
					),
			'pdf' => array(
			'dir'	=>	'jeu/',
						'type'	=>	'file',
						'required'	=>	true,
					),
			'image_optionnelle' => array(
			'dir'	=>	'jeu/',
						'type'	=>	'image',
						'required'	=>	false,
						'format'	=>	IMAGETYPE_PNG,
					),
		);
	
	public static $relationships = array(	
		'participants'	=>	array(
			'type'	=>	'hasMany',
			'model'	=>	'participant',
		),
		'magasins'	=>	array(
			'type'	=>	'HMABT',
			'model'	=>	'magasin',
		),
	);
	
	public static $behaviors = array(	
		);
		
	public static $meta = array(
			);
	
	public function __toString() {
		return $this->titre;
	}
}