<?php
namespace Coxis\Core;

#todo check that class is contextable

class Context {
	public static $default = 'default';

	private static $instances = array();
	private $classes = array();

	private $ioc = null;

	function __construct() {
		$this->ioc = new IoC;
		$this->ioc->register('url', function() {
			return new \Coxis\Core\URL;
		});
		$this->ioc->register('router', function() {
			return new \Coxis\Core\Router;
		});
		$this->ioc->register('bundlesmanager', function() {
			return new \Coxis\Core\BundlesManager;
		});
		$this->ioc->register('autoloader', function() {
			return new \Coxis\Core\Autoloader;
		});
		$this->ioc->register('config', function() {
			return new \Coxis\Core\Config;
		});
		$this->ioc->register('hook', function() {
			return new \Coxis\Core\Hook;
		});
		$this->ioc->register('importer', function() {
			return new \Coxis\Core\Importer;
		});
		$this->ioc->register('response', function() {
			return new \Coxis\Core\Response;
		});
		$this->ioc->register('user', function() {
			return new \Coxis\Core\User;
		});
		$this->ioc->register('validation', function() {
			return new \Coxis\Core\Validation;
		});
		$this->ioc->register('memory', function() {
			return new \Coxis\Core\Memory;
		});
	}

	public static function getDefault() {
		return static::$default;
	}

	public static function setDefault($def) {
		static::$default = $def;
	}

	public static function instance($context=null) {
		if(!$context)
			$context = static::$default;
		if(!isset(static::$instances[$context]))
			static::$instances[$context] = new static;
		return static::$instances[$context];
	}

	public static function get($class) {
		$context = static::instance();
		return $context->_get($class);
	}

	private function _get($class) {
		if($class == 'ioc')
			return $this->ioc;

		if(!isset($this->classes[$class]))
			$this->classes[$class] = $this->ioc->get($class);

		return $this->classes[$class];
	}

	public function __get($name) {
		return $this->_get($name);
	}
}