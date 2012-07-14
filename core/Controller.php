<?php
class Controller {
	public function forward404() {
		Response::setCode(404)->send();
	}
	
	public function url_for($action, $params=array(), $relative=false) {
		return url_for(array($this->getControllerName(), $action), $params, $relative);
	}
	
	public function getControllerName() {
		return preg_replace('/Controller$/', '', get_called_class());
	}
	
	protected function removeHook($hook, $controller, $action) {
		//todo
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

	public static function static_filter($filterName, $p, $args=array()) {
		$filters_table = Coxis::get('filters_table');
		
		if(isset($filters_table[$filterName]))
			foreach($filters_table[$filterName] as $filter)
				$p = Router::run($filter['controller'], $filter['action'], array($p, $args));
			
		return $p;
	}

	public function filter($filterName, $p, $args=array()) {
		$filters_table = Coxis::get('filters_table');
		
		if(isset($filters_table[$filterName]))
			foreach($filters_table[$filterName] as $filter)
				$p = Router::run($filter['controller'], $filter['action'], array($p, $args), $this);
			
		return $p;
	}

	public static function static_trigger($hookName, $args=null) {
		if(isset(Coxis::$hooks_table[$hookName]))
			foreach(Coxis::$hooks_table[$hookName] as $hook)
				Router::run($hook['controller'], $hook['action'], $args, null, false);
	}

	public function trigger($hookName, $args=null) {
		if(isset(Coxis::$hooks_table[$hookName]))
			foreach(Coxis::$hooks_table[$hookName] as $hook)
				Router::run($hook['controller'], $hook['action'], $args, $this, false);
	}
	
	public function render($view, $args) {
		return $this->showView($view, $args);
	}
	
	public function setView($view) {
		$this->view = $view;
	}
	
	public function getView() {
		return $this->view;
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
		//TODO: check if component exists. If not, throw exception!
		//TODO: only throw 404 if request is wrong..
		echo Router::run($controller, $action, $args, $this);
	}
	
	private function trigger_show($hookName, $args=null) {
		if(isset(Coxis::$hooks_table[$hookName]))
			foreach(Coxis::$hooks_table[$hookName] as $hook)
				echo Router::run($hook['controller'], $hook['action'], $args, $this);
	}
		
	private function showView($view, $_args) {
		$reflection = new ReflectionObject($this);
		$dir = dirname($reflection->getFileName());
		$_viewfile = $dir.'/../views/'.strtolower(preg_replace('/Controller$/i', '', get_class($this))).'/'.$view;
		
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

	//~ public function redirect($url) {
		//~ Response::redirect($url)->send();
	//~ }
	
	public function _hookAction($request) {
		$controller = strtolower(str_replace('Controller', '', get_class($this)));
		
		$request['_controller'] = $controller;
		
		if(isset(Coxis::$controller_hooks[$controller]))
			foreach(Coxis::$controller_hooks[$controller] as $hook) {
				if(Router::match($hook['route'])) {
					return Router::run($hook['controller'], $hook['action'], $request, $this);
				}
			}
		throw new Exception('Controller hook does not exist!');
	}
	
	//~ todo ajouter l'action a tous les admincontrollers avec sortable
	//~ passer par le controlleur concerne (heritage, configure, ..)
	//~ ':id/promote'	=>	array('Sortable', 'promote')
	
	//OVERRIDE
	public function configure($request){}
}