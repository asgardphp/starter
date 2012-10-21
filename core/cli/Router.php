<?php
namespace Coxis\Core\Cli;

class Router {
	public function addRoute($route, $action) {
		$this->routes[$route] = $action;
	}

	public $routes = array(
		#move it to git
		//~ 'init'	=>	'coxis:test',
		#git clone https://leyou@bitbucket.org/leyou/coxis.git
		//~ git clone ...
		'get'	=>	array('Coxis\Core\Cli\Coxis', 'get'),	#set config var
		'set'	=>	array('Coxis\Core\Cli\Coxis', 'set'),	#set config var
		//~ 'search'	=>	'coxis:test',	#search for bundles
		'import'	=>	array('Coxis\Core\Cli\Coxis', 'import'),	#import bundle
		'build'	=>	array('Coxis\Core\Cli\Coxis', 'build'),	#build bundles from build.yml
		'install'	=>	array('Coxis\Core\Cli\Coxis', 'install'),	
		'install-all'	=>	array('Coxis\Core\Cli\Coxis', 'installAll'),	
		'console'	=>	array('Coxis\Core\Cli\Coxis', 'console'),	
		'publish'	=>	array('Coxis\Core\Cli\Coxis', 'publish'),	
		
		'dump'	=>	array('Coxis\Core\Cli\DB', 'dump'),	#dump data into data.yml
		'backup-db'	=>	array('Coxis\Core\Cli\DB', 'backup'),	#dump data into default yml file
		'backup-files'	=>	array('Coxis\Core\Cli\DB', 'backupFiles'),	#dump data into default yml file
		'load-all'	=>	array('Coxis\Core\Cli\DB', 'loadAll'),	#load all data (including bundles), usually for startup
		'load'	=>	array('Coxis\Core\Cli\DB', 'load'),	#load specific data file
		
		'version'	=>	array('Coxis\Core\Cli\Coxis', 'version'),
	);

	public function run($controller, $action, $params=array()) {
		$controller .= 'Controller';
		$c = new $controller;
		$c->run($action, $params);
	}

	public function dispatch($route, $args=array()) {
		if(!isset($this->routes[$route]))
			return false;

		$route = $this->routes[$route];
		$controller = $route[0];
		$action = $route[1];

		$this->doDispatch($controller, $action, $args);

		return true;
	}

	public function doDispatch($controller, $action, $args=array()) {
		$this->run($controller, $action, $args);
	}
}