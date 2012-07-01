<?php
class Choeur extends Model {
			public $nom;
	
			public $region;
	
			public $adresse;
	
			public $ville;
	
			public $code_postal;
	
			public $telephone;
			public $mobile;
	
			/**
			@Type('email')
			*/
			public $email;
	
			/**
			@Required(false)
			*/
			public $site_web;
	
			/**
			@Required(false)
			*/
			public $lieu_repetition_adresse;
	
			/**
			@Required(false)
			*/
			public $lieu_repetition_ville;
	
			/**
			@Required(false)
			*/
			public $lieu_repetition_code_postal;
	
			/**
			@Required(false)
			*/
			public $repetitions_horaires;
	
			/**
			@Multiple(true)
			*/
			public $style_musical;
	
			/**
			@Required(false)
			*/
			public $responsable_adresse;
			/**
			@Required(false)
			*/
			public $responsable_code_postal;
			/**
			@Required(false)
			*/
			public $responsable_ville;
			
			public $responsable_nom;
	
			public $responsable_prenom;
	
			public $responsable_telephone;
			/**
			@Required(false)
			*/
			public $responsable_mobile;
	
			public $responsable_email;
	
	
			/**
			@In({"Le recrutement est ouvert à tous sans audition ni entretien", "Un test de lecture", "Un test de chant", "Un entretien", "Chœur d’université et enseignement supérieur"})
			@Multiple(true)
			*/
			public $conditions_admission;
			
			
			/**
			@Multiple(true)
			*/
			public $type_choeurs;
			
			public function configure() {
				Model::$_properties['choeur']['type_choeurs']['in'] = array(
					'Baroque',
					'Chanson/Variété',
					'Chants du monde',
					'Classique',
					'Comédie musique',
					'Comptines et contes musicaux',
					'Gospel',
					'Grégorien',
					'Jazz',
					'Médiéval',
					'Musique contemporaine (après 1945)',
					'Musique du XXème siècle (avant 1945)',
					'Musique lithurgique d\'aujourd\'hui',
					'Musique traditionnelle et/ou folklorique',
					'Negro spiritual',
					'Opéra',
					'Opéra pour enfants',
					'Opérette',
					'Oratorio',
					'Renaissance',
					'Romantique',
					'Tout type de répertoire',
				);
				
				Model::$_properties['choeur']['style_musical']['in'] = array(
					'Chœur d’enfants',
					'Chœur de jeunes (16-26 ans)',
					'Chœur d’adultes',
					'Chanson/Variété',
					'Chœur associant des enfants et des adultes',
					'Chœur d’université et enseignement supérieur',
					'Chœur d’entreprise',
					'Chœur de maison de retraite',
					'Chœur liturgique',
					'Chœur mixte',
					'Chœur à voix égales de femmes',
					'Chœur à voix égales d’hommes',
					'',
					'',
					'',
				);
				
				Model::$_properties['choeur']['region']['in'] = Arpa::$regions;
			}
	
		#General
	public function __toString() {
		return $this->nom;
	}	
		public static $files = array(	
		);
	
	public static $relationships = array(	
		);
	
	public static $behaviors = array(	
			'slugify' => true,
		);
		
	//~ public static $messages = array(
		//~ 'nom' =>	array(
			//~ '_default'	=>	'Le champ nom est requis.',
		//~ )
	//~ );
}