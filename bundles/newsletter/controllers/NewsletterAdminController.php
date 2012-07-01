<?php
/**
@Prefix('admin/newsletter')
*/
class NewsletterAdminController extends AdminParentController {
	/**
	@Route('')
	*/
	public function indexAction($request) {
		//~ $this->form = new Form();
		$this->configForm();
	}
	
	public function configForm() {
		$this->form = new AdminSimpleForm(
			'newsletterform', 
			array(
				'method'	=>	'post',
				'action'	=>	'contact/submit',
			),
			array(
				'sujet'		=>	
					new Widget(array(
						'rules'	=>	array(
							'required'	=>	true,
						)
					)),
				'contenu'		=>
					new Widget(array(
						'rules'	=>	array(
							'required'	=>	true,
						)
					)),
			)
		);
		if($this->form->isSent()) {
			if(!($errors = $this->form->errors())) {
				$subject = $this->form->sujet->value;
				$html = $this->form->contenu->value;
				$inscrits = Inscrit::find();
				foreach($inscrits as $inscrit)
					Email::generate($inscrit->email, $subject, MySettings::get('email'), '', $html)->send();
				Messenger::getInstance()->addSuccess('Newsletter envoyée avec succès !');
			}
			else {
				Messenger::getInstance()->addError($errors);
			}
		}
	}
}