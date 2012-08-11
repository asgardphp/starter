<?php
/**
@Prefix('admin/mailings')
*/
class MailingAdminController extends \Coxis\Bundles\Admin\Libs\Controller\ModelAdminController {
	static $_model = 'mailing';
	static $_models = 'mailings';
	
	static $_messages = array(
			'modified'			=>	'Mailing modified with success.',
			'created'			=>	'Mailing created with success.',
			'many_deleted'			=>	'Mailings modified with success.',
			'deleted'			=>	'Mailing deleted with success.',
			'unexisting'			=>	'This mailing does not exist.',
		);
	
	public function formConfigure($model) {
		$form = new \Coxis\Bundles\Admin\Libs\Form\AdminModelForm($model, $this);
		
		return $form;
	}
}