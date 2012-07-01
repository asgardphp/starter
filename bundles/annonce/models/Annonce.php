<?php
class Annonce extends Model {
			public $intitule;
	
			/**
			@In({"Stage", "Chef de choeur", "Projet", "Choriste", "Concert"})
			*/
			public $categorie;
			
			public $region;
	
			public $adresse;
			public $ville;
			public $code_postal;
	
			/**
			@Length(600)
			*/
			public $contenu;
	
			public $nom;
	
			public $prenom;
			/**
			@Required(false)
			*/
			public $portable;
			public $telephone;
	
			public $email;
	
			public $site_web;
	
	public function configure() {
		Model::$_properties['annonce']['region']['in'] = Arpa::$regions;
	}
	
		#General
	public function __toString() {
		return $this->intitule;
	}	
		public static $files = array(	
		);
	
	public static $relationships = array(	
		);
	
	public static $behaviors = array(	
			'slugify' => true,
		);
}