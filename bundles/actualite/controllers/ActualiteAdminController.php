<?php
/**
@Prefix('admin/actualites')
*/
class ActualiteAdminController extends MultiAdminController {
	static $_model = 'actualite';
	static $_models = 'actualites';
	
	static $_messages = array(
			'modified'			=>	'Actualité modifiée avec succès.',
			'created'			=>	'Actualité créée avec succès.',
			'many_deleted'			=>	'Actualités supprimées avec succès.',
			'deleted'			=>	'Actualité supprimée avec succès.',
			'unexisting'			=>	'Cette actualité n\'existe pas.',
		);
	
	public function formConfigure($model) {
		$form = new AdminModelForm($model);
		
		return $form;
	}
}