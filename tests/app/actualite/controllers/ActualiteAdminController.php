<?php
namespace Tests\App\Actualite\Controllers;

/**
@Prefix('admin/actualites')
*/
class ActualiteAdminController extends \Coxis\Bundles\Admin\Libs\Controller\EntityAdminController {
	static $_entity = '\Coxis\App\Actualite\Entities\Actualite';
	static $_entities = 'actualites';
	
	static $_messages = array(
			'modified'			=>	'Actualité modifiée avec succès.',
			'created'			=>	'Actualité créée avec succès.',
			'many_deleted'			=>	'Actualités supprimées avec succès.',
			'deleted'			=>	'Actualité supprimée avec succès.',
			'unexisting'			=>	'Cette actualité n\'existe pas.',
		);
	
	public function formConfigure($entity) {
		$form = new \Coxis\Bundles\Admin\Libs\Form\AdminEntityForm($entity, $this);
		
		return $form;
	}
}