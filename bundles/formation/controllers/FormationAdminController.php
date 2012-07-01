<?php
/**
@Prefix('admin/formations')
*/
class FormationAdminController extends MultiAdminController {
	static $_model = 'formation';
	static $_models = 'formations';
	
	static $_messages = array(
			'modified'			=>	'Formation modifiée avec succès.',
			'created'			=>	'Formation créée avec succès.',
			'many_deleted'			=>	'Formations supprimées avec succès.',
			'deleted'			=>	'Formation supprimée avec succès.',
			'unexisting'			=>	'Cette formation n\'existe pas.',
		);
	
	public function formConfigure($model) {
		$form = new AdminModelForm($model);
		
		return $form;
	}
}