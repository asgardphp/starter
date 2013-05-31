<?php
namespace Coxis\App\Admin\Controllers;

class LoginController extends \Coxis\App\Core\Controller {
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
		if(\POST::has('username'))
			$administrator = Administrator::where(array('username' => \POST::get('username'), 'password' => sha1(\Config::get('salt').\POST::get('password'))))->first();
		elseif(\Cookie::has('coxis_remember')) {
			$remember = \Cookie::get('coxis_remember');
			$administrator = Administrator::where(array('MD5(CONCAT(username, \'-\', password))' => $remember))->first();
		}
		
		if($administrator) {
			\Session::set('admin_id', $administrator->id);
			if(\POST::get('remember')=='yes')
				\Cookie::set('coxis_remember', md5($administrator->username.'-'.$administrator->password));
			if(\SESSION::has('redirect_to'))
				return \Response::redirect(\SESSION::get('redirect_to'), false);
			else
				return \Response::redirect('admin');
		}
		elseif(\POST::has('username'))
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