<?php
namespace Coxis\Bundles\Admin\Models;

class Administrator extends \Coxis\Core\Model {
	#General
	public function __toString() {
		return $this->username;
	}
	
	public static $properties = array(
		'username'    => array(
			'length'    =>    100,
		),
		'password'    => array(
			'length'    =>    100,
			'setfilter'  =>    array(array('administrator', 'hash')),
		),
	);

	public static $behaviors = array();
	public static $relationships = array();
	
	public static function hash($pwd) {
		return sha1(Config::get('salt').$pwd);
	}
		
	public static $meta = array(
	);
}