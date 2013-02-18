<?php
/**
@Prefix('admin/actualites')
*/
class ActualiteAdminController extends \App\Admin\Libs\Controller\ModelAdminController {
	static $_model = 'actualite';
	static $_models = 'actualites';

	function __construct() {
		$this->_messages = array(
			'modified'			=>	__('Actualité modifiée avec succès.'),
			'created'			=>	__('Actualité créée avec succès.'),
			'many_deleted'			=>	__('Actualités modifiéee avec succès.'),
			'deleted'			=>	__('Actualité supprimée avec succès.'),
			'unexisting'			=>	__('Cette actualité n\'existe pas.'),
		);
		parent::__construct();
	}
	
	public function formConfigure($model) {
		$form = new \App\Admin\Libs\Form\AdminModelForm($model, $this);
		
		return $form;
	}
}