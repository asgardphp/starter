<?php
class ArpaController extends Controller {
	/**
	@Route('arpa')
	*/
	public function arpa1Action($request) {
		$this->page = Page::loadByName('arpa');
		Metas::set($this->page);
	}
	
	/**
	@Route('etudes')
	*/
	public function arpa2Action($request) {
		$this->documents = Document::find();
	}
	
	/**
	@Route('partenaires')
	*/
	public function arpa3Action($request) {
		$this->page = Page::loadByName('partenaires');
		Metas::set($this->page);
	}
	
	public function configForm() {
		$this->form = new Form(
			'contactform', 
			array(
				'method'	=>	'post',
				'action'	=>	'contact/submit',
			),
			array(
				'souhaitez'		=>	
					new Widget(array(
						//~ 'default'			=>	'en',
						'choices'	=>	array(
							'renseignement'		=>	'Un renseignement',
							'adherer'	=>	'Adhérer à l\'ARPA',
						),
						'multiple'	=>	true,
						'rules'				=>	array(
							'required'		=>	true,
						),
					)),
				'nom'		=>
					new Widget(array(
						'rules'	=>	array(
							'required'	=>	true,
						)
					)),
				'prenom'		=>
					new Widget(array(
						'rules'	=>	array(
							'required'	=>	true,
						)
					)),
				'telephone'		=>
					new Widget(array(
						'rules'	=>	array(
							'required'	=>	true,
						)
					)),
				'email'		=>
					new Widget(array(
						'rules'	=>	array(
							'required'	=>	true,
							'regex'	=>	'/^[\w-]+(\.[\w-]+)*@([a-z0-9-]+(\.[a-z0-9-]+)*?\.[a-z]{2,6}|(\d{1,3}\.){3}\d{1,3})(:\d{4})?$/',
						)
					)),
				'question'		=>
					new Widget(array(
						'rules'	=>	array(
							//~ 'required'	=>	true,
							'length'	=>	600,
						)
					)),
			)
		);
	}
	
	/**
	@Route('contact')
	*/
	public function contactAction($request) {
		$this->configForm();
	}

	/**
	@Route('contact/submit')
	*/
	public function contactsubmitAction($request) {
		$this->configForm();
		if($this->form->isSent()) {
			if(!($errors = $this->form->errors())) {
				//~ d($this->form->getData());
				$data = $this->form->getData();
				$text = 'Souhaite : '.implode(', ', $data['souhaitez'])."\n".
				'Nom : '.$data['nom']."\n".
				'Prénom : '.$data['prenom']."\n".
				'Téléphone : '.$data['telephone']."\n".
				'E-mail : '.$data['email']."\n".
				'Question : '.$data['question']."\n";
				//~ d($text);
				Email::generate(MySettings::get('email'), 'Arpa : Contact', $data['email'], $text)->send();
				
				Response::setCode(200)->send();
			}
			else {
				foreach($errors as $err)
					echo $err."\n";
				Response::setCode(500)->send();
			}
		}
	}
}