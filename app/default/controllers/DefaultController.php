<?php
class DefaultController extends Controller {
	/**
	@Route('')
	*/
	public function indexAction($request) {
		$this->canonical('');
	}
	
	public function layoutAction($content) {
		if(access(Router::getRequest(), 'format') != 'html')
			return $content;
		$this->content = $content;
		$this->view = 'layout.php';
	}
	
	public function sidebarAction($request) {
	}
	
	public function _404Action() {
	}
}