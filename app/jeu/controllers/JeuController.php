<?php
class JeuController extends Controller {
	public function configure($request) {
		if(!($this->jeu = Jeu::loadByAdresse($request['nom'])))
			$this->forward404();
		if($this->jeu->date_debut->timestamp > time()
			|| $this->jeu->date_fin->timestamp+3600*24 < time())
			Response::setCode(404)->send();
			
		$participant = new Participant();
		$participant->jeu_id = $this->jeu->id;
		$this->form = new ModelForm($participant);
		$this->form->accepter = new Widget(array(
			'validation'	=>	'required',
			'messages'	=>	array(
				'required'	=>	'Vous devez accepter le règlement du jeu.',
			),
		));
		$this->form->prev_email = new Widget();
		$this->form->code_barre = new Widget();
		$jeu = $this->jeu;
		$this->form->email->params['validation'] = array(
			'custom'	=>	function($attribute, $value, $params, $validator) use($jeu) {
				$data = $params[0];
				$prev_email = $data['email'];
				//~ $prev_email = $data['prev_email'];
				$code_barre = $data['code_barre'];
				
				//~ if(empty($prev_email) || empty($code_barre))
				if(empty($code_barre))
					return null;
				
				$count = Participant::where(array('email'=>$prev_email))->count();
				if($count == 0)
					return null;
				if(
					!in_array($code_barre, explode("\r\n", $jeu->codes_barres))
					|| $count > 0
				) {
					return 'Vous ne pouvez pas rejouer avec cette adresse et ce code barre.';
				}
			},
		);
		$this->form->email->params['messages'] = array(
			'unique'	=>	'Cette adresse email est déjà utilisée.',
		);
			
		Coxis::set('layout', false);
	}

	/**
	@Route(value=':nom', method="get")
	*/
	public function showAction($request) {
	}

	/**
	@Route(value=':nom', method="post")
	*/
	public function ajaxAction($request) {
		try {
			$this->form->save();
			Response::send();
		} catch(\Coxis\Core\Form\FormException $e) {
			Response::setCode(500)->setContent(implode("\n", $e->errors))->send();
		}
		$this->view = false;
	}
}