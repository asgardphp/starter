<?php
/**
@Prefix('admin/choeurs')
*/
class ChoeurAdminController extends MultiAdminController {
	static $_model = 'choeur';
	static $_models = 'choeurs';
	
	static $_messages = array(
			'modified'			=>	'Choeur modifié avec succès.',
			'created'			=>	'Choeur créé avec succès.',
			'many_deleted'			=>	'Choeurs supprimés avec succès.',
			'deleted'			=>	'Choeur supprimé avec succès.',
			'unexisting'			=>	'Ce choeur n\'existe pas.',
		);
	
	public function formConfigure($model) {
		$form = new AdminModelForm($model);
		
		
		return $form;
	}
}