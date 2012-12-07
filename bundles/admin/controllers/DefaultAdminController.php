<?php
namespace Coxis\Bundles\Admin\Controllers;

class DefaultAdminController extends \Coxis\Bundles\Admin\Libs\Controller\AdminParentController {
	public function configure() {
		return parent::configure();
	}
	
	/**
	@Route('admin')
	*/
	public function indexAction($request) {
	}
}