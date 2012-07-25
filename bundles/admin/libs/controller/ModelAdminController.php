<?php
namespace Coxis\Bundles\Admin\Libs\Controller;

class ModelAdminController extends AdminParentController {
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
		#trigger the model behaviors coxisadmin hook
		$model = static::$_model;
		$model_behaviors = $model::$behaviors;
		foreach($model_behaviors as $behavior => $params)
			if($params)
				Event::trigger('behaviors_coxisadmin_'.$behavior, static::getControllerName());
		
		if(static::$_models == null)
			static::$_models = basename(strtolower(static::$_model.'s'));
		if(isset(static::$_messages))
			static::$_messages = array_merge(static::$__messages, static::$_messages);
		else
			static::$_messages = static::$__messages;
		static::$_index = static::$_index ? static::$_index:basename(static::$_models);
	}
	
	public static function getModel() {
		return static::$_model;
	}
	
	public static function getIndexURL() {
		return static::url_for('index');
	}
	
	public static function getEditURL($id) {
		return static::url_for('edit', array('id'=>$id));
	}
	
	/**
	@Route('')
	*/
	public function indexAction($request) {
		$_model = static::$_model;
		$_models = static::$_models;
		
		$this->searchForm = new \Coxis\Core\Form\Form();
		$this->searchForm->search = new \Coxis\Core\Form\Widget();
	
		//submitted
		$i = 0;
		if(count($_POST)>1 && isset($_POST['action']) && $_POST['action']=='delete') {
			foreach($_POST['id'] as $id)
				$i += $_model::destroyOne($id);
		
			Flash::addSuccess(sprintf(static::$_messages['many_deleted'], $i));
		}
		
		$conditions = array();
		
		#Search
		if(isset($request['search']) && $request['search']) {
			$conditions['or'] = array();
			foreach($_model::getAttributes() as $property)
				$conditions['or']["`$property` LIKE ?"] = array('%'.$request['search'].'%');
		}
		#Filters
		elseif(isset($request['filter']) && $request['filter']) {
			$conditions['and'] = array();
			foreach($request['filter'] as $key=>$value)
				if($value)
					$conditions['and']["`$key` LIKE ?"] = array('%'.$value.'%');
		}
		
		$pagination = $_model::where($conditions);
		if(isset(static::$_orderby))
			$pagination->orderBy(static::$_orderby);
		list($this->$_models, $this->paginator) = $_model::where($conditions)->paginate(
			isset($request['page']) ? $request['page']:1
		);
	}
	
	/**
	@Route(':id/edit')
	*/
	public function editAction($request) {
		$_model = static::$_model;
		$modelName = strtolower(basename($_model));
		
		if(!($this->$modelName = $_model::load($request['id'])))
			$this->forward404();
	
		$this->form = $this->formConfigure($this->$modelName);
	
		if($this->form->isSent())
			try {
				$this->form->save();
				Flash::addSuccess(static::$_messages['modified']);
				if(isset($_POST['send']))
					Response::redirect('admin/'.static::$_index)->send();
			} catch(\Coxis\Core\Form\FormException $e) {
				Flash::addError($e->errors);
			}
		
		$this->view = 'form.php';
	}
	
	/**
	@Route('new')
	*/
	public function newAction($request) {
		$_model = static::$_model;
		$modelName = strtolower(basename($_model));
		
		$this->$modelName = new $_model;
	
		$this->form = $this->formConfigure($this->$modelName);
	
		if($this->form->isSent())
			try {
				$this->form->save();
				Flash::addSuccess(static::$_messages['created']);
				if(isset($_POST['send']))
					Response::redirect('admin/'.static::$_index)->send();
				else {
					Response::redirect('admin/'.static::$_index.'/'.$this->$modelName->id.'/edit')->send();
				}
			} catch(\Coxis\Core\Form\FormException $e) {
				Flash::addError($e->errors);
			}
		
		$this->view = 'form.php';
	}
	
	/**
	@Route(':id/delete')
	*/
	public function deleteAction($request) {
		$_model = static::$_model;
		
		!$_model::destroyOne($request['id']) ?
			Flash::addError(static::$_messages['unexisting']) :
			Flash::addSuccess(static::$_messages['deleted']);
			
		Response::redirect('admin/'.static::$_index)->send();
	}
	
	/**
	@Route(':id/deletefile/:file')
	*/
	public function deleteSingleFileAction($request) {
		$_model = static::$_model;
		
		if(!($this->$_model = $_model::load($request['id'])))
			$this->forward404();
			
		$this->$_model->deleteFile($request['file']);
		Flash::addSuccess('Fichier supprimé avec succès.');
		Response::redirect(static::getEditURL($this->$_model->id), false)->send();
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
			Flash::addSuccess('Fichier supprimé avec succès.');
			FileManager::unlink(_WEB_DIR_.'/'.$path);
		} catch(\Exception $e) {
			Flash::addError('Il y a eu une erreur avec le fichier');
		}
		
		try {
			Response::redirect($this->url_for('edit', array('id' => $model->id)), false)->send();
		} catch(\Exception $e) {
			Response::redirect($this->url_for('index'), false)->send();
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
		$modelName::init();#todo not generic (generic for models actually..)
		
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
}