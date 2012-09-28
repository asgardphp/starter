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
		public static $directories = array('bundles', 'app');
		public static $load_routes = true;
		private static $routes = array();
		private static $hooks = array();
		
		public static function loadBundle($bundle) {
			\Coxis\Core\Autoloader::preloadDir($bundle.'/models');
			
			if(Coxis::get('load_locales'))
				Locale::importLocales($bundle.'/locales');	

			if(!static::$load_routes)
				\Coxis\Core\Autoloader::preloadDir($bundle.'/controllers');
			else {
				if(file_exists($bundle.'/controllers/')) {
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
								#todo priority
								// if($method_reflection->getAnnotation('Priority'))
								// 	$priority = $method_reflection->getAnnotation('Priority')->value;
								// else
								// 	$priority = 0;
								// $priority *= 1000;
								// while(isset(BundlesManager::$hooks_table[$hook][$priority]))
								// 	$priority += 1;

								$controller = static::formatControllerName($classname);
								$action = static::formatActionName($method);
								static::$hooks[$hook][] = array($controller, $action);
							}
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
				static::$routes = Cache::get('routing/routes', array());
				static::$hooks = Cache::get('routing/hooks', array());
				if(static::$routes && static::$hooks)
					static::$load_routes = false;
			}
			
			$bundles = static::getBundles();
			
			foreach($bundles as $bundle)
				Autoloader::preloadDir($bundle.'/libs');
			foreach($bundles as $bundle)
				static::loadBundle($bundle);
			foreach($bundles as $bundle)
				if(file_exists($bundle.'/bundle.php'))
					include($bundle.'/bundle.php');
			
			foreach(static::$routes as $route)
				Router::addRoute($route);
			foreach(static::$hooks as $name=>$hooks)
				foreach($hooks as $hook)
					\Coxis\Core\Controller::hookOn($name, $hook);

			if(static::$load_routes)
				static::sortRoutes();
			if(\Coxis\Core\Config::get('phpcache')) {
				\Coxis\Core\Cache::set('routing/routes', static::$routes);
				\Coxis\Core\Cache::set('routing/hooks', static::$hooks);
			}
		}

		public static function sortRoutes() {
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
					elseif($c1 != ':' && $c2 != ':') {
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

		private static function formatControllerName($controller) {
			return preg_replace('/Controller$/i', '', $controller);
		}

		private static function formatActionName($action) {
			return preg_replace('/Action$/i', '', $action);
		}
	}
}
