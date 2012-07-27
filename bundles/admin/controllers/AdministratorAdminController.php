<?php
namespace Coxis\Bundles\Admin\Controllers;

/**
@Prefix('admin/administrators')
*/
class AdministratorAdminController extends ModelAdminController {
	static $_model = 'administrator';
	static $_messages = array();
	
	public static function _autoload() {
		static::$__messages = array(
			'modified'			=>	__('Administrator updated with success.'),
			'created'				=>	__('Administrator created with success.'),
			'many_deleted'	=>	__('%s administrators deleted.'),
			'deleted'				=>	__('Administrator deleted with success.'),
			'unexisting'			=>	__('This administrator does not exist.'),
		);
	}
	
	public function formConfigure($model) {
		$form = new AdminModelForm($model, $this);
		$form->password->params['view']['value'] = '';
		
		return $form;
	}
}