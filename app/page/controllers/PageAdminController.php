<?php
/**
@Prefix('admin/pages')
*/
class PageAdminController extends \Coxis\Admin\Libs\Controller\ModelAdminController {
	static $_model = 'page';
	static $_models = 'pages';

	function __construct() {
		$this->_messages = array(
					'modified'			=>	__('Page modifiée avec succès.'),
					'created'			=>	__('Page créée avec succès.'),
					'many_deleted'			=>	__('Pages modifiées avec succès.'),
					'deleted'			=>	__('Page supprimée avec succès.'),
					'unexisting'			=>	__('Cette page n\'existe pas.'),
				);
		parent::__construct();
	}
	
	public function formConfigure($model) {
		$form = new \Coxis\Admin\Libs\Form\AdminModelForm($model, $this);

		if(_ENV_ != 'dev')
			unset($this->form->name);

		return $form;
	}
}