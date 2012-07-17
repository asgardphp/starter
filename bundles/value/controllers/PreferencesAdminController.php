<?php
namespace Coxis\Bundles\Value\Controllers;

/**
@Prefix('admin/preferences')
*/
class PreferencesAdminController extends AdminParentController {
	public function configure($params=null) {
		parent::configure($params);
	}
	
	static $_model = 'preferences';
	static $_messages = array(
		'modified'			=>	'Préférences mises à jour avec succès.',
	);
	
	public function formConfigure() {
		$form = new AdminSimpleForm();
		
		$form->values = array();
		$vars = array('name', 'email', 'head_script');
		//~ foreach($values as $value) {
		foreach($vars as $valueName) {
			$value = Value::get($valueName);
			$a = new AdminModelForm($value);
			unset($a->key);
			$form->values[$value->key] = $a;
		}
		
		$form->values['name']['value']->params['rules']['required'] = true;
		$form->values['name']['value']->params['messages']['required'] = 'Le champ "nom" est requis.';
		
		return $form;
	}
	
	/**
	@Route('')
	*/
	public function editAction($request) {
		$this->form = $this->formConfigure();
	
		if($this->form->isSent())
			try {
				$this->form->save();
				Messenger::getInstance()->addSuccess(static::$_messages['modified']);
				if(isset($_POST['send']))
					$this->redirect('admin/'.static::$_index, true)->send();
			} catch(FormException $e) {
				Messenger::getInstance()->addError($e->errors);
			}
		
		$this->view = 'form.php';
	}
}
?>