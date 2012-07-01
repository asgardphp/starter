<?php
abstract class MultiAdminController extends AdminParentController {
	static $_orderby = null;
	
	/**
	@Route('')
	*/
	public function indexAction($request) {
		$_model = static::$_model;
		$_models = static::$_models;
		
		$this->searchForm = new Form();
		$this->searchForm->search = new Widget();
	
		//submitted
		$i = 0;
		if(count($_POST)>1 && isset($_POST['action']) && $_POST['action']=='delete') {
			foreach($_POST['id'] as $id)
				$i += $_model::destroyOne($id);
		
			Messenger::getInstance()->addSuccess(sprintf(static::$_messages['many_deleted'], $i));
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
		
		list($this->$_models, $this->paginator) = $_model::paginate(
			isset($request['page']) ? $request['page']:1,
			array(
				'conditions'	=>	$conditions,
				'order_by'	=>	isset(static::$_orderby) ? static::$_orderby:null
			)
		);
	}
	
	/**
	@Route(':id/edit')
	*/
	public function editAction($request) {
		$_model = static::$_model;
		
		if(!($this->$_model = $_model::load($request['id'])))
			$this->forward404();
			
		//~ d($this->$_model);
	
		$this->form = $this->formConfigure($this->$_model);
	
		if($this->form->isSent())
			try {
				$this->form->save();
				Messenger::getInstance()->addSuccess(static::$_messages['modified']);
				if(isset($_POST['send']))
					Response::redirect('admin/'.static::$_index)->send();
			}
			catch(FormException $e) {
				Messenger::getInstance()->addError($e->errors);
			}
		
		$this->view = 'form.php';
	}
	
	/**
	@Route('new')
	*/
	public function newAction($request) {
		$_model = static::$_model;
		
		$this->$_model = $_model::create();
	
		$this->form = $this->formConfigure($this->$_model);
	
		if($this->form->isSent())
			try {
				$this->form->save();
				Messenger::getInstance()->addSuccess(static::$_messages['created']);
				if(isset($_POST['send']))
					Response::redirect('admin/'.static::$_index)->send();
				else
					Response::redirect('admin/'.static::$_index.'/'.$this->$_model->id.'/edit')->send();
			}
			catch(FormException $e) {
				Messenger::getInstance()->addError($e->errors);
			}
		
		$this->view = 'form.php';
	}
	
	/**
	@Route(':id/delete')
	*/
	public function deleteAction($request) {
		$_model = static::$_model;
		
		!$_model::destroyOne($request['id']) ?
			Messenger::getInstance()->addError(static::$_messages['unexisting']) :
			Messenger::getInstance()->addSuccess(static::$_messages['deleted']);
			
		Response::redirect('admin/'.static::$_index)->send();
	}
}