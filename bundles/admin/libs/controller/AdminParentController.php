<?php
namespace Coxis\Bundles\Admin\Libs\Controller;

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
		'unexisting'			=>	'Cet élément n\'existe pas.',
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
		throw new \Exception('Controller hook does not exist!');
	}
	
	/**
	@Route(':id/deletefile/:file')
	*/
	public function deleteSingleFileAction($request) {
		$_model = static::$_model;
		
		if(!($this->$_model = $_model::load($request['id'])))
			$this->forward404();
			
		$this->$_model->deleteFile($request['file']);
		Messenger::addSuccess('Fichier supprimé avec succès.');
		Response::redirect(CoxisAdmin::getShowFor($_model, $this->$_model->id))->send();
	}
	
	/**
	@Route(':id/:file/add')
	*/
	public function addFileAction($request) {
		//~ $modelName = CoxisAdmin::getModelNameFor($request['_controller']);
		$modelName = static::$_model;;
		if(!($model = $modelName::load($request['id'])))
			$this->forward404();
		if(!$model->fileExists($request['file']))
			$this->forward404();
			
		try {
			if(isset($_FILES['Filedata']))
				$files = array($request['file'] => $_FILES['Filedata']);
			else
				Response::setCode(500)->setContent('Erreur lors de l\'envoi.')->send();
				
			$model->setFiles($files)->save();
			$final_paths = $model->getFilePath($request['file']);
			$response = array(
				'url' => array_pop($final_paths),
				'deleteurl' => $this->url_for('deleteFile', array('id' => $model->id, 'pos' => sizeof($final_paths)+1, 'file' => $request['file'])),
			);
			Response::setCode(200)->setContent(json_encode($response))->send();
		} catch(\Exception $e) {
			Response::setCode(500)->setContent('Erreur lors de l\'envoi.')->send();
		}
	}
	
	/**
	@Route(':id/:file/delete/:pos')
	*/
	public function deleteFileAction($request) {
		//~ $modelName = CoxisAdmin::getModelNameFor($request['_controller']);
		$modelName = static::$_model;
		if(!($model = $modelName::load($request['id'])))
			$this->forward404();
		if(!$model->fileExists($request['file']))
			$this->forward404();
			
		$paths = $model->getFilePath($request['file']);

		if(!isset($paths[$request['pos']-1]))
			Response::redirect($this->url_for('edit', array('id' => $model->id)), false)->setCode(404)->send();

		$path = $paths[$request['pos']-1];
		
		$rawpaths = $model->getRawFilePath($request['file']);
		unset($rawpaths[$request['pos']-1]);
		$rawpaths = array_values($rawpaths);
		
		try {
			$model->setRawFilePath($request['file'], $rawpaths)->save(null, true);
			Messenger::addSuccess('Fichier supprimé avec succès.');
			FileManager::unlink(_WEB_DIR_.'/'.$path);
		} catch(\Exception $e) {
			Messenger::addError('Il y a eu une erreur avec le fichier');
		}
		
		try {
			Response::redirect($this->url_for('edit', array('id' => $model->id)), false)->send();
		} catch(\Exception $e) {
			Response::redirect($this->url_for('index'), false)->send();
		}
	}
}