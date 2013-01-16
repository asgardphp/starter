<?php
namespace Coxis\Cli;

class Router {
	public function addRoute($route, $action, $usage='', $description='') {
		$this->routes[$route]['route'] = $action;
		$this->routes[$route]['usage'] = $usage;
		$this->routes[$route]['description'] = $description;
	}

	public $routes = array(
		#move it to git
		//~ 'init'	=>	'coxis:test',
		#git clone https://leyou@bitbucket.org/leyou/coxis.git
		//~ git clone ...
		'get'	=>	array(
			'usage'	=>	'get [relative_url]',
			'description'	=>	'Make a GET request on an url',
			'route'	=>	array('Coxis\Cli\Coxis', 'get'),	#set config var
		),
		// 'set'	=>	array(
		// 	'usage'	=>	'set ',
		// 	'route'	=>	array('Coxis\Cli\Coxis', 'set'),	#set config var
		// ),
		//~ 'search'	=>	'coxis:test',	#search for bundles
		'import'	=>	array(
			'usage'	=>	'import bundle',
			'description'	=>	'Import a new bundle',
			'route'	=>	array('Coxis\Cli\Coxis', 'import'),	#import bundle
		),
		'build'	=>	array(
			'usage'	=>	'build build.yml',
			'description'	=>	'Build bundles from a yml file',
			'route'	=>	array('Coxis\Cli\Coxis', 'build'),	#build bundles from build.yml
		),
		'install'	=>	array(
			'usage'	=>	'install bundle',
			'description'	=>	'Install a bundle',
			'route'	=>	array('Coxis\Cli\Coxis', 'install'),	
		),
		'install-all'	=>	array(
			'usage'	=>	'installAll',
			'description'	=>	'Install all bundles',
			'route'	=>	array('Coxis\Cli\Coxis', 'installAll'),	
		),
		'console'	=>	array(
			'usage'	=>	'console',
			'description'	=>	'Open a PHP console',
			'route'	=>	array('Coxis\Cli\Coxis', 'console'),	
		),
		'publish'	=>	array(
			'usage'	=>	'publish [bundle]',
			'description'	=>	'Publish a bundle assets',
			'route'	=>	array('Coxis\Cli\Coxis', 'publish'),
		),

		'test-all'	=>	array(
			'usage'	=>	'test-all',
			'description'	=>	'Test the core and all the bundles',
			'route'	=>	array('Coxis\Cli\Coxis', 'testAll'),
		),
		'generate-tests'	=>	array(
			'usage'	=>	'generate-tests',
			'description'	=>	'Generate an AutoTest file to tests your application',
			'route'	=>	array('Coxis\Cli\Coxis', 'generateTests'),
		),
		'generate-testsuite'	=>	array(
			'usage'	=>	'generate-testsuite [output.xml]',
			'description'	=>	'Generate a testsuite file for testing all your bundles with phpunit',
			'route'	=>	array('Coxis\Cli\Coxis', 'generateTestSuite'),
		),
		
		'dump'	=>	array(
			'usage'	=>	'dump output.yml',
			'description'	=>	'Dump your database into a yml file',
			'route'	=>	array('Coxis\Cli\DB', 'dump'),	#dump data into data.yml
		),
		'backup-db'	=>	array(
			'usage'	=>	'backup',
			'description'	=>	'Dump DB into a timestamp named file',
			'route'	=>	array('Coxis\Cli\DB', 'backup'),	#dump data into default yml file
		),
		'backup-files'	=>	array(
			'usage'	=>	'backup-files',
			'description'	=>	'Backup the upload folder',
			'route'	=>	array('Coxis\Cli\DB', 'backupFiles'),	#dump data into default yml file
		),
		'load-all'	=>	array(
			'usage'	=>	'load-all',
			'description'	=>	'Load all bundles data',
			'route'	=>	array('Coxis\Cli\DB', 'loadAll'),	#load all data (including bundles), usually for startup
		),
		'load'	=>	array(
			'usage'	=>	'load',
			'description'	=>	'Load a specific data file',
			'route'	=>	array('Coxis\Cli\DB', 'load'),	#load specific data file
		),
		'version'	=>	array(
			'usage'	=>	'version',
			'description'	=>	'Give coxis version',
			'route'	=>	array('Coxis\Cli\Coxis', 'version'),
		),
		'cc'	=>	array(
			'usage'	=>	'cc',
			'description'	=>	'Clear cache',
			'route'	=>	array('Coxis\Cli\Coxis', 'cc'),
		),
	);

	public function run($controller, $action, $params=array()) {
		$controller .= 'Controller';
		$c = new $controller;
		$c->run($action, $params);
	}

	public function dispatch($route, $args=array()) {
		if(!isset($this->routes[$route]['route']))
			return false;

		$route = $this->routes[$route]['route'];
		$controller = $route[0];
		$action = $route[1];

		$this->doDispatch($controller, $action, $args);

		return true;
	}

	public function doDispatch($controller, $action, $args=array()) {
		$this->run($controller, $action, $args);
	}
}