<?php
namespace App\Admin\Controllers;

/**
@Prefix('admin/administrators')
*/
class AdministratorAdminController extends \App\Admin\Libs\Controller\ModelAdminController {
	static $_model = 'administrator';
	static $_models = 'administrators';
	
	function __construct() {
		$this->_messages = array(
			'modified'			=>	__('Administrateur modifié avec succès.'),
			'created'				=>	__('Administrateur créé avec succès.'),
			'many_deleted'	=>	__('%s administrateurs supprimés.'),
			'deleted'				=>	__('Administrateur supprimé avec succès.'),
			'unexisting'			=>	__('Cet administrateur n\'existe pas.'),
		);
		parent::__construct();
	}
	
	public function formConfigure($model) {
		$form = new AdminModelForm($model, $this);
		$form->password->params['view']['value'] = '';
		
		return $form;
	}
}