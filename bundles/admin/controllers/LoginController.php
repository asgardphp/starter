<?php
namespace Coxis\Bundles\Admin\Controllers;

class LoginController extends \Coxis\Core\Controller {
	public function configure() {
		\Memory::set('layout', false);
	}
	
	/**
	@Route('admin/login')
	*/
	public function loginAction($request) {
		if(\Session::get('admin_id'))
			return \Response::redirect('admin');
	
		$administrator = null;
		if(isset($_POST['username']))
			$administrator = Administrator::where(array('username' => $_POST['username'], 'password' => sha1(\Config::get('salt').$_POST['password'])))->first();
		elseif(\Cookie::has('coxis_remember')) {
			$remember = \Cookie::get('coxis_remember');
			$administrator = Administrator::where(array('MD5(CONCAT(username, \'-\', password))' => $remember))->first();
		}
		
		if($administrator) {
			\Session::set('admin_id', $administrator->id);
			if(isset($_POST['remember']) && $_POST['remember']=='yes')
				\Cookie::set('coxis_remember', md5($administrator->username.'-'.$administrator->password));
			if(isset($_SESSION['redirect_to']))
				return \Response::redirect($_SESSION['redirect_to'], false);
			else
				return \Response::redirect('admin');
		}
		elseif(isset($_POST['username']))
			\Flash::addError(__('Invalid username or password.'));
	}
	
	/**
	@Route('admin/logout')
	*/
	public function logoutAction($request) {
		\Cookie::remove('coxis_remember');
		\Session::remove('admin_id');
		return \Response::redirect('');
	}
	
	/**
	@Route('admin/forgotten')
	*/
	public function forgottenAction($request) {
		#todo
		Email::generate(
			Config::get('website', 'email'), 
			'Mot de pase oublié', 
			Config::get('website', 'email'), 
			__(
				'Your username/password: :credentials',
				array('credentials'=>Config::get('admin', 'username').'/'.Config::get('admin', 'password'))
			)
		)->send();
		
		\Flash::addSuccess(__('Your username/password were sent to you by email.'));
		$this->setRelativeView('login.php');
	}
}