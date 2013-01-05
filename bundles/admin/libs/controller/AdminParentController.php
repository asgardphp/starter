<?php
namespace Coxis\Bundles\Admin\Libs\Controller;

abstract class AdminParentController extends \Coxis\Core\Controller {
	public function configure() {
		// Config::set('locale', 'en');
		\Memory::set('layout', array('\Coxis\Bundles\Admin\Controllers\AdminController', 'layout'));
		if(!\Coxis\Core\Inputs\Session::get('admin_id')) {
			\Coxis\Core\Inputs\Session::set('redirect_to', \URL::full());
			return \Response::setCode(401)->redirect('admin/login', true);
		}
	}
}