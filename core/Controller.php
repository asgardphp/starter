<?php
namespace Coxis\Core;

class Controller {
	public function forward404($msg = 'Not found') {
		throw new ControllerException($msg, \Response::setCode(404));
	}
	
	public static function url_for($action, $params=array(), $relative=false) {
		return \URL::url_for(array(static::getControllerName(), $action), $params, $relative);
	}
	
	public static function getControllerName() {
		return preg_replace('/Controller$/', '', get_called_class());
	}
	
	public function canonical($canonical, $relative=true, $redirect=true) {
		if($relative)
			$uri = \URL::get();
		else
			$uri = \URL::current();
		
		if($redirect && $canonical != $uri)
			throw new ControllerException('Page not found', \Response::setCode(301)->redirect($canonical, $relative));
		if($relative)
			HTML::code('<link rel="canonical" href="'.\URL::to($canonical).'">');
		else
			HTML::code('<link rel="canonical" href="'.$canonical.'">');
	}

	public static function hookOn($hookName, $cAction) {
		$cAction = array_values($cAction);
		$controller = $cAction[0];
		$action = $cAction[1];
		\Hook::hookOn($hookName, function($chain, $arg1=null, $arg2=null, $arg3=null, $arg4=null,
			$arg5=null, $arg6=null, $arg7=null, $arg8=null, $arg9=null, $arg10=null) use($controller, $action) {
			$args = array(&$arg1, &$arg2, &$arg3, &$arg4, &$arg5, &$arg6, &$arg7, &$arg8, &$arg9, &$arg10);
			return Router::run($controller, $action, $args);
		});
	}

	public function run($action, $params=array(), $showView=true) {
		$this->view = null;
		if(($actionName=$action) != 'configure')
			$actionName = $action.'Action';
	
		if(!is_array($params))
			$params = array($params);

		ob_start();
		$result = call_user_func_array(array($this, $actionName), $params);
		$controllerBuffer =  ob_get_clean();

		if($result!==null) {
			if($result instanceof \Coxis\Core\Model) {
				\Response::setHeader('Content-Type', 'application/json');
				return \Response::setContent(json_encode($result->toArray()));
			}
			elseif(is_array($result)) {
				\Response::setHeader('Content-Type', 'application/json');
				return \Response::setContent(json_encode(Tools::modelsToArray($result)));
			}
			else
				return $result;
		}
		elseif($controllerBuffer)
			return $controllerBuffer;
		elseif(!$showView) #todo still necessary?
			return null;
		elseif($this->view !== false) {
			if($this->view === null)
				if(!$this->setRelativeView($action.'.php'))
					return null;
			return $this->showView($this->view, $this);
		}
		return null;
	}
	
	protected function component($controller, $action, $args=array()) {
		echo Router::run($controller, $action, $args);
	}

	public function noView() {
		$this->view = false;
	}
	
	public function setRelativeView($view) {
		$reflection = new \ReflectionObject($this);
		$dir = dirname($reflection->getFileName());
		$this->setView($dir.'/../views/'.strtolower(preg_replace('/Controller$/i', '', Importer::basename(get_class($this)))).'/'.$view);
		return file_exists($dir.'/../views/'.strtolower(preg_replace('/Controller$/i', '', Importer::basename(get_class($this)))).'/'.$view);
	}
	
	public function setView($view) {
		$this->view = $view;
	}
		
	protected function showView($_viewfile, $_args) {
		return View::render($_viewfile, $_args);
	}
	
	public function render($view, $args=array()) {
		$reflection = new \ReflectionObject($this);	
		$dir = dirname($reflection->getFileName());
		return $this->showView($dir.'/../views/'.strtolower(preg_replace('/Controller$/i', '', Importer::basename(get_class($this)))).'/'.$view, $args);
	}
}