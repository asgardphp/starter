<?php
/**
@Prefix('admin/centrales')
*/
class CentraleAdminController extends \Coxis\Bundles\Admin\Libs\Controller\ModelAdminController {
	static $_model = 'centrale';
	static $_models = 'centrales';
	
	static $_messages = array(
			'modified'			=>	'Centrale modified with success.',
			'created'			=>	'Centrale created with success.',
			'many_deleted'			=>	'Centrales modified with success.',
			'deleted'			=>	'Centrale deleted with success.',
			'unexisting'			=>	'This centrale does not exist.',
		);
	
	public function formConfigure($model) {
		$form = new \Coxis\Bundles\Admin\Libs\Form\AdminModelForm($model, $this);
		
		return $form;
	}
}