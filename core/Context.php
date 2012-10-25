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

		$facades = array(
			'URL'				=>	'\Coxis\Core\URL',
			'Router'			=>	'\Coxis\Core\Router',
			'Config'			=>	'\Coxis\Core\Config',
			'Hook'				=>	'\Coxis\Core\Hook',
			'Response'			=>	'\Coxis\Core\Response',
			'Memory'			=>	'\Coxis\Core\Memory',
			'Flash'				=>	'\Coxis\Core\Tools\Flash',
			'DB'				=>	'\Coxis\Core\DB\DB',
			'CLIRouter'			=>	'\Coxis\Core\CLI\Router',
			'Validation'		=>	'\Coxis\Core\Validation',
			'ModelsManager'		=>	'\Coxis\Core\ModelsManager',

			'Locale'			=>	'\Coxis\Core\Tools\Locale',

			'HTML'				=>	'\Coxis\Core\Tools\HTML',
			'Importer'			=>	'\Coxis\Core\Importer',

			'Request'		=>	'\Coxis\Core\Request',
			'GET'			=>	'\Coxis\Core\Inputs\GET',
			'POST'			=>	'\Coxis\Core\Inputs\POST',
			'FILE'			=>	'\Coxis\Core\Inputs\FILE',
			'Server'		=>	'\Coxis\Core\Inputs\SERVER',
			'Cookie'		=>	'\Coxis\Core\Inputs\COOKIE',
			'Session'		=>	'\Coxis\Core\Inputs\SESSION',
			'ARGV'			=>	'\Coxis\Core\Inputs\ARGV',
			'JSON'			=>	'\Coxis\Core\Inputs\JSON',
		);

		$this->ioc->register('autoloader', function() {
			return new \Coxis\Core\Autoloader;
		});

		$this->ioc->register('db', function() {
			return new \Coxis\Core\DB\DB(\Config::get('database'));
		});

		foreach($facades as $facade=>$class) {
			if(!$this->ioc->registered(strtolower($facade))) {
				$this->ioc->register(strtolower($facade), function() use($class) {
					return new $class;
				});
			}
			$this->autoloader->map[strtolower($facade)] = 'core/facades/'.$facade.'.php';
		}
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