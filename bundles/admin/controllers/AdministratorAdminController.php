<?php
namespace Coxis\Bundles\Admin\Controllers;

/**
@Prefix('admin/administrators')
*/
class AdministratorAdminController extends MultiAdminController {
	public function configure($params=null) {
		parent::configure($params);
	}
	
	static $_model = 'administrator';
	static $_messages = array(
		'modified'			=>	'Administrateur mis à jour avec succès.',
		'created'				=>	'Administrateur créée avec succès.',
		'many_deleted'	=>	'%s administrateurs supprimés.',
		'deleted'				=>	'Administrateur supprimé avec succès.',
		'unexisting'			=>	'Cet administrateur n\'existe pas.',
	);
	
	public function formConfigure($model) {
		$form = new AdminModelForm($model);
		$form->password->params['view']['value'] = '';
		
		return $form;
	}
}