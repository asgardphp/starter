<?php
/**
@Prefix('admin/professeurs')
*/
class ProfesseurAdminController extends MultiAdminController {
	static $_model = 'professeur';
	static $_models = 'professeurs';
	
	static $_messages = array(
			'modified'			=>	'Professeur modifié avec succès.',
			'created'			=>	'Professeur créé avec succès.',
			'many_deleted'			=>	'Professeurs supprimés avec succès.',
			'deleted'			=>	'Professeur supprimé avec succès.',
			'unexisting'			=>	'Cet professeur n\'existe pas.',
		);
	
	public function formConfigure($model) {
		$form = new AdminModelForm($model);
		
		return $form;
	}
}