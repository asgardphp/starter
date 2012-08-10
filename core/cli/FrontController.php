<?php
namespace Coxis\Core\Cli;

class FrontController extends CLIController {
	public function mainAction($request) {
		global $argv;
			
		array_shift($argv);
		
		if(sizeof($argv) == 0)
			static::usage();
		
		$route = $argv[0];
		array_shift($argv);

		$args = static::parseArgs($argv);
		
		if(isset($args['env']))
			define('_ENV_', $args['env']);
		else
			define('_ENV_', 'dev');
				
		/* CONFIG */
		\Coxis\Core\Config::loadConfigDir('config');
		if(\Coxis\Core\Config::get('error_display'))
			\Coxis\Core\Error::display(true);
			
		BundlesManager::loadBundles();
		
		#todo remove
		try {
			while(ob_end_clean()){}
		} catch(\Exception $e) {}
		
		//~ try {
			#alias
			if(strpos($route, ':') === false)
				$route = Router::$routes[$route];
			
			list($controller, $action) = explode(':', $route);

		//~ d($argv, $args);
			Router::dispatch($controller, $action, $args);
		//~ } catch(\Exception $e) {
			//~ static::usage();
		//~ }
	}
	
	public static function usage() {
		echo 'usage...';
		die();
	}
	
	private static function parseArgs($argv) {
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