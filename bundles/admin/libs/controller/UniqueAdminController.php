<?php
abstract class UniqueAdminController extends AdminParentController {
	/**
	@Route('')
	*/
	public function editAction($request) {
		$_model = static::$_model;
		
		if(!($this->$_model = $_model::load(1)))
			$this->forward404();
	
		$this->form = $this->formConfigure($this->$_model);
	
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