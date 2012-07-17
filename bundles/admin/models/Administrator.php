<?php
namespace Coxis\Bundles\Admin\Models;

class Administrator extends Model {
	//~ /**
	//~ @Length(100)
	//~ */
	//~ public $username;
	
	//~ /**
	//~ @Length(100)
	//~ @SetFilter({'Administrator', 'hash'})
	//~ */
	//~ public $password;
	
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
	
	public static function behaviors() {
		return array(
		);
	}
	
	public static function relationships() {
		return array();
	}
	
	public static function files() {
		return array();
	}
	
	public static function hash($pwd) {
		return sha1(Config::get('salt').$pwd);
	}
}