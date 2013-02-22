<?php
namespace App\Intranet\Models;

class User extends \Coxis\Core\Model {
	public static $properties = array(
		'username' => array(
			'unique'	=>	true,
		),
		'password' => array(
			'form'	=>	array(
				'hidden'	=>	true,
			),
			'setHook'  =>    array('tools', 'hash'),
		),
		'email'	=>	array(
			'type'	=>	'email',
			'unique'	=>	true,
		),
		'confirmed'	=>	array(
			'type'	=>	'boolean',
			'default'	=>	false,
			'form'	=>	array(
				'editable'	=>	false,
			)
		),
	);

	public function __toString() {
		return (string)$this->username;
	}

	public function getUID() {
		return sha1(\Value::val('salt').$this->id);
	}

	public static function getByUID($uid) {
		return static::where(array('SHA1(CONCAT(\''.\Cookie::get('salt').'\', id))=\''.$uid.'\''))->first();
	}
}