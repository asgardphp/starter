<?php
require_once('vendors/addendum/annotations.php');
class Route extends Annotation {
	public $name;
	public $requirements;
	public $method;
}
/* Controllers */
class Hook extends Annotation {}
class Prefix extends Annotation {}
class Priority extends Annotation {}
/* Models */
class Type extends Annotation {}
class Length extends Annotation {}
class Required extends Annotation {}
class DefaultValue extends Annotation {}
class Filter extends Annotation {}
class Validation extends Annotation {}
class In extends Annotation {}
class Multiple extends Annotation {}
class SetFilter extends Annotation {}
class Editable extends Annotation {}
	
class BundlesManager {
	public static $bundles_routes = array();
	public static $filters_table = array();
	public static $routes = array();
	
	public static function loadBundle($bundle) {
		if(file_exists('bundles/'.$bundle.'/bundle.php'))
			include('bundles/'.$bundle.'/bundle.php');
	
		if(file_exists('bundles/'.$bundle.'/controllers/'))
			foreach(glob('bundles/'.$bundle.'/controllers/*.php') as $filename) {
				include_once($filename);
				$classname = static::classname($filename);
				$reflection = new ReflectionAnnotatedClass($classname);
				
				if($reflection->getAnnotation('Prefix'))
					$prefix = Router::formatRoute($reflection->getAnnotation('Prefix')->value);
				else
					$prefix = '';
				
				$methods = get_class_methods($classname);
				foreach($methods as $method) {
					if(!preg_match('/Action$/i', $method))
						continue;
					$method_reflection = new ReflectionAnnotatedMethod($classname, $method);
				
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
						while(isset(Coxis::$hooks_table[$hook][$priority]))
							$priority += 1;
						Coxis::$hooks_table[$hook][$priority] = array('controller'=>static::formatControllerName($classname), 'action'=>static::formatActionName($method));
					}
					if($method_reflection->getAnnotation('Filter')) {
						$filter = $method_reflection->getAnnotation('Filter')->value;
						static::$filters_table[$filter][] = array('controller'=>static::formatControllerName($classname), 'action'=>static::formatActionName($method));
					}
				}
			}
		
		if(file_exists('bundles/'.$bundle.'/models/'))
			foreach(glob('bundles/'.$bundle.'/models/*.php') as $filename) {
				include_once($filename);
				$model_name = strtolower(static::classname($filename));
				$_properties = array();
				
				$properties = array();
				$a = new ReflectionClass($model_name);
				$props = $a->getProperties();
				
				foreach($props as $prop) {
					if($prop->isStatic())
						continue;
						
					$property = $prop->getName();
					$property_reflection = new ReflectionAnnotatedProperty($model_name, $property);
						
					$_properties[$property] = array();		
					$annotations = $property_reflection->getAnnotations();
					$_properties[strtolower($property)]['type'] = 'text';
					$_properties[strtolower($property)]['required'] = true;
					//~ if($property=='password')
						//~ d($annotations);
					foreach($annotations as $annotation)
						$_properties[strtolower($property)][strtolower(get_class($annotation))] = $annotation->value;
				}
				
				Model::$_properties[$model_name] = $_properties;
				$model_name::loadModel();
				//~ $model_name::loadBehaviors();
				//~ $model_name::loadFiles();
			}
	}
	
	public static function loadBundleLibs($bundle) {
		Coxis::preLoadClasses('bundles/'.$bundle.'/libs/');
	}
	
	public static function loadBundles() {
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
			$bundles = glob('bundles/*');
			$bundles = str_replace('bundles/', '', $bundles);
		}

		foreach($bundles as $k=>$bundle) {
			if(in_array($bundle, $exclusion_bundles_list))
				unset($bundles[$k]);
		}

		foreach($bundles as $bundle)
			static::loadBundleLibs($bundle);
		foreach($bundles as $bundle)
			static::loadBundle($bundle);

		foreach(Coxis::$hooks_table as $k=>$v)
			ksort(Coxis::$hooks_table[$k]);

		Coxis::set('filters_table', static::$filters_table); //todo what's that for ?!

		#TODO
		//~ -comment une action _hook peut retrouver le model correspondant ?
			//~ -
		//~ -matcher la route avec le prefix du controlleur ?
		//~ -retrouver le controlleur correspondant au model ?
		#/

		//~ d(Coxis::$controller_hooks);
		foreach(Coxis::$controller_hooks as $controller=>$hooks) {
			foreach($hooks as $hook) {
				static::$bundles_routes[] = array(//$index.
					'route'	=>	$hook['route'],
					'controller'	=>	$controller,
					'action'	=>	'_hook',
					'requirements'	=>	isset($hook['requirements']) ? $hook['requirements']:null,
					'method'	=>	isset($hook['method']) ? $hook['method']:null,
					'name'	=>	isset($hook['name']) ? $hook['name']:null,
				);
			}
		}
		
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