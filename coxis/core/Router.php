<?php
namespace Coxis\Core;

class Router {
	public $request;
	protected $routes = array();

	public function setRoutes($routes) {
		$this->routes = $routes;
	}

	public function addRoute($route) {
		$this->routes[] = $route;
	}

	public function dispatch() {
		$this->parseRoutes();
		if(method_exists($this->request['controller'].'Controller', $this->request['action'].'Action')) {
			$controllerName = ucfirst(strtolower($this->request['controller']));
			$actionName = $this->request['action'];
			$params = array($this->request);
			
			return static::run($controllerName, $actionName, $params);
		}
		else
			throw new NotFoundException('Page not found');
	}
	
	public static function run($controllerName, $actionName, $params=array(), $showView=true) {
		$controllerName = ucfirst($controllerName);
		$controllerClassName = $controllerName.'Controller';
		$controller = new $controllerClassName();
		$actionName = $actionName.'Action';

		$controller->action = $actionName;

		\Hook::trigger('controller_configure', array($controller));

		if(method_exists($controller, 'configure'))
			if($response = $controller->run('configure', array(), false))
				return $response;

		if(!$result = $controller->trigger('before', array($controller))) {
			$result = $controller->run($actionName, $params, $showView);
			$controller->trigger('after', array($controller, &$result));
		}

		if(is_string($result))
			\Response::setContent($result);

		return \Response::inst();
	}

	//todo ADD root /
	public static function formatRoute($route) {
		return '/'.trim($route, '/');
	}
	
	public static function matchWith($route, $with, $requirements=array(), $method=null) {
		$server_method = \Request::method();
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
		$with = static::formatRoute(\URL::get());
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

	public function parseRoutes() {
		$this->request = array(
			'method'=> \Request::method(),
			'controller'=>'',
			'action'=>'',
			'format'=>'html',
		);

		$request_key = md5(serialize(array(\Request::method(), \URL::get())));

		$routes = $this->routes;
		$this->request = Cache::get('Router/requests/'.$request_key, function() use($routes) {
			$request = null;
			/* PARSE ALL ROUTES */
			foreach($routes as $params) {
				$route = $params['route'];
				$requirements = $params['requirements'];
				$method = $params['method'];

				/* IF THE ROUTE MATCHES */
				if(($results = \Coxis\Core\Router::match($route, $requirements, $method)) !== false) {
					$results = array_merge(array('format'=>'html'), $results, array('body'=>\Request::body()));
					$results = array_merge(\GET::all(), $params, $results);
					
					if(!isset($results['action']))
						switch(\SERVER::get('REQUEST_METHOD')) {
							case 'POST': $results['action'] = 'create'; break;
							case 'GET': $results['action'] = 'show'; break;
							case 'DELETE': $results['action'] = 'destroy'; break;
							case 'PUT': $results['action'] = 'update'; break;
						}
						
					$request = $results;
					
					break;
				}
			}
		
			preg_match('/\.([a-zA-Z0-9]{1,5})$/', \URL::get(), $matches);
			
			if(isset($matches[1]))
				$request['format'] = $matches[1];

			return $request;
		});
		if(!$this->request)
			throw new \Exception('Route not found!');
			
		return $this->request;
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

	public function getRequest() {
		return $this->request;
	}
	
	public function getController() {
		return strtolower($this->request['controller']);
	}
	
	public function getAction() {
		return strtolower($this->request['action']);
	}

	public function getParam($param) {
		if(isset($this->request[$param]))
			return strtolower($this->request[$param]);
		else
			return null;
	}
	
	public function getRouteFor($what) {
		foreach($this->routes as $route)
			if($route['controller'] == $what[0] && $route['action'] == $what[1])
				return $route['route'];
	}

	public function getRoutes() {
		return $this->routes;
	}
}
