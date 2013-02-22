<?php
class Actualite extends \Coxis\Core\Model {
	public static $properties = array(
		'title',
		'content'	=>	'longtext',
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
		'metas',
		'slugify'
	);
	
	public function __toString() {
		return (string)$this->title;
	}

	public function url() {
		return \URL::url_for(array('Actualite', 'show'), array('id'=>$this->id, 'slug'=>$this->slug));
	}
}