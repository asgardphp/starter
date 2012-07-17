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
	public static $directories = array('bundles');
	
	public static function loadBundle($bundle) {
		if(file_exists($bundle.'/bundle.php'))
			include($bundle.'/bundle.php');

		Autoloader::preloadDir($bundle.'/models');
	
		if(file_exists($bundle.'/controllers/'))
			foreach(glob($bundle.'/controllers/*.php') as $filename) {
				$classname = Importer::loadClassFile($filename);
				
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
	
	public static function loadBundleLibs($bundle) {
		Autoloader::preloadDir($bundle.'/libs');
	}
	
	public static function loadBundles() {
		foreach(static::$directories as $dir)
			static::loadBundlesDir($dir);
	}
	
	public static function loadBundlesDir($dir) {
		//TODO: either set all routes or simply give specific routes
		//TODO: Takes routes from CONFIG
		$routes = Config::get('routes');
		if(!$routes)	$routes = array();

		foreach($routes as $route=>$params) {
			unset($routes[$route]);
			$routes[Router::formatRoute($route)] = $params;
		}
		
		$inclusion_bundles_list = array();
		$exclusion_bundles_list = array();
		if(isset($inclusion_bundles_list) && sizeof($inclusion_bundles_list)>0)
			$bundles = $inclusion_bundles_list;
		else {
			$bundles = glob($dir.'/*');
			$bundles = str_replace($dir.'/', '', $bundles);
		}

		foreach($bundles as $k=>$bundle) {
			if(in_array($bundle, $exclusion_bundles_list))
				unset($bundles[$k]);
		}

		foreach($bundles as $bundle)
			static::loadBundleLibs($dir.'/'.$bundle);
		foreach($bundles as $bundle)
			static::loadBundle($dir.'/'.$bundle);

		foreach(Event::$hooks_table as $k=>$v)
			ksort(Event::$hooks_table[$k]);

		Event::$filters_table = static::$filters_table;
		
		$all_routes = array_merge(static::$bundles_routes, $routes);
		
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

		$route_paths = array_keys($routes);
		for($i=sizeof($route_paths)-2; $i>=0; $i--)
			if(keypos($route_paths[$i], $all_routes) > keypos($route_paths[$i+1], $all_routes))
				move_key($route_paths[$i], keypos($route_paths[$i+1], $all_routes), $all_routes);

		foreach($bundles as $bundle) {
			$bundleclass = $bundle.'Bundle';
			if(class_exists($bundleclass, false))
				$bundleclass::configure();
		}
		
		static::$routes = $all_routes;
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