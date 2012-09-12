<?php
/**
@Prefix('admin/morceaux')
*/
class MorceauAdminController extends \Coxis\Bundles\Admin\Libs\Controller\ModelAdminController {
	static $_model = 'Morceau';
	static $_models = 'morceaux';

	static $_messages = array(
			'modified'			=>	'Morceau modifié avec succès.',
			'created'			=>	'Morceau créé avec succès.',
			'many_deleted'			=>	'Morceaux modifiés avec succès.',
			'deleted'			=>	'Morceau supprimé avec succès.',
			'unexisting'			=>	'Ce morceau n\'existe pas.',
		);
	
	public function formConfigure($model) {
		$form = new \Coxis\Bundles\Admin\Libs\Form\AdminModelForm($model, $this);
		
		return $form;
	}
}