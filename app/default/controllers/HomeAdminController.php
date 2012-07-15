<?php
/**
@Prefix('admin/home_intro')
*/
class HomeAdminController extends AdminParentController {
	static $_messages = array(
			'modified'			=>	'Page modifiée avec succès.',
			'created'			=>	'Page créée avec succès.',
			'many_deleted'			=>	'Pages supprimées avec succès.',
			'deleted'			=>	'Page supprimée avec succès.',
			'unexisting'			=>	'Cette page n\'existe pas.',
		);
	
	/**
	@Route('')
	*/
	public function editAction($request) {
		$page = Page::loadByName('services');
		$pref = Preferences::findOne();
	
		$this->form = new AdminForm($page);
		$this->form->pref = new AdminForm($pref);
		unset($this->form->name);
		unset($this->form->pref->name);
		unset($this->form->pref->email);
		unset($this->form->pref->our_mission);
		unset($this->form->pref->contact_us);
		unset($this->form->pref->presentation);
	
		if($this->form->isSent())
			try {
				$this->form->save();
				Messenger::getInstance()->addSuccess(static::$_messages['modified']);
			}
			catch(FormException $e) {
				Messenger::getInstance()->addError($e->errors);
			}
	}
}