<?php
abstract class AdminParentController extends Controller {
	static $_model = null;
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