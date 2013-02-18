<?php
/**
@Prefix('admin/faq')
*/
class QuestionAdminController extends \Coxis\Admin\Libs\Controller\ModelAdminController {
	static $_model = 'question';
	static $_models = 'questions';

	function __construct() {
		$this->_messages = array(
			'modified'			=>	__('Question modifiée avec succès.'),
			'created'			=>	__('Question créée avec succès.'),
			'many_deleted'			=>	__('Questions modifiées avec succès.'),
			'deleted'			=>	__('Question supprimée avec succès.'),
			'unexisting'			=>	__('Cette question n\'existe pas.'),
		);
		parent::__construct();
	}
	
	public function formConfigure($model) {
		$form = new \Coxis\Admin\Libs\Form\AdminModelForm($model, $this);
		
		return $form;
	}
}