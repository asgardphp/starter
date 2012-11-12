<?php
namespace {
	require_once('vendors/addendum/annotations.php');

	/* Controllers */
	class Annootate_Hook extends Annotation {}
	class Annootate_Prefix extends Annotation {}
	class Annootate_Priority extends Annotation {}
	class Annootate_Route extends Annotation {
		public $name;
		public $requirements;
		public $method;
	}
	class Annootate_Shortcut extends Annotation {}
}

namespace Coxis\Core {
	abstract class BundlesManager {
		public static function loadBundle($bundle) {
			\Locale::importLocales($bundle.'/locales');

			\Coxis\Core\Context::get('autoloader')->preloadDir($bundle.'/models');

			if(file_exists($bundle.'/controllers/')) {
				\Coxis\Core\Context::get('autoloader')->preloadDir($bundle.'/controllers');
				foreach(glob($bundle.'/controllers/*.php') as $filename)
					\Coxis\Core\Importer::loadClassFile($filename);
			}

			if(file_exists($bundle.'/cli/')) {
				\Coxis\Core\Context::get('autoloader')->preloadDir($bundle.'/cli');
				foreach(glob($bundle.'/cli/*.php') as $filename)
					\Coxis\Core\Importer::loadClassFile($filename);
			}
		}
		
		public static function getBundles($directory = null) {
			if(\Config::get('phpcache') && $bundles=Cache::get('bundlesmanager/bundles'))
				return $bundles;
			else {
				$bundles = array();
				if(!$directory)
					$directories = \Config::get('bundle_directories');
				elseif(is_string($directory))
					$directories = array($directory);
				else
					$directories = $directory;
				foreach($directories as $dir)
					foreach(glob($dir.'/*') as $bundlepath)
						$bundles[] = $bundlepath;
				if(\Config::get('phpcache'))
					Cache::set('bundlesmanager/bundles', $bundles);
				return $bundles;
			}
		}
		
		public static function loadBundles($directory = null) {
			$bundles = static::getBundles($directory);

			if(\Config::get('phpcache') && $bm=Cache::get('bundlesmanager')) {
				#todo only cache locales and preloaded from bundles (might be stuff cached from before bundlesManager)
				$routes = $bm['routes'];
				$hooks = $bm['hooks'];
				\Locale::setLocales($bm['locales']);
				Context::get('autoloader')->preloaded = $bm['preloaded'];
			}
			else {
				foreach($bundles as $bundle)
					\Coxis\Core\Context::get('autoloader')->preloadDir($bundle.'/libs');
				foreach($bundles as $bundle)
					static::loadBundle($bundle);

				list($routes, $hooks) = static::getRoutesAndHooks();

				#Parse cli controllers
				$controllers = get_declared_classes();
				$controllers = array_filter($controllers, function($controller) {
					return is_subclass_of($controller, 'Coxis\Core\CLI\CLIController');
				});
				foreach($controllers as $classname) {
					$r = new \ReflectionClass($classname);
					if(!preg_match('/cli$/', dirname($r->getFileName())))
						continue;

					$reflection = new \ReflectionAnnotatedClass($classname);
					
					foreach(get_class_methods($classname) as $method) {
						if(!preg_match('/Action$/i', $method))
							continue;
						$method_reflection = new \ReflectionAnnotatedMethod($classname, $method);
					
						if($v = $method_reflection->getAnnotation('Shortcut'))
							\CLIRouter::addRoute($v->value, array(static::formatControllerName($classname), static::formatActionName($method)));
					}
				}

				static::sortRoutes($routes);
					
				if(\Config::get('phpcache')) {
					\Coxis\Core\Cache::set('bundlesmanager', array(
						'routes'	=>	$routes,
						'hooks'	=>	$hooks,
						'preloaded'	=>	Context::get('autoloader')->preloaded,
						'locales'	=>	\Locale::getLocales(),
					));
				}
			}

			foreach($routes as $route)
				\Router::addRoute($route);
			foreach($hooks as $name=>$hooks)
				foreach($hooks as $hook)
					\Coxis\Core\Controller::hookOn($name, $hook);

			foreach($bundles as $bundle)
				if(file_exists($bundle.'/bundle.php'))
					include($bundle.'/bundle.php');
		}

		public static function sortRoutes(&$routes) {
			usort($routes, function($route1, $route2) {
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

		public static function loadModelFixtures($bundle_path) {
			if(file_exists($bundle_path.'/data')) {
				foreach(glob($bundle_path.'/data/*.models.yml') as $file) {
					ORMManager::loadModelFixtures($file);
				}
			}

			// $yaml = new sfYamlParser();
			// if(file_exists($bundle_path.'/data')) {
			// 	foreach(glob($bundle_path.'/data/*') as $file) {
			// 		$raw = $yaml->parse(file_get_contents($file));
			// 		foreach($raw as $class=>$all)
			// 			foreach($all as $one) 
			// 				$class::create($one);
			// 	}
			// }
		}

		public static function loadModelFixturesAll() {
			foreach(static::getBundles() as $bundle)
				static::loadModelFixtures($bundle);
		}

		public static function getRoutesAndHooks($directory = false) {
			$routes = array();
			$hooks = array();

			#Parse controllers routes/hooks
			$controllers = get_declared_classes();
			$controllers = array_filter($controllers, function($controller) {
				return is_subclass_of($controller, 'Coxis\Core\Controller');
			});
			foreach($controllers as $classname) {
				$r = new \ReflectionClass($classname);
				if(!preg_match('/controllers$/', dirname($r->getFileName())))
					continue;
				if($directory)
					if(strpos($r->getFileName(), realpath($directory)) !== 0)
						continue;

				$reflection = new \ReflectionAnnotatedClass($classname);
				
				if($reflection->getAnnotation('Prefix'))
					$prefix = \Router::formatRoute($reflection->getAnnotation('Prefix')->value);
				else
					$prefix = '';
				
				$methods = get_class_methods($classname);
				foreach($methods as $method) {
					if(!preg_match('/Action$/i', $method))
						continue;
					$method_reflection = new \ReflectionAnnotatedMethod($classname, $method);
				
					if($method_reflection->getAnnotation('Route')) {
						$route = \Router::formatRoute($prefix.'/'.$method_reflection->getAnnotation('Route')->value);

						$routes[] = array(
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
						$hooks[$hook][] = array($controller, $action);
					}
				}
			}

			return array($routes, $hooks);
		}

		public static function getRoutesFromDirectory($directory) {
			static::loadBundles($directory);
			list($routes) = static::getRoutesAndHooks($directory);
			return $routes;
		}
	}
}
