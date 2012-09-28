<?php
namespace Coxis\Core;

class Router {
	public static $request;
	private static $routes = array();

	public static function addRoute($route) {
		static::$routes[] = $route;
	}

	public static function dispatch($src=null) {
		if(method_exists(static::$request['controller'].'Controller', static::$request['action'].'Action')) {
			$controllerName = ucfirst(strtolower(static::$request['controller']));
			$controllerClassName = $controllerName.'Controller';
			$controller = new $controllerClassName();
			$actionName = static::$request['action'];
			$params = array(static::$request);
			
			static::runAction($controller, 'configure', $params, $src, false);
			\Coxis\Core\Hook::trigger('beforeDispatchAction');
			return static::runAction($controller, $actionName, $params, $src);
		}
		else
			Response::setCode(404)->send();
	}
	
	private static function runAction($controller, $actionName, $params=array(), $src=null, $showView=true) {
		$actionName = strtolower($actionName);
		
		if($src != null)
			foreach($src as $k=>$v)
				$controller->$k = $v;
		
		$result = $controller->run($actionName, $params, $showView);
		
		if($src != null)
			foreach($controller as $k=>$v)
				$src->$k = $v;
		
		return $result;
	}
	
	public static function run($controllerName, $actionName, $params=array(), $src=null, $showView=true) {
		$controllerName = ucfirst($controllerName);
		$controllerClassName = $controllerName.'Controller';
		$controller = new $controllerClassName();
		return static::runAction($controller, $actionName, $params, $src, $showView);
	}

	//todo ADD root /
	public static function formatRoute($route) {
		return '/'.trim($route, '/');
	}
	
	public static function matchWith($route, $with, $requirements=array(), $method=null) {
		$server_method = isset($_SERVER['REQUEST_METHOD']) ? strtolower($_SERVER['REQUEST_METHOD']):'get';
		if($method)
			if(is_array($method)) {
				$good = false;
				foreach($method as $v)
					if(strtolower($server_method) == $v)
						$good = true;
				if(!$good)
					return false;
			}
			elseif(strtolower($method) != $server_method)
				return false;
				
		$regex = static::getRegexFromRoute($route, $requirements);
		$matches = array();
		$res = preg_match_all('/^'.$regex.'(?:\.[a-zA-Z0-9]{1,5})?\/?$/', $with, $matches);
		
		if($res == 0)
			return false;
		else {
			$results = array();
			/* EXTRACTS VARIABLES */
			preg_match_all('/:([a-zA-Z0-9_]+)/', $route, $keys);
			for($i=0; $i<sizeof($keys[1]); $i++)
				$results[$keys[1][$i]] = $matches[$i+1][0];
			
			return $results;
		}
	}
	
	public static function match($route, $requirements=array(), $method=null) {
		$with = static::formatRoute(URL::get());
		return static::matchWith($route, $with, $requirements, $method);
	}
	
	public static function getRegexFromRoute($route, $requirements) {
		preg_match_all('/:([a-zA-Z0-9_]+)/', $route, $symbols);
		$regex = preg_quote($route, '/');
			
		/* REPLACE EACH SYMBOL WITH ITS REGEX */
		foreach($symbols[1] as $symbol) {
			if(is_array($requirements) && array_key_exists($symbol, $requirements)) {
				$requirement = $requirements[$symbol];
				switch($requirement['type']) {
					case 'regex':
						$replacement = $requirement['regex']; break;
					case 'integer':
						$replacement = '[0-9]+'; break;
					//todo int, etc.
				}
			}
			else
				$replacement = '[^\/]+';
			
			$regex = preg_replace('/\\\:'.$symbol.'/', '('.$replacement.')', $regex);
		}
		
		return $regex;
	}

	public static function parseRoutes() {
		$url = URL::get();
		
		static::$request = array(
			'method'=> isset($_SERVER['REQUEST_METHOD']) ? strtolower($_SERVER['REQUEST_METHOD']):'get',
			'controller'=>'',
			'action'=>'',
			'format'=>'html',
		);
		
		/* PARSE ALL ROUTES */
		foreach(static::$routes as $params) {
			$route = $params['route'];
			$requirements = $params['requirements'];
			$method = $params['method'];
			
			/* IF THE ROUTE MATCHES */
			if(($results = static::match($route, $requirements, $method)) !== false) {
				$results = array_merge(array('format'=>'html'), $results, array('data'=>file_get_contents('php://input')));
				$results = array_merge($_GET, $params, $results);
				
				if(!isset($results['action']))
					switch($_SERVER['REQUEST_METHOD']) {
						case 'POST': $results['action'] = 'create'; break;
						case 'GET': $results['action'] = 'show'; break;
						case 'DELETE': $results['action'] = 'destroy'; break;
						case 'PUT': $results['action'] = 'update'; break;
					}
					
				static::$request = $results;
				
				break;
			}
		}
		
		preg_match('/\.([a-zA-Z0-9]{1,5})$/', $url, $matches);
		
		if(isset($matches[1]))
			static::$request['format'] = $matches[1];
			
		return static::$request;
	}
	
	public static function buildRoute($route, $params=array()) {
		foreach($params as $symbol=>$param) {
			$count = 0;
			$route = str_replace(':'.$symbol, $param, $route, $count);
			if($count)
				unset($params[$symbol]);
		}
		if($params)
			$route .= '?';
		$i=0;
		foreach($params as $symbol=>$param) {
			if($i > 0)
				$route .= '&;';
			$route .= urlencode($symbol).'='.urlencode($param);
			$i++;
		}
			
		if(preg_match('/:([a-zA-Z0-9_]+)/', $route))
			throw new \Exception('Missing parameter for route: '.$route);
			
		return trim($route, '/');
	}

	public static function getRequest() {
		return static::$request;
	}
	
	public static function getController() {
		return strtolower(static::$request['controller']);
	}
	
	public static function getAction() {
		return strtolower(static::$request['action']);
	}

	public static function getParam($param) {
		if(isset(static::$request[$param]))
			return strtolower(static::$request[$param]);
		else
			return null;
	}
	
	public static function getRouteFor($what) {
		foreach(static::$routes as $route)
			if($route['controller'] == $what[0] && $route['action'] == $what[1])
				return $route['route'];
	}

	public static function getRoutes() {
		return static::$routes;
	}
}
