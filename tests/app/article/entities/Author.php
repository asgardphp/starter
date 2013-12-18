<?php
namespace Tests\App\Article\Entities;

// class Author extends \Coxis\Core\ORM\EntityORM {
class Author extends \Coxis\Core\Entity {
	public static $properties = array(
		'name',
	);
	
	public static $files = array(
	);
	
	public static $relations = array(
		'articles'	=>	array(
			'entity'	=>	'\Tests\App\Article\Entities\Article',
			'has'		=>	'many',
		),
	);
	
	public static $behaviors = array(
	);
		
	public static $meta = array(
	);
		
	#General
	public function __toString() {
		return (string)$this->name;
	}
}