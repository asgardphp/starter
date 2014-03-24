<?php
namespace App\General\Controllers;

class DefaultController extends \Asgard\Core\Controller {
	/**
	@Route('')
	*/
	public function indexAction($request) {
	}

	public function _404Action() {
	}
	
	public function layout($content) {
		$this->content = $content;
	}
}