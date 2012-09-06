<?php
namespace Coxis\Tests\App\Article\Models;

class Article extends \Coxis\Core\ORM\ModelORM {
	public static $properties = array(
		'title',
	);
	
	public static $files = array(
	);
	
	public static $relationships = array(
		'authors'	=>	array(
			'model'	=>	'\Coxis\Tests\App\Actualite\Models\Author',
			'type'		=>	'HMABT',
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