<?php
/**
@Prefix('admin/inscrits')
*/
class InscritAdminController extends AdminModelForm {
	static $_model = 'inscrit';
	static $_models = 'inscrits';
	
	static $_messages = array(
			'modified'			=>	'Inscrit modifié avec succès.',
			'created'			=>	'Inscrit créé avec succès.',
			'many_deleted'			=>	'Inscrits supprimés avec succès.',
			'deleted'			=>	'Inscrit supprimé avec succès.',
			'unexisting'			=>	'Cet inscrit n\'existe pas.',
		);
	
	public function formConfigure($model) {
		$form = new AdminModelForm($model);
		
		return $form;
	}
}