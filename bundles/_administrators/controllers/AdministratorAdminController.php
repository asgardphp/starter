<?php
/**
@Prefix('admin/administrators')
*/
class AdministratorAdminController extends MultiAdminController {
	public function configure($params=null) {
		parent::configure($params);
	}
	
	static $_model = 'administrator';
	static $_messages = array(
		'modified'			=>	'Administrateur mis à jour avec succès.',
		'created'				=>	'Administrateur créée avec succès.',
		'many_deleted'	=>	'%s administrateurs supprimés.',
		'deleted'				=>	'Administrateur supprimé avec succès.',
		'unexisting'			=>	'Cet administrateur n\'existe pas.',
	);
	
	public function formConfigure($model) {
		$form = new AdminForm($model);
		$form->password->params['view']['value'] = '';
		//todo add Editable to model attributes
		//todo validator check if var === '' || var === null
		//todo modelform, do not set if input was not displayed (but should still alert if input is required..)
		//todo need the right value when accessing a widget
		
		return $form;
	}
}