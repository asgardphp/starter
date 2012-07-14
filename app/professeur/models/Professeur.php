<?php
class Professeur extends Model {
			public $nom;
	
			public $prenoms;
	
			public $region;
	
			public $adresse;
	
			public $ville;
	
			public $code_postal;
	
			public $telephone;
	
			/**
			@Type('email')
			*/
			public $email;
	
			public $site_web;
	
			/**
		@In({'oui', 'non'})
		*/
		public $cours_particuliers;
	
			/**
			@Multiple(true)
			*/
			public $type_choeurs;
	
			/**
			@Length(600)
			*/
			public $informations_complementaires;
	
	public function configure() {
				Model::$_properties['professeur']['type_choeurs']['in'] = array(
					'Baroque',
					'Chanson/Variété',
					'Chants du monde',
					'Classique',
					'Comédie musique',
					'Contemporain',
					'Gospel',
					'Jazz',
					'Métal',
					'Moyen âge',
					'Pop',
					'Renaissance',
					'Rock',
					'Romantique',
					'R’n’B',
					'Variété',
					'Tout style',
				);
				
		Model::$_properties['professeur']['region']['in'] = Arpa::$regions;
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
}