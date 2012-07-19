<?php
namespace {
require_once('vendors/addendum/annotations.php');

/* Controllers */
class Hook extends Annotation {}
class Prefix extends Annotation {}
class Priority extends Annotation {}
class Filter extends Annotation {}
class Route extends Annotation {
	public $name;
	public $requirements;
	public $method;
}
}

namespace Coxis\Core {
class BundlesManager {
	public static $bundles_routes = array();
	public static $filters_table = array();
	public static $routes = array();
	public static $directories = array('app', 'bundles');
	public static $bundles = array();
	public static $load_routes = true;
	
	public static function loadBundle($bundle) {
		\Coxis\Core\Autoloader::preloadDir($bundle.'/models');
		
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
							static::$bundles_routes[] = array(
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
							while(isset(Event::$hooks_table[$hook][$priority]))
								$priority += 1;
							Event::$hooks_table[$hook][$priority] = array('controller'=>static::formatControllerName($classname), 'action'=>static::formatActionName($method));
						}
						if($method_reflection->getAnnotation('Filter')) {
							$filter = $method_reflection->getAnnotation('Filter')->value;
							static::$filters_table[$filter][] = array('controller'=>static::formatControllerName($classname), 'action'=>static::formatActionName($method));
						}
					}
				}
		}
	}
	
	public static function getBundles() {
		foreach(static::$directories as $dir)
			foreach(glob($dir.'/*') as $bundlepath)
				static::$bundles[] = $bundlepath;
	}
	
	public static function loadBundles() {
		static::getBundles();
		
		if(static::$load_routes) {
			BundlesManager::$routes = array();
			Event::$hooks_table = array();
			Event::$filters_table = array();
		}
		
		foreach(static::$bundles as $bundle)
			Autoloader::preloadDir($bundle.'/libs');
		foreach(static::$bundles as $bundle)
			static::loadBundle($bundle);
		foreach(static::$bundles as $bundle)
			if(file_exists($bundle.'/bundle.php'))
				include($bundle.'/bundle.php');
			
		if(static::$load_routes) {
			foreach(Event::$hooks_table as $k=>$v)
				ksort(Event::$hooks_table[$k]);

			Event::$filters_table = static::$filters_table;
			
			$all_routes = static::$bundles_routes;
			
			usort($all_routes, function($route1, $route2) {
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
			
			static::$routes = $all_routes;
		}
		
		if(\Coxis\Core\Config::get('phpcache')) {
			\Coxis\Core\Cache::store('routing/routes', BundlesManager::$routes);
			\Coxis\Core\Cache::store('routing/hooks', Event::$hooks_table);
			\Coxis\Core\Cache::store('routing/filters', Event::$filters_table);
		}
	}
	//TODO: either set all routes or simply give specific routes
	//TODO: Takes routes from CONFIG
	
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