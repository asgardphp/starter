<?php
namespace Coxis\Core\Inputs;

abstract class InputsBag {
	protected static function getRequest() {
		return \Request::inst();
	}

	public static function get($name, $default=null) {
		$req = static::getRequest();
		$datatype = strtolower(NamespaceUtils::basename(get_called_class()));

		return static::has($name) ? $req->{$datatype}[$name]:$default;
	}

	public static function set($name, $value) {
		$req = static::getRequest();
		$datatype = strtolower(NamespaceUtils::basename(get_called_class()));
		
		if(is_array($name))
			$req->$datatype = array_merge($req->$datatype, $name);
		else
			$req->$datatype[$name] = $value;
	}

	public static function has($name) {
		$req = static::getRequest();
		$datatype = strtolower(NamespaceUtils::basename(get_called_class()));
		
		return isset($req->{$datatype}[$name]);
	}

	public static function remove($name) {
		$req = static::getRequest();
		$datatype = strtolower(NamespaceUtils::basename(get_called_class()));
		
		unset($req->$datatype[$name]);
	}

	public static function all() {
		$req = static::getRequest();
		$datatype = strtolower(NamespaceUtils::basename(get_called_class()));
		return $req->$datatype;
	}

	public static function clear() {
		$req = static::getRequest();
		$datatype = strtolower(NamespaceUtils::basename(get_called_class()));
		
		$req->$datatype = array();
	}

	public static function setAll($all) {
		return $this->clear()->set($all);
	}
}