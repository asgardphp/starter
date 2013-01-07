<?php
namespace Tests\App\Article\Models;

// class Article extends \Coxis\Core\ORM\ModelORM {
class Article extends \Coxis\Core\Model {
	public static $properties = array(
		'title',
	);
	
	public static $files = array(
	);
	
	public static $relations = array(
		'authors'	=>	array(
			'model'	=>	'\Tests\App\Article\Models\Author',
			'has'		=>	'many',
		),
	);
	
	public static $behaviors = array(
		// 'sortable'	=>	true,
	);
		
	public static $meta = array();
		
	#General
	public function __toString() {
		return (string)$this->titre;
	}
}