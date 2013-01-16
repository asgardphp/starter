<?php
namespace App\Admin\Controllers;

class DefaultAdminController extends \App\Admin\Libs\Controller\AdminParentController {
	public function configure() {
		return parent::configure();
	}
	
	/**
	@Route('admin')
	*/
	public function indexAction($request) {
	}
}