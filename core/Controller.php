<?php
namespace Coxis\Core;

class Controller {
	public function forward404() {
		Response::setCode(404)->send();
	}
	
	public function url_for($action, $params=array(), $relative=false) {
		return url_for(array($this->getControllerName(), $action), $params, $relative);
	}
	
	public static function getControllerName() {
		return preg_replace('/Controller$/', '', get_called_class());
	}
	
	public function canonical($canonical, $relative=true, $redirect=true) {
		if($relative)
			$uri = URL::get();
		else
			$uri = URL::current();
		
		if($redirect && $canonical != $uri)
			Response::setCode(301)->redirect($canonical, $relative)->send();
		if($relative)
			HTML::code('<link rel="canonical" href="'.URL::to($canonical).'">');
		else
			HTML::code('<link rel="canonical" href="'.$canonical.'">');
	}

	public function run($action, $params, $showView=false) {
		$this->view = $action.'.php';
		if(($actionName=$action) != 'configure')
			$actionName = $action.'Action';
		
		ob_start();
		$result = $this->$actionName($params);
		$controllerBuffer =  ob_get_clean();
		
		if($controllerBuffer)
			return $controllerBuffer;
		elseif(!$showView)
			return null;
		elseif($result!==null)
			return $result;
		elseif($this->view)
			return $this->showView($this->getView(), $this);
		return null;
	}
	
	private function component($controller, $action, $args=array()) {
		echo Router::run($controller, $action, $args, $this);
	}
	
	public function setView($view) {
		$this->view = $view;
	}
	
	public function getView() {
		return $this->view;
	}
		
	private function showView($view, $_args) {
		$reflection = new \ReflectionObject($this);
		$dir = dirname($reflection->getFileName());
		$_viewfile = $dir.'/../views/'.strtolower(preg_replace('/Controller$/i', '', basename(get_class($this)))).'/'.$view;
		
		unset($dir);
		unset($reflection);
		
		foreach($_args as $_key=>$_value)
			$$_key = $_value;#TODO, watchout keywords
		
		ob_start();
		Coxis::set('in_view', true);
		include($_viewfile);
		Coxis::set('in_view', false);
		return ob_get_clean();
	}
	
	public function render($view, $args) {
		return $this->showView($view, $args);
	}
	
	#todo deprecated
	public function _hookAction($request) {
		$controller = strtolower(str_replace('Controller', '', get_class($this)));
		
		$request['_controller'] = $controller;
		
		if(isset(Coxis::$controller_hooks[$controller]))
			foreach(Coxis::$controller_hooks[$controller] as $hook) {
				if(Router::match($hook['route'])) {
					return Router::run($hook['controller'], $hook['action'], $request, $this);
				}
			}
		throw new \Exception('Controller hook does not exist!');
	}
	
	//OVERRIDE
	public function configure($request){}
}