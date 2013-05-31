<?php
/**
@Prefix('admin/actualites')
*/
class ActualiteAdminController extends \Coxis\App\Admin\Libs\Controller\ModelAdminController {
	static $_model = 'actualite';
	static $_models = 'actualites';

	function __construct() {
		$this->_messages = array(
			'modified'			=>	__('Actualite modified with success.'),
			'created'			=>	__('Actualite created with success.'),
			'many_deleted'			=>	__('Actualites modified with success.'),
			'deleted'			=>	__('Actualite deleted with success.'),
			'unexisting'			=>	__('This actualite does not exist.'),
		);
		parent::__construct();
	}
	
	public function formConfigure($model) {
		$form = new \Coxis\App\Admin\Libs\Form\AdminModelForm($model, $this);
		
		return $form;
	}
}