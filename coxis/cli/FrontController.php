<?php
namespace Coxis\Cli;

class FrontController extends CLIController {
	public function mainAction($request) {
		global $argv;
			
		array_shift($argv);
		
		if(sizeof($argv) == 0)
			static::usage();
		
		$route = $argv[0];
		array_shift($argv);

		$args = static::parseArgs($argv);
		
		if(!defined('_ENV_')) {
			if(isset($args['env']))
				define('_ENV_', $args['env']);
			else
				define('_ENV_', 'dev');
		}

		/* CONFIG */
		\Config::loadConfigDir('config');
			
		// \BundlesManager::loadBundles();
		\Coxis::load(true);
		
		#todo remove
		while(ob_get_level()){ ob_end_clean(); }
		
		if(!\CLIRouter::dispatch($route, $args))
			static::usage();
	}
	
	public static function usage() {
		echo 'Usage: '."\n";
		foreach(\CLIRouter::inst()->routes as $name=>$route) {
			echo $name;
			if(isset($route['usage']) && $route['usage'])
				echo ": ".$route['usage'];
			echo "\n";
			if(isset($route['description']) && $route['description'])
				echo "    ".$route['description']."\n";
		}
		die();
	}
	
	protected static function parseArgs($argv) {
		$res = array();
		for($k=0; $k<sizeof($argv); $k++) {
			$v = $argv[$k];
			if(preg_match('/^--([^ =]+)=(.+)/', $v, $matches)) {
				$res[$matches[1]] = $matches[2];
			}
			elseif(preg_match('/^--(.+)/', $v, $matches)) {
				$res[$matches[1]] = $argv[$k+1];
				$k++;
			}
			elseif(preg_match('/^-(.+)/', $v, $matches)) {
				$res[$matches[1]] = $argv[$k+1];
				$k++;
			}
			else {
				$res[] = $v;
			}
		}
		
		return $res;
	}
}