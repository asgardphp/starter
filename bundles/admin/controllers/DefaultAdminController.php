<?php
namespace Coxis\Bundles\Admin\Controllers;

class DefaultAdminController extends \Coxis\Bundles\Admin\Libs\Controller\AdminParentController {
	public function configure($params=null) {
		parent::configure($params);
	}
	
	/**
	@Route('/admin')
	*/
	public function indexAction($params) {
	}
}