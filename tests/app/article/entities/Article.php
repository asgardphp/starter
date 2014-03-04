<?php
namespace Tests\App\Article\Entities;

// class Article extends \Asgard\Core\ORM\EntityORM {
class Article extends \Asgard\Core\Entity {
	public static $properties = array(
		'title',
	);
	
	public static $files = array(
	);
	
	public static $relations = array(
		'authors'	=>	array(
			'entity'	=>	'\Tests\App\Article\Entities\Author',
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