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
}

namespace Coxis\Core {
	class BundlesManager {
		public $directories = array('bundles', 'app');
		private $routes = array();
		private $hooks = array();
		
		public function loadBundle($bundle) {
			\Coxis\Core\Tools\Locale::importLocales($bundle.'/locales');

			\Coxis\Core\Context::get('autoloader')->preloadDir($bundle.'/models');

			\Coxis\Core\Context::get('autoloader')->preloadDir($bundle.'/controllers');
			if(file_exists($bundle.'/controllers/')) {
				foreach(glob($bundle.'/controllers/*.php') as $filename) {
					\Coxis\Core\Importer::loadClassFile($filename);
				}
			}
		}
		
		public function getBundles() {
			if(\Config::get('phpcache') && $bundles=Cache::get('bundlesmanager/bundles'))
				return $bundles;
			else {
				$bundles = array();
				foreach($this->directories as $dir)
					foreach(glob($dir.'/*') as $bundlepath)
						$bundles[] = $bundlepath;
				if(\Config::get('phpcache'))
					Cache::set('bundlesmanager/bundles', $bundles);
				return $bundles;
			}
		}
		
		public function loadBundles() {
			$bundles = static::getBundles();

			if(\Config::get('phpcache') && $bm=Cache::get('bundlesmanager')) {
				#todo only cache locales and preloaded from bundles (might be stuff cached from before bundlesManager)
				$this->routes = $bm['routes'];
				$this->hooks = $bm['hooks'];
				\Locale::$locales = $bm['locales'];
				Context::get('autoloader')->preloaded = $bm['preloaded'];
			}
			else {
				foreach($bundles as $bundle)
					\Coxis\Core\Context::get('autoloader')->preloadDir($bundle.'/libs');
				foreach($bundles as $bundle)
					static::loadBundle($bundle);

				#Parse controllers routes/hooks
				$controllers = get_declared_classes();
				$controllers = array_filter($controllers, function($controller) {
					return is_subclass_of($controller, 'Coxis\Core\Controller');
				});
				foreach($controllers as $classname) {
					$r = new \ReflectionClass($classname);
					if(!preg_match('/controllers$/', dirname($r->getFileName())))
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

							$this->routes[] = array(
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
							$this->hooks[$hook][] = array($controller, $action);
						}
					}
				}

				$this->sortRoutes();
					
				if(\Config::get('phpcache')) {
					\Coxis\Core\Cache::set('bundlesmanager', array(
						'routes'	=>	$this->routes,
						'hooks'	=>	$this->hooks,
						'preloaded'	=>	Context::get('autoloader')->preloaded,
						'locales'	=>	\Locale::$locales,
					));
				}
			}

			foreach($this->routes as $route)
				\Router::addRoute($route);
			foreach($this->hooks as $name=>$hooks)
				foreach($hooks as $hook)
					\Coxis\Core\Controller::hookOn($name, $hook);

			foreach($bundles as $bundle)
				if(file_exists($bundle.'/bundle.php'))
					include($bundle.'/bundle.php');
		}

		public function sortRoutes() {
			usort($this->routes, function($route1, $route2) {
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
