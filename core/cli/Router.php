<?php
namespace Coxis\Core\Cli;

class Router {
	public static $controllers = array(
		'coxis'	=>	'Coxis\Core\Cli\Coxis',
		'db'	=>	'Coxis\Core\Cli\DB',
		'migration'	=>	'Coxis\Core\Cli\Migration',
	);

	public static function addRoute($route, $action) {
		static::$routes[$route] = $action;
	}

	private static $routes = array(
		#move it to git
		//~ 'init'	=>	'coxis:test',
		#git clone https://leyou@bitbucket.org/leyou/coxis.git
		//~ git clone ...
		'set'	=>	'coxis:set',	#set config var
		//~ 'search'	=>	'coxis:test',	#search for bundles
		'import'	=>	'coxis:import',	#import bundle
		'build'	=>	'coxis:build',	#build bundles from build.yml
		'install'	=>	'coxis:install',	
		'install-all'	=>	'coxis:installAll',	
		'console'	=>	'coxis:console',	
		'publish'	=>	'coxis:publish',	
		
		'dump'	=>	'db:dump',	#dump data into data.yml
		'backup-db'	=>	'db:backup',	#dump data into default yml file
		'backup-files'	=>	'coxis:backupfiles',	#dump data into default yml file
		'load-all'	=>	'db:loadAll',	#load all data (including bundles), usually for startup
		'load'	=>	'db:load',	#load specific data file
		
		//~ 'build-db'	=>	'coxis:test',	#build db from models
		//~ 'rebuild-db'	=>	'coxis:test',	#same as above
		
		'diff'	=>	'migration:diff',	#compare models with db and generate migrations
		'migrate'	=>	'migration:migrate',
		'automigrate'	=>	'migration:automigrate',
	);

	public static function run($controller, $action, $params=array()) {
		$controller .= 'Controller';
		$c = new $controller;
		$c->run($action, $params);
	}

	public static function dispatch($route, $args=array()) {
		#alias
		if(strpos($route, ':') === false)
			$route = static::$routes[$route];
		
		list($controller, $action) = explode(':', $route);

		static::doDispatch($controller, $action, $args);
	}

	public static function doDispatch($controller, $action, $args=array()) {
		static::run(static::$controllers[$controller], $action, $args);
	}
}