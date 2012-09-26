<?php
namespace {
require_once('vendors/addendum/annotations.php');

/* Controllers */
class Hook extends Annotation {}
class Prefix extends Annotation {}
class Priority extends Annotation {}
class Route extends Annotation {
	public $name;
	public $requirements;
	public $method;
}
}

namespace Coxis\Core {
class BundlesManager {
	public static $routes = array();
	public static $hooks_table = array();
	public static $filters_table = array();
	public static $directories = array('bundles', 'app');
	public static $load_routes = true;
	
	public static function loadBundle($bundle) {
		\Coxis\Core\Autoloader::preloadDir($bundle.'/models');
		
		if(Coxis::get('load_locales'))
			Locale::importLocales($bundle.'/locales');
		
		if(!static::$load_routes)
			\Coxis\Core\Autoloader::preloadDir($bundle.'/controllers');
		else {
			if(file_exists($bundle.'/controllers/'))
				foreach(glob($bundle.'/controllers/*.php') as $filename) {
					$classname = \Coxis\Core\Importer::loadClassFile($filename);
					
					$reflection = new \ReflectionAnnotatedClass($classname);
					
					if($reflection->getAnnotation('Prefix'))
						$prefix = Router::formatRoute($reflection->getAnnotation('Prefix')->value);
					else
						$prefix = '';
					
					$methods = get_class_methods($classname);
					foreach($methods as $method) {
						if(!preg_match('/Action$/i', $method))
							continue;
						$method_reflection = new \ReflectionAnnotatedMethod($classname, $method);
					
						if($method_reflection->getAnnotation('Route')) {
							$route = Router::formatRoute($prefix.'/'.$method_reflection->getAnnotation('Route')->value);
							static::$routes[] = array(
								'route'	=>	$route,
								'controller'		=>	static::formatControllerName($classname), 
								'action'			=>	static::formatActionName($method),
								'requirements'	=>	$method_reflection->getAnnotation('Route')->requirements,
								'method'	=>	$method_reflection->getAnnotation('Route')->method,
								'name'	=>	isset($method_reflection->getAnnotation('Route')->name) ? $method_reflection->getAnnotation('Route')->name:null
							);
						}
						if($method_reflection->getAnnotation('Hook')) {
							$hook = $method_reflection->getAnnotation('Hook')->value;
							if($method_reflection->getAnnotation('Priority'))
								$priority = $method_reflection->getAnnotation('Priority')->value;
							else
								$priority = 0;
							$priority *= 1000;
							while(isset(BundlesManager::$hooks_table[$hook][$priority]))
								$priority += 1;

							$controller = static::formatControllerName($classname);
							$action = static::formatActionName($method);
							\Coxis\Core\Controller::hookOn($hook, array($controller, $action));
						}
					}
				}
		}
	}
	
	public static function getBundles() {
		$bundles = array();
		foreach(static::$directories as $dir)
			foreach(glob($dir.'/*') as $bundlepath)
				$bundles[] = $bundlepath;
		return $bundles;
	}
	
	public static function loadBundles() {
		if(\Coxis\Core\Config::get('phpcache')) {
			BundlesManager::$routes = Cache::get('routing/routes');
			BundlesManager::$hooks_table = Cache::get('routing/hooks');
			BundlesManager::$filters_table = Cache::get('routing/filters');
			if(BundlesManager::$routes && BundlesManager::$hooks_table && BundlesManager::$filters_table)
				BundlesManager::$load_routes = false;
			else {
				BundlesManager::$routes = array();
				BundlesManager::$hooks_table = array();
				BundlesManager::$filters_table = array();
			}
		}
		
		$bundles = static::getBundles();
		
		foreach($bundles as $bundle)
			Autoloader::preloadDir($bundle.'/libs');
		foreach($bundles as $bundle)
			static::loadBundle($bundle);
		foreach($bundles as $bundle)
			if(file_exists($bundle.'/bundle.php'))
				include($bundle.'/bundle.php');
			
		if(static::$load_routes) {
			foreach(BundlesManager::$hooks_table as $k=>$v)
				ksort(BundlesManager::$hooks_table[$k]);

			BundlesManager::$filters_table = static::$filters_table;
			
			usort(static::$routes, function($route1, $route2) {
				$route1 = $route1['route'];
				$route2 = $route2['route'];
				
				while(true) {
					if(!$route1)
						return 1;
					if(!$route2)
						return -1;
					$c1 = substr($route1, 0, 1);
					$c2 = substr($route2, 0, 1);
					if($c1 == ':' && $c2 != ':')
						return 1;
					elseif($c1 != ':' && $c2 == ':')
						return -1;
					elseif($c1 != ':' && $c2 != ':'){
						$route1 = substr($route1, 1);
						$route2 = substr($route2, 1);
					}
					elseif($c1 == ':' && $c2 == ':') {
						$route1 = preg_replace('/^:[a-zA-Z0-9_]+/', '', $route1);
						$route2 = preg_replace('/^:[a-zA-Z0-9_]+/', '', $route2);
					}
				}
			});
		}
		
		if(\Coxis\Core\Config::get('phpcache')) {
			Event::addHook('end', function() {
				\Coxis\Core\Cache::set('routing/routes', BundlesManager::$routes);
				\Coxis\Core\Cache::set('routing/hooks', BundlesManager::$hooks_table);
				\Coxis\Core\Cache::set('routing/filters', BundlesManager::$filters_table);
			});
		}
		//~ Router::$routes = BundlesManager::$routes; #todo
		Hook::hooks(BundlesManager::$hooks_table);
		Hook::hooks(BundlesManager::$filters_table);
	}
	
	private static function move_key($key, $pos, &$arr) {
		$before = array_slice($arr, 0, $pos);
		$after = array_slice($arr, $pos);
		$arr = array_merge($before, array($key => $arr[$key]), $after);
	}

	private static function classname($filename) {
		list($classname) = explode('.', basename($filename));
		return $classname;
	}

	private static function formatControllerName($controller) {
		return preg_replace('/Controller$/i', '', $controller);
	}

	private static function formatActionName($action) {
		return preg_replace('/Action$/i', '', $action);
	}

	private static function keypos($key, $array) {
		$i=0;
		foreach($array as $k=>$v) {
			if($k===$key)
				return $i;
			$i++;
		}
		return -1;
	}
}
}