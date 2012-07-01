<?php
/**
@Prefix('admin/annonces')
*/
class AnnonceAdminController extends MultiAdminController {
	static $_model = 'annonce';
	static $_models = 'annonces';
	
	static $_messages = array(
			'modified'			=>	'Annonce modifiée avec succès.',
			'created'			=>	'Annonce créée avec succès.',
			'many_deleted'			=>	'Annonces supprimées avec succès.',
			'deleted'			=>	'Annonce supprimée avec succès.',
			'unexisting'			=>	'Cette annonce n\'existe pas.',
		);
	
	public function formConfigure($model) {
		$form = new AdminModelForm($model);
		
		return $form;
	}
}