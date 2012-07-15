<?php
class Actualite extends Model {
	public static $properties = array(
		'titre',
		'date'    =>    array(
			'required'    =>    false,
		),
		'lieu'    =>    array(
			'required'    =>    false,
		),
		'introduction',
		'contenu',
	);
	
	public static $files = array(	
		'image' => array(
		'dir'	=>	'actualite/',
			'type'	=>	'image',
			'required'	=>	true,
		),
	);
	
	public static $relationships = array(
	);
	
	public static $behaviors = array(
		'slugify' => true,
		'sortable' => true,
	);
		
	#General
	public function __toString() {
		return (string)$this->titre;
	}
}