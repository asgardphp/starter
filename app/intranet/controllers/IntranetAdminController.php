<?php
/**
@Prefix('admin/users')
*/
class IntranetAdminController extends \App\Admin\Libs\Controller\ModelAdminController {
	static $_model = 'user';
	static $_models = 'users';

	function __construct() {
		$this->_messages = array(
			'modified'			=>	__('Utilisateur modifiée avec succès.'),
			'created'			=>	__('Utilisateur créée avec succès.'),
			'many_deleted'			=>	__('Utilisateurs modifiéee avec succès.'),
			'deleted'			=>	__('Utilisateur supprimée avec succès.'),
			'unexisting'			=>	__('Cet utilisateur n\'existe pas.'),
		);
		parent::__construct();
	}
	
	public function formConfigure($model) {
		$form = new \App\Admin\Libs\Form\AdminModelForm($model, $this);
		
		return $form;
	}
}