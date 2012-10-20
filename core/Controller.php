<?php
namespace Coxis\Core;

class Controller {
	public function forward404() {
		Response::setCode(404)->send();
	}
	
	public static function url_for($action, $params=array(), $relative=false) {
		return url_for(array(static::getControllerName(), $action), $params, $relative);
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

	public static function hookOn($hookName, $cAction) {
		$cAction = array_values($cAction);
		$controller = $cAction[0];
		$action = $cAction[1];
		\Hook::hookOn($hookName, function($chain, $arg1=null, $arg2=null, $arg3=null, $arg4=null,
			$arg5=null, $arg6=null, $arg7=null, $arg8=null, $arg9=null, $arg10=null) use($controller, $action) {
		// \Hook::hookOn($hookName, function($chain, $args=null) use($controller, $action) {
			// if(!is_array($args))
			// 	$args = array($args);
			// if($action == 'preSending')
			// d($controller, $action, $args);
			$args = array(&$arg1, &$arg2, &$arg3, &$arg4, &$arg5, &$arg6, &$arg7, &$arg8, &$arg9, &$arg10);
			Router::run($controller, $action, $args);
		});
	}

	public function run($action, $params=array(), $showView=false) {
		$this->view = null;
		if(($actionName=$action) != 'configure')
			$actionName = $action.'Action';
	
		if(!is_array($params))
			$params = array($params);

		ob_start();
		$result = call_user_func_array(array($this, $actionName), $params);
		$controllerBuffer =  ob_get_clean();

		if($controllerBuffer)
			return $controllerBuffer;
		elseif(!$showView)
			return null;
		elseif($result!==null)
			return $result;
		elseif($this->view !== false) {
			if($this->view === null)
				$this->setRelativeView($action.'.php');
			return $this->showView($this->view, $this);
		}
		return null;
	}
	
	private function component($controller, $action, $args=array()) {
		echo Router::run($controller, $action, $args, $this);
	}

	public function noView() {
		$this->view = false;
	}
	
	public function setRelativeView($view) {
		$reflection = new \ReflectionObject($this);
		$dir = dirname($reflection->getFileName());
		$this->setView($dir.'/../views/'.strtolower(preg_replace('/Controller$/i', '', Importer::basename(get_class($this)))).'/'.$view);
	}
	
	public function setView($view) {
		$this->view = $view;
	}
		
	private function showView($_viewfile, $_args) {
		if(!file_exists($_viewfile))
			return null;

		foreach($_args as $_key=>$_value)
			$$_key = $_value;#TODO, watchout keywords

		ob_start();
		\Memory::set('in_view', true);
		include($_viewfile);
		\Memory::set('in_view', false);
		return ob_get_clean();
	}
	
	public function render($view, $args) {
		return $this->showView($view, $args);
	}
	
	#todo deprecated
	public function _hookAction($request) {
		$controller = strtolower(str_replace('Controller', '', get_class($this)));
		
		$request['_controller'] = $controller;
		
		if(isset(\Memory::$controller_hooks[$controller]))
			foreach(\Memory::$controller_hooks[$controller] as $hook) {
				if(Router::match($hook['route'])) {
					return Router::run($hook['controller'], $hook['action'], $request, $this);
				}
			}
		throw new \Exception('Controller hook does not exist!');
	}
	
	//OVERRIDE
	public function configure($request){}
}