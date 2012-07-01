<?php
/**
@Prefix('admin/pages')
*/
class PageAdminController extends MultiAdminController {
	//~ public function configure($params=null) {
		//~ parent::configure($params);
	//~ }
	
	static $_model = 'page';
	//~ static $_orderby = 'title';
	//~ static $_models = 'pages';
	static $_messages = array(
		'modified'			=>	'Page updated with success.',
		'created'				=>	'Page created with success.',
		'many_deleted'	=>	'%s pages deleted.',
		'deleted'				=>	'Page deleted with success.',
		'unexisting'			=>	'Page not existing.',
	);
	
	public function formConfigure($model) {
		$form = new AdminModelForm($model);
		if(_ENV_ != 'dev')
			unset($form->name);
		//~ unset($form->meta_description);
		//~ unset($form->meta_keywords);
		//~ unset($form->slug);
		//~ unset($form->created_at);
		//~ unset($form->updated_at);
		//~ $form->comments = array();
		//~ foreach($model->getComments() as $comment)
			//~ $form->comments[] = new AdminForm($comment);
		//~ d($form->comments[0]);
		//todo add Editable to model attributes
		//todo validator check if var === '' || var === null
		//todo modelform, do not set if input was not displayed (but should still alert if input is required..)
		//todo need the right value when accessing a widget
		
		//~ d($_FILES, $_POST, $form->logo);
		//~ d($form->logo);
		
		return $form;
	}
}