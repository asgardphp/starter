<?php
namespace Coxis\Tests\App\Article\Models;

class Author extends \Coxis\Core\ORM\ModelORM {
	public static $properties = array(
		'name',
	);
	
	public static $files = array(
	);
	
	public static $relationships = array(
		'articles'	=>	array(
			'model'	=>	'\Coxis\Tests\App\Actualite\Models\Article',
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