<?php
namespace Coxis\Tests\App\Article\Models;

// class Author extends \Coxis\Core\ORM\ModelORM {
class Author extends \Coxis\Core\Model {
	public static $properties = array(
		'name',
	);
	
	public static $files = array(
	);
	
	public static $relations = array(
		'articles'	=>	array(
			'model'	=>	'\Coxis\Tests\App\Article\Models\Article',
			'type'		=>	'HMABT',
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