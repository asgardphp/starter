<?php
namespace Coxis\Core;

class URL {
	public $url = null;
	public $root = null;
	public $server = null;

	public function get() {
		if(!$this->url) {
			if(isset($_SERVER['PATH_INFO']))
				$this->url = $_SERVER['PATH_INFO'];
			elseif(isset($_SERVER['ORIG_PATH_INFO']))
				$this->url = $_SERVER['ORIG_PATH_INFO'];
			elseif(isset($_SERVER['REDIRECT_URL']))
				$this->url = $_SERVER['REDIRECT_URL'];
			else
				$this->url = '';
			$this->url = preg_replace('/^\//', '', $this->url);
		}

		\Hook::trigger('path_filter', $this->url);
		
		return $this->url;
	}
	
	public function setURL($url) {
		return $this->url = $url;
	}
	
	public function setServer($server) {
		return $this->server = $server;
	}
	
	public function setRoot($root) {
		return $this->root = $root;
	}
	
	public function current() {
		return $this->base().$this->get();
	}
	
	public function full() {
		if(sizeof($_GET)) {
			$r = $this->current().'?';
			foreach($_GET as $k=>$v)
				$r .= $k.'&'.$v;
			return $r;
		}
		else
			return $this->current();
	}
	
	public function base() {
		$res = $this->server().'/';
		if($this->root())
			$res .= $this->root().'/';
		return $res;
	}
	
	public function to($url) {
		return $this->base().$url;
	}
	
	public function root() {
		if($this->root !== null)
			$result = $this->root;
		elseif(isset($_SERVER['ORIG_SCRIPT_NAME']))
			$result = dirname($_SERVER['ORIG_SCRIPT_NAME']);
		else
			$result = dirname($_SERVER['SCRIPT_NAME']);
		
		$result = str_replace('\\', '/', $result);
		$result = trim($result, '/');
		//~ $result = '/'.$result.'/';
		$result = str_replace('//', '/', $result);
		
		return $result;
	}
	
	public function server() {
		if($this->server !== null)
			return 'http://'.$this->server;
		elseif(isset($_SERVER['SERVER_NAME']))
			return 'http://'.trim($_SERVER['SERVER_NAME'], '/');
		else
			return '';
	}

	public function url_for($what, $params=array(), $relative=true) {
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
						return $this->to(Router::buildRoute($route, $params));
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
						return $this->to(Router::buildRoute($route, $params));
			}
		}
					
		throw new \Exception('Route not found.');
	}
}