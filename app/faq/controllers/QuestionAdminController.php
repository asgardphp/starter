<?php
/**
@Prefix('admin/faq')
*/
class QuestionAdminController extends \Coxis\Admin\Libs\Controller\ModelAdminController {
	static $_model = 'question';
	static $_models = 'questions';

	function __construct() {
		$this->_messages = array(
			'modified'			=>	__('Question modified with success.'),
			'created'			=>	__('Question created with success.'),
			'many_deleted'			=>	__('Questions modified with success.'),
			'deleted'			=>	__('Question deleted with success.'),
			'unexisting'			=>	__('This question does not exist.'),
		);
		parent::__construct();
	}
	
	public function formConfigure($model) {
		$form = new \Coxis\Admin\Libs\Form\AdminModelForm($model, $this);
		
		return $form;
	}
}