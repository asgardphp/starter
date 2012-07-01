<?php
class LoginController extends Controller {
	public function configure($request) {
		Coxis::set('layout', false);
	}
	
	/**
	@Route('admin/login')
	*/
	public function loginAction($request) {
		if(User::getId() && User::getRole()=='admin')
			Response::redirect('admin')->send();
	
		$administrator = null;
		if(isset($_POST['username']))
			$administrator = Administrator::query('SELECT * FROM %table% WHERE username=? AND password=?', array($_POST['username'], sha1(Config::get('salt').$_POST['password'])));
		elseif(Cookie::exist('coxis_remember')) {
			$remember = Cookie::get('coxis_remember');
			$administrator = Administrator::query('SELECT * FROM %table% WHERE MD5(CONCAT(username, \'-\', password))=?', array($remember));
		}
		
		if($administrator) {
			User::setId($administrator[0]->id);
			User::setRole('admin');
			if(isset($_POST['remember']) && $_POST['remember']=='yes') {
				Cookie::delete('coxis_remember');
				Cookie::set('coxis_remember', md5($administrator[0]->username.'-'.$administrator[0]->password));
			}
			if(isset($_SESSION['redirect_to'])) {
				Response::redirect($_SESSION['redirect_to'], false)->send();
				unset($_SESSION['redirect_to']);
			}
			else
				Response::redirect('admin')->send();
		}
		elseif(isset($_POST['username'])) {
			Messenger::getInstance()->addError('Utilisateur ou mot de passe invalide.');
		}
	}
	
	/**
	@Route('admin/logout')
	*/
	public function logoutAction($request) {
		Cookie::delete('coxis_remember');
		User::logout();
		Response::redirect('')->send();
	}
	
	/**
	@Route('admin/forgotten')
	*/
	public function forgottenAction($request) {
		Email::generate(Config::get('website', 'email'), 'Mot de pase oublié', Config::get('website', 'email'), 'Votre identifiant/mot de passe : '.Config::get('admin', 'username').'/'.Config::get('admin', 'password'))->send();
		
		Messenger::getInstance()->addSuccess('Votre identifiant/mot de passe vous a été envoyé par mail.');
		$this->useView('login.php');
	}
}