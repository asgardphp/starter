<?php
namespace Coxis\Bundles\Admin\Libs\Controller;

abstract class AdminParentController extends Controller {
	public function configure($request) {
		Coxis::set('layout', array('Admin', 'layout'));
		if(!User::get('admin_id')) {
			$_SESSION['redirect_to'] = URL::full();
			Response::setCode(401)->redirect('admin/login', true)->send();
		}
	}
}