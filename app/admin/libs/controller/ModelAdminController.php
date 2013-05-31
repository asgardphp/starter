<?php
namespace Coxis\App\Admin\Libs\Controller;

abstract class ModelAdminController extends AdminParentController {
	static $_model = null;#todo model variable name or model name ?
	static $_models = null;
	static $_index = null;
	protected static $_hooks = array();
	
	function __construct() {
		$this->__messages = array(
			'modified'			=>	__('Element updated with success.'),
			'created'				=>	__('Element created with success.'),
			'many_deleted'	=>	__('%s elements deleted.'),
			'deleted'				=>	__('Element deleted with success.'),
		);

		#trigger the model behaviors coxisadmin hook
		$model = static::$_model;
		$model_behaviors = $model::getDefinition()->behaviors();
		foreach($model_behaviors as $behavior => $params) {
			if($params)
				\Hook::trigger('behaviors_coxisadmin_'.$behavior, static::getControllerName());
		}

		if(static::$_models == null)
			static::$_models = basename(strtolower(static::$_model.'s'));
		if(isset($this->_messages))
			$this->_messages = array_merge($this->__messages, $this->_messages);
		else
			$this->_messages = $this->__messages;
		static::$_index = static::$_index ? static::$_index:basename(static::$_models);
	}
	
	public static function getModel() {
		return preg_replace('/^\\\/', '', static::$_model);
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
		
		$this->searchForm = new \Coxis\Form\Form(array('method'=>'get'));
		$this->searchForm->search = new \Coxis\Form\Fields\TextField;
	
		//submitted
		$controller = $this;
		$this->globalactions = array();
		\Hook::trigger('coxis_'.static::$_model.'_globalactions', array(&$this->globalactions), function($chain, &$actions) use($_model, $controller) {
			$actions[] = array(
				'text'	=>	__('Delete'),
				'value'	=>	'delete',
				'callback'	=>	function() use($_model, $controller) {
					$i = 0;
					if(POST::size()>1) {
						foreach(POST::get('id') as $id)
							$i += $_model::destroyOne($id);
					
						Flash::addSuccess(sprintf($controller->_messages['many_deleted'], $i));
					}
				}
			);
		});
		foreach($this->globalactions as $action) {
			if(POST::get('action') == $action['value']) {
				$cb = $action['callback'];
				$cb();
			}
		}
		
		$conditions = array();
		#todo with new orm
		#Search
		if(GET::get('search')) {
			$conditions['or'] = array();
			foreach($_model::propertyNames() as $property) {
				if($property != 'id')
					$conditions['or']["`$property` LIKE ?"] = '%'.GET::get('search').'%';
			}
		}
		#Filters
		elseif(GET::get('filter')) {
			$conditions['and'] = array();
			foreach(GET::get('filter') as $key=>$value) {
				if($value)
					$conditions['and']["`$key` LIKE ?"] = '%'.$value.'%';
			}
		}

		$pagination = $_model::where($conditions);
		
		if(isset(static::$_orderby))
			$pagination->orderBy(static::$_orderby);

		$this->orm = $pagination;

		\Hook::trigger('coxisadmin_'.static::$_model.'_index', array($this));

		$this->paginator = null;

		$this->$_models = $this->orm->paginate(
			GET::get('page', 1),
			10,
			$this->paginator
		);
	}
	
	/**
	@Route(':id/edit')
	*/
	public function editAction($request) {
		$_model = static::$_model;
		$modelName = strtolower(basename($_model));#todo namespace utils
		
		if(!($this->$modelName = $_model::load($request['id'])))
			throw new NotFoundException;
		$this->original = clone $this->$modelName;

		$this->form = $this->formConfigure($this->$modelName);
	
		if($this->form->isSent()) {
			try {
				$this->form->save();
				\Flash::addSuccess($this->_messages['modified']);
				if(\POST::has('send'))
					return Server::has('HTTP_REFERER') && Server::get('HTTP_REFERER') !== \URL::full() ? \Response::back():\Response::redirect($this->url_for('index'));
			} catch(\Coxis\Form\FormException $e) {
				\Flash::addError($this->form->getGeneralErrors());
				\Response::setCode(400);
			}
		}
		elseif(!$this->form->uploadSuccess()) {
			\Flash::addError(__('Data exceeds upload size limit. Maybe your file is too heavy.'));
			\Response::setCode(400);
		}
		
		$this->setRelativeView('form.php');
	}
	
	/**
	@Route('new')
	*/
	public function newAction($request) {
		$_model = static::$_model;
		$modelName = strtolower(basename($_model));#todo
		
		$this->$modelName = new $_model;
		$this->original = clone $this->$modelName;
	
		$this->form = $this->formConfigure($this->$modelName);
	
		#todo
		if($this->form->isSent()) {
			try {
				$this->form->save();
				\Flash::addSuccess($this->_messages['created']);
				if(\POST::has('send'))
					return Server::has('HTTP_REFERER') && Server::get('HTTP_REFERER') !== \URL::full() ? \Response::back():\Response::redirect($this->url_for('index'));
				else
					return \Response::redirect('admin/'.static::$_index.'/'.$this->$modelName->id.'/edit');
			} catch(\Coxis\Form\FormException $e) {
				\Flash::addError($this->form->getGeneralErrors());
				\Response::setCode(400);
			}
		}
		elseif(!$this->form->uploadSuccess()) {
			\Flash::addError(__('Data exceeds upload size limit. Maybe your file is too heavy.'));
			\Response::setCode(400);
		}
		
		$this->setRelativeView('form.php');
	}
	
	/**
	@Route(':id/delete')
	*/
	public function deleteAction($request) {
		$_model = static::$_model;
		
		!$_model::destroyOne($request['id']) ?
			\Flash::addError($this->_messages['unexisting']) :
			\Flash::addSuccess($this->_messages['deleted']);
			
		return \Response::redirect('admin/'.static::$_index);
	}
	
	/**
	@Route(':id/deletefile/:file')
	*/
	public function deleteSingleFileAction($request) {
		$_model = static::$_model;
		
		if(!($this->$_model = $_model::load($request['id'])))
			$this->forward404();
			
		$file = $request['file'];
		$this->$_model->$file->delete();
		\Flash::addSuccess(__('File deleted with success.'));
		return \Response::back();
	}
	
	/**
	@Route(':id/:file/add')
	*/
	public function addFileAction($request) {
		Memory::set('layout', false);
		$modelName = static::$_model;;
		if(!($model = $modelName::load($request['id'])))
			$this->forward404();
		if(!$model->hasProperty($request['file']))
			$this->forward404();
			
		if(\File::has('Filedata')) {
			$file = \File::get('Filedata');
			$files = array($request['file'] => array('name'=>$file['name'], 'path'=>$file['tmp_name']));
		}
		else
			return \Response::setCode(500)->setContent(__('An error occured.'));

		$file = $request['file'];
		$model->$file->add($files);
		$model->save(array(), true);
		$final_paths = $model->$file->get();
		$response = array(
			'url' => array_pop($final_paths),
			'deleteurl' => $this->url_for('deleteFile', array('id' => $model->id, 'pos' => sizeof($final_paths)+1, 'file' => $request['file'])),
		);
		return \Response::setCode(200)->setContent(json_encode($response));
	}
	
	/**
	@Route(':id/:file/delete/:pos')
	*/
	public function deleteFileAction($request) {
		$modelName = static::$_model;
		if(!($model = $modelName::load($request['id'])))
			$this->forward404();
		if(!$model->hasProperty($request['file']))
			$this->forward404();
		
		$file = $request['file'];
			
		$paths = $model->$file->get();

		if(!isset($paths[$request['pos']-1]))
			return \Response::redirect($this->url_for('edit', array('id' => $model->id)), false)->setCode(404);
		
		try {
			$model->$file->delete($request['pos']-1);
			$model->save(null, true);
			\Flash::addSuccess(__('File deleted with success.'));
		} catch(\Exception $e) {
			\Flash::addError(__('There was an error in the file'));
		}
		
		try {
			return \Response::redirect($this->url_for('edit', array('id' => $model->id)), false);
		} catch(\Exception $e) {
			return \Response::redirect($this->url_for('index'), false);
		}
	}
	
	public static function addHook($hook) {
		static::$_hooks[] = $hook;
		
		$hook['route'] = str_replace(':route', $hook['route'], \Router::getRouteFor(array(static::getControllerName(), 'hooks')));
		$hook['controller'] = static::getControllerName();
		$hook['action'] = 'hooks';
		\Router::addRoute($hook);
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
		import($modelName);
		
		$controller = static::getControllerName();

		#todo sort hooks routes
		foreach(static::$_hooks as $hook) {
			if($results = \Router::matchWith($hook['route'], $request['route'])) {
				$newRequest = new \Coxis\Core\Request;
				$newRequest->parentController = $controller;
				$newRequest->params = array_merge($request->params, $results);
				return Controller::run($hook['controller'], $hook['action'], $newRequest);
			}
		}
		throw new \Exception('Controller hook does not exist!');
	}
}
