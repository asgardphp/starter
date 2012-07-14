<?php
/**
@Prefix('admin/documents')
*/
class DocumentAdminController extends MultiAdminController {
	static $_model = 'document';
	static $_models = 'documents';
	
	static $_messages = array(
			'modified'			=>	'Document modifié avec succès.',
			'created'			=>	'Document créé avec succès.',
			'many_deleted'			=>	'Documents supprimés avec succès.',
			'deleted'			=>	'Document supprimé avec succès.',
			'unexisting'			=>	'Ce document n\'existe pas.',
		);
	
	public function formConfigure($model) {
		$form = new AdminModelForm($model);
		
		return $form;
	}
}