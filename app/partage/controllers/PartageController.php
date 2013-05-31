<?php
/**
@Prefix('partage')
*/
class PartageController extends Controller {
	/**
	@Route('mail/:id')
	*/
	public function mailAction($request) {
		\Memory::set('layout', false);

		$this->form = new Form;
		$this->form->from = new TextField(array('validation'=>array('email', 'required')));
		$this->form->to = new TextField(array('validation'=>array('email', 'required')));
		$this->form->message = new TextField(array('validation'=>'required'));

		$this->envoye = false;

		if($this->form->isSent()) {
			if($this->form->isValid()) {
				$txt = 'Bonjour,
Une personne souhaite partager avec : _url_ 

Voici son message : '.$this->form->message->getValue();
				Email::create($this->form->to->getValue(), $this->form->from->getValue(), 'Partage', $txt)->send();
				$this->envoye = true;
			}
			else {
				\Flash::addError(Tools::flateArray($this->form->errors()));
			}
		}
	}
}