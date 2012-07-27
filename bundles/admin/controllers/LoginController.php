<?php
namespace Coxis\Bundles\Admin\Controllers;

class LoginController extends Controller {
	public function configure($request) {
		Coxis::set('layout', false);
	}
	
	/**
	@Route('admin/login')
	*/
	public function loginAction($request) {
		if(User::get('admin_id'))
			Response::redirect('admin')->send();
	
		$administrator = null;
		if(isset($_POST['username']))
			$administrator = Administrator::where(array('username' => $_POST['username'], 'password' => sha1(Config::get('salt').$_POST['password'])))->first();
		elseif(Cookie::exist('coxis_remember')) {
			$remember = Cookie::get('coxis_remember');
			$administrator = Administrator::where(array('MD5(CONCAT(username, \'-\', password))' => $remember))->first();
		}
		
		if($administrator) {
			User::set('admin_id', $administrator->id);
			if(isset($_POST['remember']) && $_POST['remember']=='yes') {
				Cookie::delete('coxis_remember');
				Cookie::set('coxis_remember', md5($administrator->username.'-'.$administrator->password));
			}
			if(isset($_SESSION['redirect_to']))
				Response::redirect($_SESSION['redirect_to'], false)->send();
			else
				Response::redirect('admin')->send();
		}
		elseif(isset($_POST['username']))
			Flash::addError(__('Invalid username or password.'));
	}
	
	/**
	@Route('admin/logout')
	*/
	public function logoutAction($request) {
		Cookie::delete('coxis_remember');
		User::delete('admin_id');
		Response::redirect('')->send();
	}
	
	/**
	@Route('admin/forgotten')
	*/
	public function forgottenAction($request) {
		Email::generate(
			Config::get('website', 'email'), 
			'Mot de pase oublié', 
			Config::get('website', 'email'), 
			__(
				'Your username/password: :credentials',
				array('credentials'=>Config::get('admin', 'username').'/'.Config::get('admin', 'password'))
			)
		)->send();
		
		Flash::addSuccess(__('Your username/password were sent to you by email.'));
		$this->useView('login.php');
	}
}