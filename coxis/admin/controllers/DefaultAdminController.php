<?php
namespace Coxis\Admin\Controllers;

class DefaultAdminController extends \Coxis\Admin\Libs\Controller\AdminParentController {
	public function configure() {
		return parent::configure();
	}
	
	/**
	@Route('admin')
	*/
	public function indexAction($request) {
	}
}