<?php
/**
@Prefix('admin/participants')
*/
class ParticipantAdminController extends \Coxis\Bundles\Admin\Libs\Controller\ModelAdminController {
	static $_model = 'participant';
	static $_models = 'participants';
	
	static $_messages = array(
			'modified'			=>	'Participant modifié avec succès.',
			'created'			=>	'Participant créé avec succès.',
			'many_deleted'			=>	'Participants supprimés avec succès.',
			'deleted'			=>	'Participant supprimé avec succès.',
			'unexisting'			=>	'Ce participant n\'existe pas.',
		);
	
	public function formConfigure($model) {
		$form = new \Coxis\Bundles\Admin\Libs\Form\AdminModelForm($model, $this);
		unset($form->jeu);
		
		return $form;
	}
}