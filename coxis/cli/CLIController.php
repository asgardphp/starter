<?php
namespace Coxis\Cli;

abstract class CLIController {
	public function run($action, $params=array(), $showView=false) {
		$this->view = $action.'.php';
		if(($actionName=$action) != 'configure')
			$actionName = $action.'Action';
		
		if(!method_exists($this, $actionName))
			FrontController::usage();
		$result = $this->$actionName($params);
	}
	
	public static function getControllerName() {
		return preg_replace('/Controller$/', '', get_called_class());
	}
	
	//OVERRIDE
	public function configure($request){}
}