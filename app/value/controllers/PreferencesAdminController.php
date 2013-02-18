<?php
namespace App\Value\Controllers;

/**
@Prefix('admin/preferences')
*/
class PreferencesAdminController extends \Coxis\Admin\Libs\Controller\AdminParentController {
	function __construct() {
		$this->_messages = array(
			'modified'			=>	__('Preferences modifiées avec succès.'),
		);
	}
	
	public function formConfigure() {
		$form = new AdminSimpleForm($this);
		
		$form->values = array();
		$vars = array('name', 'email', 'head_script', 'adresse', 'telephone');
		foreach($vars as $valueName) {
			$value = Value::fetch($valueName);
			$a = new AdminModelForm($value, $this);
			// $a = new ModelForm($value, $this); #todo replace AdminModelForm
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
				\Flash::addSuccess($this->_messages['modified']);
				if(POST::has('send'))
					return \Response::back();
			} catch(FormException $e) {}
		
		$this->setRelativeView('form.php');
	}
}
?>