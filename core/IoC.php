<?php
namespace Coxis\Core;

#todo remove static
class IoC {
	// private static $registry = array();
	private $registry = array();

	// // public static function _autoload() {
	// function __construct() {
	// 	// static::register('url', function() {
	// 	$this->register('url', function() {
	// 		return new \Coxis\Core\URL;
	// 	});
	// }

	// public static function register($name, $callback) {
	public function register($name, $callback) {
		$this->registry[$name] = $callback;
		// static::$registry[$name] = $callback;
	}
	
	// public static function get($name) {
	public function get($name) {
		$params = func_get_args();
		if(isset($params[0]))
			unset($params[0]);
		return call_user_func_array($this->registry[$name], $params);
		// return call_user_func_array(static::$registry[$name], $params);
	}
}