<?php
class CoxisAdmin {
	public static function getModelNameFor($controller) {
		$controller_class = $controller.'Controller';
		$model = $controller_class::$_model;
		
		return $model;
	}
	
	public static function getIndexForController($controller) {
		#todo should get address to indexAction instead of controller prefix..
		try {
			$reflection = new ReflectionAnnotatedClass($controller.'Controller');
			return '/'.$reflection->getAnnotation('Prefix')->value;
		} catch(PHPErrorException $e) {
			throw new Exception('Admin Controller does not exist for model '.$controller);
		}
	}
	
	public static function getIndexFor($model) {
		return static::getIndexForController(static::getAdminControllerFor($model));
	}
	
	public static function getShowFor($model, $id) {
		try {
			return url_for(array(static::getAdminControllerFor($model), 'edit'), array('id'=>$id));
		} catch(PHPErrorException $e) {
			return url_for(array(static::getAdminControllerFor($model), 'index'));
		}
	}
	
	public static function getAdminControllerFor($model) {
		$admin_controller = $model.'Admin';
			return $admin_controller;
	}
	
	public static function url_for_model($model, $action, $params=array()) {
		$controller = static::getAdminControllerFor($model);
		$url = url_for(array($controller, $action), $params, true);
		
		return $url;
	}
}