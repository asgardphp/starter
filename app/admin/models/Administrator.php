<?php
namespace Coxis\App\Admin\Models;

class Administrator extends \Coxis\Core\Model {
	public static $properties = array(
		'username'    => array(
			'length'    =>    100,
			'unique'	=>	true,
		),
		'password'    => array(
			'form'	=>	array(
				'hidden'	=>	true,
			),
			'length'    =>    100,
			'setHook'  =>    array('tools', 'hash'),
		),
	);

	#General
	public function __toString() {
		return $this->username;
	}

	public static $behaviors = array();
	public static $relations = array();
		
	public static $meta = array(
	);

	public static function configure($definition) {
		$definition->hookBefore('destroy', function($chain, $model) {
			if(Administrator::count() < 2)
				$chain->stop();
		});
	}
}