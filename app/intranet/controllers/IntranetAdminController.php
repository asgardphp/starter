<?php
/**
@Prefix('admin/users')
*/
class IntranetAdminController extends \Coxis\App\Admin\Libs\Controller\ModelAdminController {
	static $_model = 'user';
	static $_models = 'users';

	function __construct() {
		$this->_messages = array(
			'modified'			=>	__('User modified with success.'),
			'created'			=>	__('User created with success.'),
			'many_deleted'			=>	__('Users modified with success.'),
			'deleted'			=>	__('User deleted with success.'),
			'unexisting'			=>	__('This user does not exist.'),
		);
		parent::__construct();
	}
	
	public function formConfigure($model) {
		$form = new \Coxis\App\Admin\Libs\Form\AdminModelForm($model, $this);
		
		return $form;
	}
}