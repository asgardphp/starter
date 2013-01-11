<?php
namespace Coxis\Admin\Libs\Controller;

abstract class AdminParentController extends \Coxis\Core\Controller {
	public function configure() {
		// Config::set('locale', 'en');
		\Memory::set('layout', array('\Coxis\Admin\Controllers\AdminController', 'layout'));
		if(!\Session::get('admin_id')) {
			\Session::set('redirect_to', \URL::full());
			return \Response::setCode(401)->redirect('admin/login', true);
		}
	}
}