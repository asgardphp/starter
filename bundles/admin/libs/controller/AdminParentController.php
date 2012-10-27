<?php
namespace Coxis\Bundles\Admin\Libs\Controller;

abstract class AdminParentController extends \Coxis\Core\Controller {
	public function configure($request) {
		\Memory::set('layout', array('\Coxis\Bundles\Admin\Controllers\Admin', 'layout'));
		if(!\Session::get('admin_id')) {
			\Session::set('redirect_to', \URL::full());
			return \Response::setCode(401)->redirect('admin/login', true);
		}
	}
}