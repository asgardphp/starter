<?php
class DefaultAdminController extends AdminParentController {
	public function configure($params=null) {
		parent::configure($params);
	}
	
	/**
	@Route('/admin')
	*/
	public function indexAction($params) {
	}
}