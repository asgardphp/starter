<?php
namespace Coxis\App\Intranet\Controllers;

class IntranetController extends \Coxis\Core\Controller {
	/**
	@Route('register')
	*/
	public function registerAction($request) {
		$user = new User;
		$this->form = new ModelForm($user);
		if($this->form->isSent()) {
			try {
				$this->form->save();

				$txt = __('intranet__register_welcome', array(
					'username'	=>	$user->username,
					'password'	=>	$this->form->password->getValue(),
					'link'	=>	\URL::to('confirm/'.$user->getUID()),
				));
				Email::create($user->email, \Value::val('email'), 'Confirmation', $txt)->send();

				return '<p style="text-align:center">Congrats, you are now registered. Please check your emails to confirm your registration.</p>';
			} catch(\Coxis\Form\FormException $e) {
				\Flash::addError($e->errors);
			}
		}
	}

	/**
	@Route('confirm/:uid')
	*/
	public function confirmAction($request) {
		if(!$user = User::getByUID($request['uid']))
			$this->notfound();

		$user->save(array('confirmed'=>true));
		Auth::connect($user->id);
		return '<p style="text-align:center">'.__('Congrats, your registration is now confirmed.').'</p>';
	}

	public function loginForm() {
		$this->form = new Form(array('action'=>$this->url_for('login')));
		$this->form->username = new TextField;
		$this->form->password = new TextField;
		$this->form->remember = new BooleanField;
	}

	public function login_widget() {
		$this->loginForm();
	}

	/**
	@Route('login')
	*/
	public function loginAction($request) {
		if(Auth::isConnected())
			return \Response::redirect($this->url_for('index'));
		$this->loginForm();
		if($this->form->isSent()) {
			$user = Auth::attempt($this->form->username->getValue(), $this->form->password->getValue());
			if($user) {
				if($this->form->username->getValue())
					Auth::remember($user->id);
				return \Response::redirect($this->url_for('index'));
			}
			else
				\Flash::addError(__('Wrong username or password.'));
		}
	}

	/**
	@Route('forgotten')
	*/
	public function forgottenAction($request) {
		$this->form = new Form;
		$this->form->email = new TextField(array('validation'=>array(
			'required' => true,
			'email' => true,
			'user_exists' => function($attribute, $value) {
				if(!User::loadByEmail($value))
					return __('This email address does not belong to any user.');
			}
		)));

		if($this->form->isSent() && $this->form->isValid()) {
			$user = User::loadByEmail($this->form->email->getValue());
			Email::create($user->email, Value::val('email'), 'Password forgotten', __('Click here to get a new password: ', \URL::to($this->url_for('confirm_forgotten', array('uid'=>$user->getUID())))))->send();
		}
	}

	/**
	@Route('confirm_forgotten/:uid')
	*/
	public function confirm_forgottenAction($request) {
		if(!$user = User::getByUID($request['uid']))
			$this->notfound();
		$rand = Tools::randStr(10);
		$user->save(array('password'=>$rand));
		Email::create($user->email, Value::val('email'), __('New password'), __('Your new password: :password', array('password'=>$rand)))->send();
		return __('A new password was sent to your email address.');
	}

	/**
	@Route('logout')
	*/
	public function logoutAction($request) {
		Auth::disconnect();
		return __('You have been successfully disconnected!');
	}

	/**
	@Route('intranet')
	*/
	public function indexAction($request) {
		Auth::check();
	}

	/**
	@Route('profile')
	*/
	public function profileAction($request) {
		Auth::check();
		$this->form = new ModelForm(Auth::user());
		if($this->form->isSent()) {
			try {
				$this->form->save();
				\Flash::addSuccess(__('Your profile was saved with success!'));
			} catch(\Coxis\Form\FormException $e) {
				\Flash::addError($e->errors);
			}
		}
	}
}