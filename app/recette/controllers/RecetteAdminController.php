<?php
/**
@Prefix('admin/recettes')
*/
class RecetteAdminController extends \Coxis\Bundles\Admin\Libs\Controller\ModelAdminController {
	static $_model = 'Recette';
	static $_models = 'recettes';
	
	static $_messages = array(
			'modified'			=>	'Recette modifiée avec succès.',
			'created'			=>	'Recette créée avec succès.',
			'many_deleted'			=>	'Recettes modifiées avec succès.',
			'deleted'			=>	'Recette supprimée avec succès.',
			'unexisting'			=>	'Cette recette n\'existe pas.',
		);
	
	public function formConfigure($model) {
		$form = new \Coxis\Bundles\Admin\Libs\Form\AdminModelForm($model, $this);
		
		return $form;
	}
}