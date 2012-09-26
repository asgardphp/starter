<?php
namespace Coxis\Core;

class URL {
	public static $url = null;

	public static function get() {
		if(!static::$url) {
			if(isset($_SERVER['PATH_INFO']))
				static::$url = $_SERVER['PATH_INFO'];
			elseif(isset($_SERVER['ORIG_PATH_INFO']))
				static::$url = $_SERVER['ORIG_PATH_INFO'];
			elseif(isset($_SERVER['REDIRECT_URL']))
				static::$url = $_SERVER['REDIRECT_URL'];
			else
				static::$url = '';
			static::$url = preg_replace('/^\//', '', static::$url);
		}

		\Coxis\Core\Hook::trigger('path_filter', static::$url);
		
		return static::$url;
	}
	
	public static function current() {
		return static::base().static::get();
	}
	
	public static function full() {
		if(sizeof($_GET)) {
			$r = static::current().'?';
			foreach($_GET as $k=>$v)
				$r .= $k.'&'.$v;
			return $r;
		}
		else
			return static::current();
	}
	
	public static function base() {
		return static::server().'/'.static::root().'/';
	}
	
	public static function to($url) {
		return static::base().$url;
	}
	
	public static function root() {
		if(isset($_SERVER['ORIG_SCRIPT_NAME']))
			$result = dirname($_SERVER['ORIG_SCRIPT_NAME']);
		else
			$result = dirname($_SERVER['SCRIPT_NAME']);
		
		$result = str_replace('\\', '/', $result);
		$result = trim($result, '/');
		//~ $result = '/'.$result.'/';
		$result = str_replace('//', '/', $result);
		
		return $result;
	}
	
	public static function server() {
		if(isset($_SERVER['SERVER_NAME']))
			return 'http://'.trim($_SERVER['SERVER_NAME'], '/');
		else
			return '';
	}

	public static function url_for($what, $params=array(), $relative=true) {
		#controller/action
		if(is_array($what)) {
			$controller = strtolower($what[0]);
			$action = strtolower($what[1]);
			foreach(Router::getRoutes() as $route_params) {
				$route = $route_params['route'];
				if(strtolower($route_params['controller']) == $controller && strtolower($route_params['action']) == $action)
					if($relative)
						return Router::buildRoute($route, $params);
					else
						return static::to(Router::buildRoute($route, $params));
			}
		}
		#route
		else {
			$what = strtolower($what);
			foreach(Router::getRoutes() as $route_params) {
				$route = $route_params['route'];
				if($route_params['name'] != null && strtolower($route_params['name']) == $what)
					if($relative)
						return Router::buildRoute($route, $params);
					else
						return static::to(Router::buildRoute($route, $params));
			}
		}
					
		throw new \Exception('Route not found.');
	}
}