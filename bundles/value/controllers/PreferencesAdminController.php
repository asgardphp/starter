<?php
namespace Coxis\Bundles\Value\Controllers;

/**
@Prefix('admin/preferences')
*/
class PreferencesAdminController extends \Coxis\Bundles\Admin\Libs\Controller\AdminParentController {
	static $_messages = array(
		'modified'			=>	'Préférences mises à jour avec succès.',
	);
	
	public function formConfigure() {
		$form = new AdminSimpleForm();
		
		$form->values = array();
		$vars = array('name', 'email', 'head_script');
		foreach($vars as $valueName) {
			$value = Value::fetch($valueName);
			$a = new AdminModelForm($value, $this);
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
				\Flash::addSuccess(static::$_messages['modified']);
				if(isset($_POST['send']))
					return \Response::redirect('admin/'.static::$_index, true);
			} catch(FormException $e) {
				\Flash::addError($e->errors);
			}
		
		$this->setRelativeView('form.php');
	}
}
?>