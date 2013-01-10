<?php
namespace {
	require_once('vendor/addendum/annotations.php');

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
	class Annootate_Usage extends Annotation {}
	class Annootate_Description extends Annotation {}
}

namespace Coxis\Core {
	abstract class BundlesManager {
		public static function loadBundle($bundle) {
			$bundle_load = array();
			if(file_exists($bundle.'/coxis_load.php'))
				$bundle_load = require $bundle.'/coxis_load.php';
			if(file_exists($bundle.'/../coxis_dirload.php'))
				$bundle_load = array_merge($bundle_load, require $bundle.'/../coxis_dirload.php');
			if(isset($bundle_load['type']))
				$bundle_type = $bundle_load['type'];
			else
				$bundle_type = null;

			if($bundle_type == 'mvc') {
				\Locale::importLocales($bundle.'/locales');

				Autoloader::preloadDir($bundle.'/models');

				if(file_exists($bundle.'/hooks/')) {
					Autoloader::preloadDir($bundle.'/hooks');
					foreach(glob($bundle.'/hooks/*.php') as $filename)
						\Coxis\Core\Importer::loadClassFile($filename);
				}

				if(file_exists($bundle.'/controllers/')) {
					Autoloader::preloadDir($bundle.'/controllers');
					foreach(glob($bundle.'/controllers/*.php') as $filename)
						\Coxis\Core\Importer::loadClassFile($filename);
				}

				if(file_exists($bundle.'/cli/')) {
					Autoloader::preloadDir($bundle.'/cli');
					foreach(glob($bundle.'/cli/*.php') as $filename)
						\Coxis\Core\Importer::loadClassFile($filename);
				}
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
					foreach(glob(_DIR_.$dir.'/*') as $bundlepath)
						$bundles[] = $bundlepath;
				if(\Config::get('phpcache'))
					Cache::set('bundlesmanager/bundles', $bundles);
				return $bundles;
			}
		}
		
		public static function loadBundles($directory = null) {
			Profiler::checkpoint('loadBundles 1');
			$bundles = static::getBundles($directory);
			Profiler::checkpoint('loadBundles 2');

			if(\Config::get('phpcache') && $bm=Cache::get('bundlesmanager')) {
				\Router::setRoutes($bm['routes']);
				\Coxis\Core\Controller::addHooks($bm['hooks']);
				\Locale::setLocales($bm['locales']);
				Autoloader::$preloaded = $bm['preloaded'];
			}
			else {
				foreach($bundles as $bundle)
					Autoloader::preloadDir($bundle.'/libs');
				foreach($bundles as $bundle)
					static::loadBundle($bundle);

				$routes = static::getRoutes();
				$hooks = static::getHooks();

				static::sortRoutes($routes);
				\Router::setRoutes($routes);

				foreach($routes as $route)
					\Router::addRoute($route);
				
				foreach($hooks as $name=>$subhooks)
					foreach($subhooks as $hook)
						\Coxis\Core\HooksContainer::addHook($name, $hook);

				if(\Config::get('phpcache')) {
					Cache::set('bundlesmanager', array(
						'routes' => $routes,
						'hooks' => $hooks,
						'locales' => \Locale::getLocales(),
						'preloaded' => Autoloader::$preloaded,
					));
				}
			}
			Profiler::checkpoint('loadBundles 3');

			foreach($bundles as $bundle)
				if(file_exists($bundle.'/bundle.php'))
					include($bundle.'/bundle.php');
			Profiler::checkpoint('loadBundles 4');
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

		public static function loadModelFixtures($bundle_path) {
			if(file_exists($bundle_path.'/data'))
				foreach(glob($bundle_path.'/data/*.models.yml') as $file)
					ORMManager::loadModelFixtures($file);
		}

		public static function loadModelFixturesAll() {
			foreach(static::getBundles() as $bundle)
				static::loadModelFixtures($bundle);
		}

		public static function getRoutes($directory = false) {
			$routes = array();

			$controllers = get_declared_classes();
			$controllers = array_filter($controllers, function($controller) {
				return is_subclass_of($controller, 'Coxis\Core\Controller');
			});
			foreach($controllers as $classname) {
				$r = new \ReflectionClass($classname);
				if(!$r->isInstantiable())
					continue;
				if($directory)
					if(strpos($r->getFileName(), realpath($directory)) !== 0)
						continue;

				$routes = array_merge($routes, $classname::fetchRoutes());
			}

			return $routes;
		}

		public static function getHooks($directory = false) {
			$hooks = array();

			$controllers = get_declared_classes();
			$controllers = array_filter($controllers, function($controller) {
				return is_subclass_of($controller, 'Coxis\Core\HooksContainer');
			});
			foreach($controllers as $classname) {
				$r = new \ReflectionClass($classname);
				if(!$r->isInstantiable())
					continue;
				if($directory)
					if(strpos($r->getFileName(), realpath($directory)) !== 0)
						continue;

				$hooks = array_merge_recursive($hooks, $classname::fetchHooks());
			}

			return $hooks;
		}

		public static function getRoutesFromDirectory($directory) {
			static::loadBundles($directory);
			list($routes) = static::getRoutesAndHooks($directory);
			return $routes;
		}
	}
}
