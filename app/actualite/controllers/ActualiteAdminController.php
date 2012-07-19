<?php
/**
@Prefix('admin/actualites')
*/
class ActualiteAdminController extends MultiAdminController {
	static $_model = 'actualite';#todo\Coxis\App\Actualite\Models\Actualite
	static $_models = 'actualites';
	
	static $_messages = array(
			'modified'			=>	'Actualité modifiée avec succès.',
			'created'			=>	'Actualité créée avec succès.',
			'many_deleted'			=>	'Actualités supprimées avec succès.',
			'deleted'			=>	'Actualité supprimée avec succès.',
			'unexisting'			=>	'Cette actualité n\'existe pas.',
		);
	
	public function formConfigure($model) {
		$form = new \Coxis\Bundles\Admin\Libs\Form\AdminModelForm($model);
		
		return $form;
	}
}