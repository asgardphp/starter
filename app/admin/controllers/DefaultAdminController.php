<?php
namespace Coxis\App\Admin\Controllers;

class DefaultAdminController extends \Coxis\App\Admin\Libs\Controller\AdminParentController {
	public function configure() {
		return parent::configure();
	}
	
	/**
	@Route('admin')
	*/
	public function indexAction($request) {
	}
}