<?php
namespace Tests\App\Actualite\Controllers;

/**
@Prefix('admin/actualites')
*/
class ActualiteAdminController extends \Coxis\Bundles\Admin\Libs\Controller\ModelAdminController {
	static $_model = '\Coxis\App\Actualite\Models\Actualite';
	static $_models = 'actualites';
	
	static $_messages = array(
			'modified'			=>	'Actualité modifiée avec succès.',
			'created'			=>	'Actualité créée avec succès.',
			'many_deleted'			=>	'Actualités supprimées avec succès.',
			'deleted'			=>	'Actualité supprimée avec succès.',
			'unexisting'			=>	'Cette actualité n\'existe pas.',
		);
	
	public function formConfigure($model) {
		$form = new \Coxis\Bundles\Admin\Libs\Form\AdminModelForm($model, $this);
		
		return $form;
	}
}