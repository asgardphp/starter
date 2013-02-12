<?php
class Actualite extends \Coxis\Core\Model {
	public static $properties = array(
		'title',
		'content',
		'image'	=>	array(
			'type'	=>	'file',
			'filetype'	=>	'image',
			'dir'	=>	'actualites',
			'required'	=>	false,
		),
	);
	
	public static $relations = array(	
	);
	
	public static $behaviors = array(
		'publish',
	);
	
	public function __toString() {
		return (string)$this->title;
	}

	public function url() {
		return \URL::url_for(array('Actualite', 'show'), array('id'=>$this->id));
	}
}