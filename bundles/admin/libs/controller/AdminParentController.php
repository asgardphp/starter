<?php
abstract class AdminParentController extends Controller {
	static $_model = null;#todo model variable name or model name ?
	static $_models = null;
	static $_index = null;
	static $_messages = null;
	static $__messages = array(
		'modified'			=>	'Elément mis à jour avec succès.',
		'created'				=>	'Elément créée avec succès.',
		'many_deleted'	=>	'%s éléments supprimés.',
		'deleted'				=>	'Elément supprimé avec succès.',
		'unexisting'			=>	'Cet élément n\'existe pqs.',
	);
	protected static $_hooks = array();
	
	function __construct() {
		if(static::$_models == null)
			static::$_models = static::$_model.'s';
		if(isset(static::$_messages))
			static::$_messages = array_merge(static::$__messages, static::$_messages);
		else
			static::$_messages = static::$__messages;
		static::$_index = static::$_index ? static::$_index:static::$_models;
	}

	public function configure($request) {
		Coxis::set('layout', array('Admin', 'layout'));
		if(!User::getId() || User::getRole()!='admin') {
			$_SESSION['redirect_to'] = URL::full();
			Response::setCode(401)->redirect('admin/login', true)->send();
		}
	}
	
	public static function addHook($hook) {
		static::$_hooks[] = $hook;
		
		$hook['route'] = str_replace(':route', $hook['route'], Router::getRouteFor(array(static::getControllerName(), 'hooks')));
		$hook['controller'] = static::getControllerName();
		$hook['action'] = 'hooks';
		BundlesManager::$routes[] = $hook;
	}
	
	/**
	@Route(value = 'hooks/:route', requirements = {
		route = {
			type = 'regex',
			regex = '.+'
		}	
	})
	*/
	public function hooksAction($request) {
		$modelName = static::$_model;
		$modelName::init();
		
		$controller = static::getControllerName();
		
		#todo sort hooks routes
		foreach(static::$_hooks as $hook) {
			if($results = Router::matchWith($hook['route'], $request['route'])) {
				$request = array_merge($request, $results);
				$request['_controller'] = $controller;
				$request['controller'] = $hook['controller'];
				$request['action'] = $hook['action'];
				return Router::run($hook['controller'], $hook['action'], $request, $this);
			}
		}
		throw new Exception('Controller hook does not exist!');
	}
	
	/**
	@Route(':id/deletefile/:file')
	*/
	public function deleteFileAction($request) {
		$_model = static::$_model;
		
		if(!($this->$_model = $_model::load($request['id'])))
			$this->forward404();
			
		$this->$_model->deleteFile($request['file']);
		Messenger::addSuccess('Fichier supprimé avec succès.');
		Response::redirect(CoxisAdmin::getShowFor($_model, $this->$_model->id))->send();
	}
}